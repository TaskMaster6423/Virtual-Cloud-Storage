import os
import pandas as pd
import numpy as np
import librosa
import tensorflow as tf
from tensorflow.keras import layers, models, callbacks
from sklearn.model_selection import train_test_split
import random
from tqdm import tqdm

# Configuration for Mozilla Common Voice dataset structure
config = {
    'sample_rate': 16000,
    'max_audio_length': 5,          # seconds
    'max_text_length': 100,         # max characters in transcript
    'batch_size': 32,
    'epochs': 30,
    'lstm_units': 256,
    'num_mfcc': 40,                 # number of MFCC features
    'data_path': 'C:/Users/ANURAG/Desktop/am/cv-corpus-21.0-delta-2025-03-14/en',  # path to dataset
    'train_csv': 'validated.tsv',   # main training data
    'dev_csv': 'dev.tsv',           # validation data
    'test_csv': 'test.tsv',         # test data
    'min_duration': 1.0,            # minimum audio duration (seconds)
    'max_duration': 5.0,            # maximum audio duration (seconds)
    'clips_dir': 'clips',           # directory containing audio files
    'audio_extensions': ['.mp3', '.wav', '.ogg']  # supported audio formats
}

class AudioProcessor:
    """Handles audio feature extraction and preprocessing"""
    
    def __init__(self):
        # Calculate maximum samples based on audio length
        self.max_samples = int(config['sample_rate'] * config['max_audio_length'])
    
    def load_audio(self, filepath):
        """Load audio file with error handling"""
        try:
            audio, sr = librosa.load(filepath, sr=config['sample_rate'])
            return audio, sr
        except Exception as e:
            print(f"Error loading {filepath}: {str(e)}")
            return None, None
    
    def preprocess_audio(self, audio):
        """Process raw audio into clean format"""
        # Trim silence from beginning and end
        audio, _ = librosa.effects.trim(audio, top_db=20)
        
        # Pad or truncate to fixed length
        if len(audio) > self.max_samples:
            audio = audio[:self.max_samples]
        else:
            audio = np.pad(audio, (0, max(0, self.max_samples - len(audio))), 
                          mode='constant')
        return audio
    
    def extract_features(self, audio):
        """Extract MFCC features with deltas"""
        mfcc = librosa.feature.mfcc(
            y=audio,
            sr=config['sample_rate'],
            n_mfcc=config['num_mfcc'],
            n_mels=128,
            hop_length=160,
            n_fft=512
        )
        # Add delta and delta-delta features
        delta = librosa.feature.delta(mfcc)
        delta2 = librosa.feature.delta(mfcc, order=2)
        return np.concatenate([mfcc, delta, delta2], axis=0).T  # (time, features)

class CommonVoiceDataset:
    """Handles loading and preprocessing of Common Voice data"""
    
    def __init__(self):
        self.audio_processor = AudioProcessor()
    
    def load_metadata(self, csv_file):
        """Load dataset metadata from TSV file"""
        filepath = os.path.join(config['data_path'], csv_file)
        if not os.path.exists(filepath):
            raise FileNotFoundError(f"Dataset file not found: {filepath}")
        
        df = pd.read_csv(filepath, sep='\t')
        
        # Filter by duration and valid audio extensions
        valid_ext = tuple(config['audio_extensions'])
        df = df[
            (df['duration'] >= config['min_duration']) &
            (df['duration'] <= config['max_duration']) &
            (df['path'].str.lower().str.endswith(valid_ext))
        ]
        
        return df[['path', 'sentence', 'client_id', 'age', 'gender']]
    
    def process_audio_file(self, audio_path):
        """Process single audio file"""
        full_path = os.path.join(config['data_path'], config['clips_dir'], audio_path)
        audio, sr = self.audio_processor.load_audio(full_path)
        if audio is None:
            return None
        
        audio = self.audio_processor.preprocess_audio(audio)
        features = self.audio_processor.extract_features(audio)
        return features
    
    def prepare_dataset(self, csv_file, max_samples=None):
        """Prepare complete dataset with features and labels"""
        metadata = self.load_metadata(csv_file)
        if max_samples:
            metadata = metadata[:max_samples]
        
        features, texts, speaker_ids = [], [], []
        for _, row in tqdm(metadata.iterrows(), desc=f"Processing {csv_file}"):
            feat = self.process_audio_file(row['path'])
            if feat is not None:
                features.append(feat)
                texts.append(row['sentence'].lower().strip())
                speaker_ids.append(row['client_id'])
        
        # Pad all sequences to same length
        max_length = max(f.shape[0] for f in features) if features else 0
        features_padded = np.zeros((len(features), max_length, config['num_mfcc'] * 3))
        for i, f in enumerate(features):
            features_padded[i, :f.shape[0]] = f
        
        return features_padded, np.array(texts), np.array(speaker_ids)

