<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploadcare</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --bg-color: #f9fafb;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --success-color: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin: 0 auto;
        }

        .logo {
            height: 40px;
            margin-right: 12px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .back-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-button:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .back-button img {
            height: 24px;
            width: 24px;
        }

        .upload-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .subtitle {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 3rem 2rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background-color: #f0f4ff;
        }

        .upload-area p {
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        .btn i {
            margin-right: 8px;
        }

        .link-input-container {
            margin-top: 1.5rem;
            text-align: left;
        }

        .link-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9375rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            margin-bottom: 1rem;
        }

        .link-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        .status-message {
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .status-message.success {
            background-color: #ecfdf5;
            color: #065f46;
            display: flex;
        }

        .status-message.error {
            background-color: #fef2f2;
            color: #991b1b;
            display: flex;
        }

        .status-message i {
            margin-right: 8px;
        }

        .file-info {
            margin-top: 1rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.25rem;
            }

            .upload-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <button class="back-button" onclick="history.back();">
                <img src="./assets/back-black.png" alt="Back" />
            </button>
            <div class="logo-container">
                <img src="./Document Manager.png" alt="DMS Logo" class="logo">
                <div class="logo-text">Document Manager</div>
            </div>
        </header>

        <main>
            <div class="upload-card">
                <div class="upload-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                </div>
                <h1>Upload Files</h1>
                <p class="subtitle">Drag & drop files here or click to browse</p>
                
                <!-- Uploadcare widget input -->
                <div class="upload-area" id="uploadArea">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6; margin-bottom: 1rem;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <p>Click to select files or drag and drop</p>
                    <div class="file-info" id="fileInfo">Supports all file types up to 5GB</div>
                </div>

                <input type="hidden" role="uploadcare-uploader" id="fileInput" data-multiple="true" style="display: none;" />

                <div class="link-input-container">
                    <input type="text" id="messageInput" class="link-input" placeholder="File URL will appear here after upload" readonly />
                    <button class="btn" onclick="copyToClipboard()" id="copyButton" style="display: none; margin-top: 0.5rem;">
                        <i class="fas fa-copy"></i> Copy Link
                    </button>
                </div>

                <div class="status-message" id="statusMessage">
                    <i class="fas fa-check-circle"></i>
                    <span>File uploaded successfully!</span>
                </div>
            </div>
        </main>

    <!-- Uploadcare widget -->
    <script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        // Configuration
        UPLOADCARE_PUBLIC_KEY = 'e073252401baf915ad63';
        const MAX_FILE_SIZE_MB = 100; // 100MB max file size
        const ALLOWED_FILE_TYPES = ['*/*']; // Allow all file types
        let widget;
        let isUploading = false;

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Uploadcare widget
            widget = uploadcare.Widget('#fileInput');
            const uploadArea = document.getElementById('uploadArea');
            const fileInfo = document.getElementById('fileInfo');
            const messageInput = document.getElementById('messageInput');
            const copyButton = document.getElementById('copyButton');
            const statusMessage = document.getElementById('statusMessage');

            // Handle drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadArea.classList.add('highlight');
                uploadArea.style.borderColor = 'var(--primary-color)';
                uploadArea.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
            }

            function unhighlight() {
                uploadArea.classList.remove('highlight');
                uploadArea.style.borderColor = '';
                uploadArea.style.backgroundColor = '';
            }

            // Add click event to upload area
            uploadArea.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!isUploading) {
                    widget.openDialog();
                }
            });

            // Handle file selection
            widget.onUploadComplete = function(fileInfo) {
                if (fileInfo) {
                    isUploading = true;
                    updateUIForUploading();
                    
                    if (fileInfo.cdnUrl) {
                        messageInput.value = fileInfo.cdnUrl;
                        copyButton.style.display = 'inline-block';
                        updateUIForSuccess();
                        // Auto-copy to clipboard
                        copyToClipboard();
                    }
                    isUploading = false;
                }
            };

            // Handle dropped files
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                unhighlight();
                
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    isUploading = true;
                    updateUIForUploading(files[0].name);
                    widget.value(files);
                }
            });

            function updateUIForUploading(fileName) {
                uploadArea.innerHTML = `
                    <div class="uploading-animation">
                        <div class="spinner"></div>
                        <p>Uploading ${fileName}...</p>
                        <div class="progress-bar">
                            <div class="progress"></div>
                        </div>
                    </div>
                `;
                uploadArea.style.cursor = 'wait';
            }

            function updateUIForSuccess(fileName, fileUrl) {
                uploadArea.innerHTML = `
                    <div class="success-animation">
                        <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>${fileName} uploaded successfully!</p>
                    </div>
                `;
                uploadArea.style.cursor = 'default';
                
                // Show the file URL and copy button
                messageInput.value = fileUrl;
                copyButton.style.display = 'inline-flex';
                
                // Show success message
                statusMessage.style.display = 'flex';
                statusMessage.className = 'status-message success';
                statusMessage.innerHTML = '<i class="fas fa-check-circle"></i><span>File uploaded successfully!</span>';
                
                // Auto-hide success message after 5 seconds
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
                
                // Close the window after 3 seconds
                setTimeout(() => {
                    window.close();
                }, 3000);
            }

            function updateUIForError(error) {
                uploadArea.innerHTML = `
                    <div class="error-animation">
                        <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>Upload failed: ${error}</p>
                        <button class="btn" onclick="window.location.reload()" style="margin-top: 1rem;">
                            <i class="fas fa-sync-alt"></i> Try Again
                        </button>
                    </div>
                `;
                uploadArea.style.cursor = 'default';
                
                // Show error message
                statusMessage.style.display = 'flex';
                statusMessage.className = 'status-message error';
                statusMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>Upload failed: ${error}</span>`;
                
                isUploading = false;
            }

            widget.onChange(async (file) => {
                if (!file) return;

                try {
                    let uploadedFiles = [];

                    if (file.files) {
                        // Multiple files
                        for await (const item of file.files()) {
                            let fileInfo;
                            if (typeof item.done === 'function') {
                                fileInfo = await item.done();
                            } else {
                                fileInfo = item;
                            }
                            uploadedFiles.push(fileInfo);
                        }
                    } else {
                        // Single file
                        const fileInfo = await file.done();
                        uploadedFiles.push(fileInfo);
                    }

                    if (uploadedFiles.length > 0) {
                        // Put first uploaded file URL in the input
                        const firstUrl = uploadedFiles[0].cdnUrl;
                        const input = document.getElementById('messageInput');
                        input.value = firstUrl;

                        // Send the URL to the parent window
                        window.parent.postMessage(firstUrl, '*');

                        // Close the plugin window after a short delay
                        setTimeout(() => {
                            window.close();
                        }, 500);
                    }
                } catch (error) {
                    console.error('Upload failed:', error);
                }
            });
        });

        function sendMessageToParent() {
            const message = document.getElementById('messageInput').value;
            console.log('Plugin sending message:', message);
            window.parent.postMessage(message, '*');
        }
    </script>
</body>
</html>
