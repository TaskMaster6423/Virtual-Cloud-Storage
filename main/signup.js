document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('signup-form');
    const steps = document.querySelectorAll('.form-step');
    const nextBtn = document.querySelector('.next-step');
    const prevBtn = document.querySelector('.prev-step');
    const progressCircles = document.querySelectorAll('.step-circle');

    let currentStep = 0;

    function updateProgress() {
        progressCircles.forEach((circle, index) => {
            circle.classList.toggle('active', index <= currentStep);
        });
    }

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.remove('active');
            if (index === stepIndex) {
                step.classList.add('active');
            }
        });
        updateProgress();

        if (currentStep === 0) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'inline-block';
        } else if (currentStep === steps.length - 1) {
            nextBtn.style.display = 'none';
            prevBtn.style.display = 'inline-block';
        } else {
            prevBtn.style.display = 'inline-block';
            nextBtn.style.display = 'inline-block';
        }
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    showStep(currentStep); // Show the first step initially

    // Basic form validation (you'll need more robust validation)
    form.addEventListener('submit', (event) => {
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm-password');
        let isValid = true;

        if (usernameInput.value.trim() === '') {
            displayValidationError(usernameInput, 'Username is required.');
            isValid = false;
        } else {
            clearValidationError(usernameInput);
        }

        if (!emailInput.value.includes('@')) {
            displayValidationError(emailInput, 'Please enter a valid email.');
            isValid = false;
        } else {
            clearValidationError(emailInput);
        }

        if (passwordInput.value.length < 6) {
            displayValidationError(passwordInput, 'Password must be at least 6 characters.');
            isValid = false;
        } else {
            clearValidationError(passwordInput);
        }

        if (passwordInput.value !== confirmPasswordInput.value) {
            displayValidationError(confirmPasswordInput, 'Passwords do not match.');
            isValid = false;
        } else {
            clearValidationError(confirmPasswordInput);
        }

        if (!isValid) {
            event.preventDefault();
        } else {
            // In a real application, you would submit the form data to your backend here.
            alert('Account created successfully! (Simulated)');
        }
    });

    function displayValidationError(inputElement, message) {
        const validationMessage = inputElement.nextElementSibling.nextElementSibling; // Get the span for validation
        validationMessage.textContent = message;
    }

    function clearValidationError(inputElement) {
        const validationMessage = inputElement.nextElementSibling.nextElementSibling;
        validationMessage.textContent = '';
    }
});