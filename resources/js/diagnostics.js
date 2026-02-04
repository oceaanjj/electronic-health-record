// Manages the diagnostics page, attaching key functions to the window
// for inline 'onclick' and 'onchange' event handlers.
(function (window) {
    // Store dynamic data read from the DOM
    let csrfToken = '';
    let patientId = '';
    let deleteAllUrlTemplate = '';

    // Stores client-side selected files (File objects) before upload,
    // organized by diagnostic type (e.g., 'xray').
    let selectedFilesStore = new Map();

    // Reads data from the main container's data attributes
    function initData() {
        const container = document.getElementById('form-content-container');
        if (container) {
            csrfToken = container.dataset.csrfToken;
            patientId = container.dataset.patientId;
            deleteAllUrlTemplate = container.dataset.deleteAllUrlTemplate;
        } else {
            console.error('Diagnostics container #form-content-container not found.');
        }

        // Initialize the file store for each type
        const types = ['xray', 'ultrasound', 'ct_scan', 'echocardiogram'];
        types.forEach((type) => selectedFilesStore.set(type, []));
    }

    // Updates the visibility of the "Drop files here..." prompt
    function updateUploadAreaState(type) {
        const panel = document.querySelector(`.diagnostic-panel[data-type="${type}"]`);
        if (!panel) return;

        const prompt = panel.querySelector(`#prompt-${type}`);
        const oldPreview = panel.querySelector(`#uploaded-files-${type}`);

        // Check our file store for any staged files
        const hasNewFiles = selectedFilesStore.get(type)?.length > 0;
        const hasOldFiles = oldPreview && oldPreview.children.length > 0;

        if (prompt) {
            prompt.style.display = hasNewFiles || hasOldFiles ? 'none' : 'flex';
        }
    }

    // Adds newly selected files to the store and triggers a re-render
    function previewImages(event, type) {
        const input = event.target;
        const newFiles = Array.from(input.files);

        // If user clicked "Cancel", newFiles is empty. Do nothing.
        if (newFiles.length === 0) {
            return;
        }

        const currentFiles = selectedFilesStore.get(type) || [];
        selectedFilesStore.set(type, [...currentFiles, ...newFiles]);

        renderPreviews(type);
    }
    // Expose to window for 'onchange'
    window.previewImages = previewImages;

    // Renders preview items from the file store and updates the file input
    function renderPreviews(type) {
        const files = selectedFilesStore.get(type);
        const previewContainer = document.getElementById('preview-' + type);
        const input = document.getElementById('file-input-' + type);

        previewContainer.innerHTML = '';

        // Use a DataTransfer object to repopulate the file input
        // This is necessary for the form to submit the managed file list
        const dataTransfer = new DataTransfer();

        if (!files || files.length === 0) {
            input.files = dataTransfer.files; // Set empty file list
            updateUploadAreaState(type);
            return;
        }

        files.forEach((file, index) => {
            dataTransfer.items.add(file); // Add file to the list for submission

            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.classList.add('preview-item');
                div.innerHTML = `
                    <img src="${e.target.result}" alt="preview">
                    <button 
                        type="button" 
                        class="delete-btn" 
                        onclick="handleDeletePreview(event, '${type}', ${index})">
                        &times;
                    </button>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        // Set the file input's files to our managed list
        input.files = dataTransfer.files;

        // Wait for images to render before updating state
        setTimeout(() => updateUploadAreaState(type), 100);
    }

    // Handles deleting a file from the client-side preview
    function handleDeletePreview(event, type, index) {
        event.preventDefault();
        event.stopPropagation();

        const files = selectedFilesStore.get(type);
        if (files) {
            files.splice(index, 1); // Remove the file
            selectedFilesStore.set(type, files);
            renderPreviews(type); // Re-render
        }
    }

    // Expose to window for 'onclick'
    window.handleDeletePreview = handleDeletePreview;

    // Clears the file input and the *new* file previews
    function clearPreview(type) {
        selectedFilesStore.set(type, []); // Clear our file store
        renderPreviews(type); // Re-render (which will be empty)
    }
    // Expose to window
    window.clearPreview = clearPreview;

    // Handles the "Clear All" button click
    function handleClearButtonClick(type) {
        const panel = document.querySelector(`.diagnostic-panel[data-type="${type}"]`);
        if (!panel) return;

        const uploadedImageIds = JSON.parse(panel.dataset.uploadedImageIds || '[]');
        const newFiles = selectedFilesStore.get(type) || [];

        if (uploadedImageIds.length > 0) {
            // If server images exist, confirm deletion of all of them
            deleteAllImages(type, uploadedImageIds);
        } else if (newFiles.length > 0) {
            // If only new previews exist, just clear them
            clearPreview(type);
        }
    }
    // Expose to window for 'onclick'
    window.handleClearButtonClick = handleClearButtonClick;

    // Initializes drag-and-drop listeners for all upload areas
    function initDragAndDrop() {
        const uploadAreas = document.querySelectorAll('.panel-upload-area');

        uploadAreas.forEach((area) => {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
                area.addEventListener(eventName, preventDefaults, false);
            });
            ['dragenter', 'dragover'].forEach((eventName) => {
                area.addEventListener(eventName, () => area.classList.add('drag-over'), false);
            });
            ['dragleave', 'drop'].forEach((eventName) => {
                area.addEventListener(eventName, () => area.classList.remove('drag-over'), false);
            });
            area.addEventListener('drop', handleDrop, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const type = e.currentTarget.dataset.type;
            const input = document.getElementById(`file-input-${type}`);

            if (input && files.length > 0) {
                // Use a synthetic event to trigger our previewImages function
                // This ensures dropped files are *added* to the store, not replaced.
                const mockEvent = { target: { files: files } };
                previewImages(mockEvent, type);
            }
        }
    }

    // --- DELETE FUNCTIONS (for server-side images) ---

    // Handles the click on a single *already uploaded* image's delete button
    function deleteImage(event, url) {
        event.preventDefault(); // Stop click from bubbling to the <label>
        event.stopPropagation(); // Stop click from bubbling

        // Check for custom confirmation modal or SweetAlert
        if (typeof window.showConfirm === 'function') {
            window
                .showConfirm('Do you really want to delete this image?', 'Delete Image?', 'Yes, delete', 'Cancel')
                .then((result) => {
                    if (result.isConfirmed) fetchDelete(url);
                });
        } else if (typeof window.Swal === 'function') {
            window.Swal.fire({
                title: 'Delete Image?',
                text: 'Do you really want to delete this image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2A1C0F',
                cancelButtonColor: '#6c757d',
            }).then((result) => {
                if (result.isConfirmed) fetchDelete(url);
            });
        } else {
            // Fallback to native confirm
            if (confirm('Delete this image?')) {
                fetchDelete(url);
            }
        }
    }
    // Expose to window for 'onclick'
    window.deleteImage = deleteImage;

    // Performs the fetch request to delete a single image
    function fetchDelete(url) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ _method: 'DELETE' }),
        })
            .then((res) => {
                if (res.ok) {
                    location.reload();
                } else {
                    handleDeleteError();
                }
            })
            .catch(() => handleDeleteError());
    }

    // Initiates the deletion of ALL *already uploaded* images for a type
    function deleteAllImages(type, imageIds) {
        const url = deleteAllUrlTemplate.replace('__TYPE__', type).replace('__PATIENT_ID__', patientId);

        const confirmText = 'Do you really want to delete ALL images for ' + type.toUpperCase() + '?';
        const confirmTitle = 'Delete All Images?';

        if (typeof window.showConfirm === 'function') {
            window.showConfirm(confirmText, confirmTitle, 'Yes', 'Cancel').then((result) => {
                if (result.isConfirmed) fetchDeleteAll(url, imageIds);
            });
        } else if (typeof window.Swal === 'function') {
            window.Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2A1C0F',
                cancelButtonColor: '#6c757d',
            }).then((result) => {
                if (result.isConfirmed) fetchDeleteAll(url, imageIds);
            });
        } else {
            if (confirm(confirmText)) {
                fetchDeleteAll(url, imageIds);
            }
        }
    }

    // Performs the fetch request to delete all images
    function fetchDeleteAll(url, imageIds) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ _method: 'DELETE', image_ids: imageIds }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    location.reload();
                } else {
                    handleDeleteError();
                }
            })
            .catch(() => handleDeleteError());
    }

    // Generic error handler for delete failures
    function handleDeleteError() {
        if (typeof window.showError === 'function') {
            window.showError('Failed to delete image(s).', 'Error');
        } else {
            alert('Failed to delete image(s).');
        }
    }

    // --- ON PAGE LOAD ---
    document.addEventListener('DOMContentLoaded', () => {
        // Read the dynamic data from the Blade template
        initData();

        // Initialize searchable dropdown (if its script is loaded)
        if (window.initSearchableDropdown) {
            window.initSearchableDropdown();
        }

        // Initialize drag-and-drop functionality
        initDragAndDrop();

        // Check initial state for all panels to hide/show prompts
        const types = ['xray', 'ultrasound', 'ct_scan', 'echocardiogram'];
        types.forEach((type) => {
            updateUploadAreaState(type);
        });
    });
})(window);