class TextProcessor:
    """Handles text processing and character encoding"""
    
    @staticmethod
    def create_vocab(texts):
        """Create character vocabulary from all texts"""
        chars = set()
        for text in texts:
            chars.update(text)
        return sorted(list(chars))
    
    @staticmethod
    def encode_text(texts, char_to_num, max_length=None):
        """Encode texts to numerical sequences"""
        if not max_length:
            max_length = max(len(t) for t in texts)
        
        encoded = np.zeros((len(texts), max_length), dtype=np.int32)
        for i, text in enumerate(texts):
            for j, char in enumerate(text[:max_length]):
                if char in char_to_num:
                    encoded[i, j] = char_to_num[char]
        return encoded

class CTCLayer(layers.Layer):
    """Custom CTC loss layer with proper shape handling"""
    
    def __init__(self, name=None):
        super().__init__(name=name)
        self.loss_fn = tf.keras.backend.ctc_batch_cost
    
    def call(self, inputs):
        """Compute CTC loss during training"""
        y_true, y_pred, input_length, label_length = inputs
        
        # Convert and validate input shapes
        input_length = tf.cast(tf.squeeze(input_length, -1), tf.int32)
        label_length = tf.cast(tf.squeeze(label_length, -1), tf.int32)
        
        # Compute CTC loss
        loss = self.loss_fn(y_true, y_pred, input_length, label_length)
        self.add_loss(tf.reduce_mean(loss))
        
        return y_pred  # Return predictions unchanged
    
    def compute_output_shape(self, input_shapes):
        """Specify output shape for TensorFlow"""
        return input_shapes[1]

