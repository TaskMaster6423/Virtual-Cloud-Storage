/* dashboard.js */
document.addEventListener('DOMContentLoaded', () => {
    const fileConstellationContainer = document.getElementById('file-constellation-container');
    const gridViewButton = document.querySelector('.grid-view');
    const listViewButton = document.querySelector('.list-view');
    const storageBar = document.querySelector('.storage-bar');
    const uploadButton = document.querySelector('.upload-button');
    const uploadProgressModal = document.getElementById('upload-progress-modal');
    const closeUploadModalButton = uploadProgressModal.querySelector('.close-button');
    const uploadProgressBar = document.getElementById('upload-progress-bar');
    const uploadProgressText = document.getElementById('upload-progress-text');
    const fileNodes = document.querySelectorAll('.file-node');

    let isGridView = true;

    // Initial load animation
    if (fileConstellationContainer) {
        setTimeout(() => {
            fileConstellationContainer.classList.add('show');
        }, 300);
    }

    // View Switch Functionality with Animation
    if (gridViewButton && listViewButton && fileConstellationContainer) {
        gridViewButton.addEventListener('click', () => {
            if (!isGridView) {
                fileConstellationContainer.classList.add('fade-out');
                setTimeout(() => {
                    fileConstellationContainer.classList.remove('list', 'fade-out');
                    fileConstellationContainer.classList.add('grid', 'fade-in');
                    gridViewButton.classList.add('active');
                    listViewButton.classList.remove('active');
                    isGridView = true;
                    setTimeout(() => fileConstellationContainer.classList.remove('fade-in'), 300);
                }, 300);
            }
        });

        listViewButton.addEventListener('click', () => {
            if (isGridView) {
                fileConstellationContainer.classList.add('fade-out');
                setTimeout(() => {
                    fileConstellationContainer.classList.remove('grid', 'fade-out');
                    fileConstellationContainer.classList.add('list', 'fade-in');
                    listViewButton.classList.add('active');
                    gridViewButton.classList.remove('active');
                    isGridView = false;
                    setTimeout(() => fileConstellationContainer.classList.remove('fade-in'), 300);
                }, 300);
            }
        });
    }

    // Storage Bar Update (Simulated)
    function updateStorage(percentage) {
        if (storageBar) {
            storageBar.style.width = `${percentage}%`;
        }
    }
    setTimeout(() => updateStorage(75), 2500);

    // Drag and Drop Functionality
    let draggedItem = null;

    fileNodes.forEach(node => {
        node.addEventListener('dragstart', (event) => {
            draggedItem = event.target;
            event.target.classList.add('dragging');
        });

        node.addEventListener('dragend', () => {
            if (draggedItem) {
                draggedItem.classList.remove('dragging');
                draggedItem = null;
            }
        });

        // Implement dragover and drop listeners on target areas (e.g., folders)
        node.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (event.target.classList.contains('folder') && draggedItem !== event.target) {
                event.target.classList.add('drag-over');
            }
        });

        node.addEventListener('dragleave', (event) => {
            if (event.target.classList.contains('folder')) {
                event.target.classList.remove('drag-over');
            }
        });

        node.addEventListener('drop', (event) => {
            event.preventDefault();
            if (event.target.classList.contains('folder') && draggedItem !== event.target) {
                event.target.classList.remove('drag-over');
                console.log(`Dropped ${draggedItem.dataset.name} into ${event.target.dataset.name}`);
                // In a real application, you would send this information to your backend
                // to update the file structure.
                // For visual feedback, you might want to move the DOM element.
                // event.target.parentNode.insertBefore(draggedItem, event.target.nextSibling);
            }
        });
    });

    // Upload Button Functionality (Simulated Progress Modal)
    if (uploadButton && uploadProgressModal && closeUploadModalButton) {
        uploadButton.addEventListener('click', () => {
            uploadProgressModal.style.display = 'block';
            // Simulate upload progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += 5;
                uploadProgressBar.style.width = `${progress}%`;
                uploadProgressText.textContent = `${progress}%`;
                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        uploadProgressModal.style.display = 'none';
                        uploadProgressBar.style.width = '0%';
                        uploadProgressText.textContent = '0%';
                        alert('Upload complete (simulated)!');
                        // In a real application, you would handle the actual file upload here.
                    }, 1500);
                }
            }, 200);
        });

        closeUploadModalButton.addEventListener('click', () => {
            uploadProgressModal.style.display = 'none';
            // In a real application, you might want to handle cancelling the upload here.
        });

        window.addEventListener('click', (event) => {
            if (event.target === uploadProgressModal) {
                uploadProgressModal.style.display = 'none';
                // In a real application, you might want to handle cancelling the upload here.
            }
        });
    }

    // Basic hover effects for file actions are handled by CSS
});
// dashboard.js (frontend)
document.addEventListener('DOMContentLoaded', () => {
    // ... (existing frontend code) ...

    const storageBar = document.querySelector('.storage-bar');
    const storageText = document.querySelector('.storage-text');
    const fileConstellationContainer = document.getElementById('file-constellation-container');

    // Function to fetch storage usage
    async function fetchStorageUsage() {
        try {
            const token = localStorage.getItem('authToken'); // Assuming you store the token in localStorage
            const response = await fetch('/api/dashboard/storage', {
                headers: {
                    'Authorization': token,
                },
            });
            if (!response.ok) {
                console.error('Failed to fetch storage usage:', response.status);
                return;
            }
            const data = await response.json();
            updateStorage(data.storageUsed, data.storageCapacity);
        } catch (error) {
            console.error('Error fetching storage usage:', error);
        }
    }

    function updateStorage(used, capacity) {
        if (storageBar && storageText) {
            const percentage = (used / capacity) * 100;
            storageBar.style.width = `${percentage}%`;
            storageText.textContent = `${formatBytes(used)} / ${formatBytes(capacity)}`;
        }
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Function to fetch recent files
    async function fetchRecentFiles() {
        try {
            const token = localStorage.getItem('authToken');
            const response = await fetch('/api/dashboard/recent-files', {
                headers: {
                    'Authorization': token,
                },
            });
            if (!response.ok) {
                console.error('Failed to fetch recent files:', response.status);
                return;
            }
            const data = await response.json();
            displayRecentFiles(data);
        } catch (error) {
            console.error('Error fetching recent files:', error);
        }
    }

    function displayRecentFiles(files) {
        if (fileConstellationContainer) {
            fileConstellationContainer.innerHTML = ''; // Clear existing content
            files.forEach(file => {
                const fileNode = document.createElement('div');
                fileNode.classList.add('file-node');
                fileNode.dataset.name = file.name;
                fileNode.innerHTML = `
                    <span class="node-icon"><i class="far fa-file"></i></span>
                    <span class="node-name">${file.name}</span>
                    <span class="node-size">${formatBytes(file.size)}</span>
                    <span class="node-actions">
                        <button><i class="fas fa-download"></i></button>
                        <button><i class="fas fa-trash"></i></button>
                    </span>
                `;
                fileConstellationContainer.appendChild(fileNode);
            });
        }
    }

    // Call these functions when the dashboard page loads
    fetchStorageUsage();
    fetchRecentFiles();

    // ... (rest of your frontend dashboard logic) ...
});