class SpeechRecognitionModel:
    """End-to-end speech recognition model"""
    
    def __init__(self, input_shape, num_chars):
        self.model = self._build_model(input_shape, num_chars)
    
    def _build_model(self, input_shape, num_chars):
        """Construct the neural network architecture"""
        # Input layers
        audio_input = layers.Input(shape=input_shape, name='audio_input')
        labels = layers.Input(name='labels', shape=[None], dtype='int32')
        input_length = layers.Input(name='input_length', shape=[1], dtype='int32')
        label_length = layers.Input(name='label_length', shape=[1], dtype='int32')
        
        # CNN feature extraction
        x = layers.Conv1D(64, 11, strides=2, padding='same', activation='relu')(audio_input)
        x = layers.BatchNormalization()(x)
        x = layers.Dropout(0.2)(x)
        
        x = layers.Conv1D(128, 7, strides=1, padding='same', activation='relu')(x)
        x = layers.BatchNormalization()(x)
        x = layers.Dropout(0.2)(x)
        
        # Bidirectional LSTM layers
        x = layers.Bidirectional(layers.LSTM(
            config['lstm_units'],
            return_sequences=True,
            dropout=0.2,
            recurrent_dropout=0.2
        ))(x)
        x = layers.Bidirectional(layers.LSTM(
            config['lstm_units'],
            return_sequences=True,
            dropout=0.2,
            recurrent_dropout=0.2
        ))(x)
        
        # Output layer
        outputs = layers.Dense(num_chars + 1, activation='softmax')(x)
        
        # CTC layer
        ctc_output = CTCLayer(name='ctc_loss')([labels, outputs, input_length, label_length])
        
        # Create complete model
        model = tf.keras.Model(
            inputs=[audio_input, labels, input_length, label_length],
            outputs=ctc_output
        )
        
        return model
    
    def compile_model(self):
        """Configure model for training"""
        self.model.compile(optimizer=tf.keras.optimizers.Adam(0.001))
    
    def train(self, X_train, y_train, X_val, y_val, char_to_num):
        """Train the model with validation"""
        # Calculate sequence lengths
        input_length_train = np.array([[X_train.shape[1]]] * len(X_train))
        input_length_val = np.array([[X_val.shape[1]]] * len(X_val))
        
        # Calculate label lengths
        y_train_len = np.array([[len(txt)] for txt in y_train])
        y_val_len = np.array([[len(txt)] for txt in y_val])
        
        # Encode texts
        y_train_enc = TextProcessor.encode_text(y_train, char_to_num, config['max_text_length'])
        y_val_enc = TextProcessor.encode_text(y_val, char_to_num, config['max_text_length'])
        
        # Define callbacks
        callbacks_list = [
            callbacks.EarlyStopping(patience=5, restore_best_weights=True),
            callbacks.ModelCheckpoint(
                'best_model.h5',
                save_best_only=True,
                monitor='val_loss',
                mode='min'
            ),
            callbacks.ReduceLROnPlateau(
                monitor='val_loss',
                factor=0.5,
                patience=3,
                min_lr=1e-6,
                verbose=1
            )
        ]
        
        # Train model
        history = self.model.fit(
            x={
                'audio_input': X_train,
                'labels': y_train_enc,
                'input_length': input_length_train,
                'label_length': y_train_len
            },
            y=np.zeros(len(X_train)),  # Dummy output
            validation_data=(
                {
                    'audio_input': X_val,
                    'labels': y_val_enc,
                    'input_length': input_length_val,
                    'label_length': y_val_len
                },
                np.zeros(len(X_val))
            ),
            batch_size=config['batch_size'],
            epochs=config['epochs'],
            callbacks=callbacks_list,
            verbose=1
        )
        
        return history

def main():
    """Main execution function"""
    # Initialize dataset loader
    dataset = CommonVoiceDataset()
    
    # Load training data
    print("Loading training data...")
    X_train, y_train, _ = dataset.prepare_dataset(config['train_csv'], max_samples=5000)
    
    # Load or create validation data
    if os.path.exists(os.path.join(config['data_path'], config['dev_csv'])):
        print("Loading validation data...")
        X_val, y_val, _ = dataset.prepare_dataset(config['dev_csv'], max_samples=1000)
    else:
        print("Splitting training data for validation...")
        X_train, X_val, y_train, y_val = train_test_split(
            X_train, y_train, test_size=0.2, random_state=42
        )
    
    # Create vocabulary and character mappings
    print("Creating vocabulary...")
    vocab = TextProcessor.create_vocab(np.concatenate([y_train, y_val]))
    char_to_num = {c: i+1 for i, c in enumerate(vocab)}  # 0 reserved for padding
    num_to_char = {i+1: c for i, c in enumerate(vocab)}
    
    # Initialize and train model
    print("Building model...")
    model = SpeechRecognitionModel((X_train.shape[1], X_train.shape[2]), len(char_to_num))
    model.compile_model()
    
    print("Starting training...")
    history = model.train(X_train, y_train, X_val, y_val, char_to_num)
    
    # Save model and vocabulary
    print("Saving model and vocabulary...")
    model.model.save('common_voice_speech_recognition.h5')
    np.save('char_to_num.npy', char_to_num)
    np.save('num_to_char.npy', num_to_char)
    
    print("Training complete!")

if __name__ == "__main__":
    main()