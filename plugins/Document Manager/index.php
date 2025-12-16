<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document Manager Upload</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--bg-color);
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.header {
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1rem;
    margin-bottom: 2rem;
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

.upload-card {
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    padding: 2.5rem;
    text-align: center;
}

.upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 3rem 2rem;
    background-color: #f8fafc;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: var(--primary-color);
    background-color: #f0f4ff;
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
}

.btn {
    background-color: var(--primary-color);
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn:hover {
    background-color: var(--primary-hover);
}

.status-message {
    margin-top: 1rem;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    border-radius: 8px;
}

.status-message.success {
    display: flex;
    background-color: #ecfdf5;
    color: #065f46;
}

.status-message.error {
    display: flex;
    background-color: #fef2f2;
    color: #991b1b;
}

.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>

<body>
<div class="container">
    <header class="header">
        <img src="./Document Manager.png" alt="Logo" class="logo">
        <div class="logo-text">Document Manager</div>
    </header>

    <main>
        <div class="upload-card">
            <div class="upload-area" id="uploadArea">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.6; margin-bottom:1rem;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p>Click or drag files to upload (supports any file type up to 5GB)</p>
            </div>

            <input 
                type="hidden"
                role="uploadcare-uploader"
                id="fileInput"
                data-public-key="e073252401baf915ad63"
                data-multiple="true"
                data-images-only="false"
                data-tabs="file camera url gdrive dropbox"
                style="display:none;"
            />

            <div class="link-input-container">
                <input type="text" id="messageInput" class="link-input" placeholder="File URL will appear here..." readonly />
                <button class="btn" id="copyButton" style="display:none; margin-top:0.5rem;">
                    <i class="fas fa-copy"></i> Copy Link
                </button>
            </div>

            <div class="status-message" id="statusMessage"></div>
        </div>
    </main>
</div>

<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js"></script>
<script>
window.UPLOADCARE_PUBLIC_KEY = 'e073252401baf915ad63';
window.UPLOADCARE_IMAGES_ONLY = false;
window.UPLOADCARE_FILE_TYPES = '*/*';
window.UPLOADCARE_ALLOWED_FILE_TYPES = ['*/*'];
window.UPLOADCARE_MULTIPLE = true;
window.UPLOADCARE_PREVIEW_STEP = false;

let widget, isUploading = false;

document.addEventListener('DOMContentLoaded', () => {
    const uploadArea = document.getElementById('uploadArea');
    const messageInput = document.getElementById('messageInput');
    const copyButton = document.getElementById('copyButton');
    const statusMessage = document.getElementById('statusMessage');

    widget = uploadcare.Widget('#fileInput');
    console.log('Uploadcare initialized');

    widget.onChange(file => {
        if (!file) return;
        if (file.count && file.count > 1) {
            showUploading('Multiple files');
        } else if (file.name) {
            showUploading(file.name);
        }
    });

    widget.onUploadComplete(fileInfo => {
        console.log('Upload complete:', fileInfo);

        let finalUrl = fileInfo.cdnUrl;
        let fileName = fileInfo.name || 'File(s)';

        if (fileInfo.count && fileInfo.count > 1) {
            fileName = `${fileInfo.count} files`;
            finalUrl = fileInfo.cdnUrl; // group URL
        }

        showSuccess(fileName, finalUrl);
        messageInput.value = finalUrl;
        copyButton.style.display = 'inline-flex';

        // ✅ Send uploaded file URL back to parent window
        try {
            console.log('>>> Sending URL to parent window:', finalUrl);
            window.parent.postMessage(finalUrl, '*');
        } catch (err) {
            console.error('Error sending postMessage:', err);
        }

        // ✅ Close popup or iframe automatically
        setTimeout(() => {
            try {
                if (window.parent !== window) {
                    window.parent.postMessage('close-plugin', '*');
                    console.log('>>> Close-plugin message sent');
                } else {
                    window.close();
                }
            } catch (err) {
                console.error('Error closing plugin window:', err);
            }
        }, 2000);
    });

    // Click to open Uploadcare dialog
    uploadArea.addEventListener('click', () => widget.openDialog());

    // Drag and drop support
    ['dragenter','dragover','dragleave','drop'].forEach(eName => {
        uploadArea.addEventListener(eName, e => {
            e.preventDefault(); e.stopPropagation();
        });
    });

    uploadArea.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (!files.length) return;
        showUploading(files.length > 1 ? `${files.length} files` : files[0].name);
        uploadcare.fileFrom('object', files[0])
            .done(f => widget.value(f))
            .fail(err => showError(err.message || 'Upload failed'));
    });

    // Copy URL button
    copyButton.addEventListener('click', () => {
        messageInput.select();
        document.execCommand('copy');
        copyButton.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => {
            copyButton.innerHTML = '<i class="fas fa-copy"></i> Copy Link';
        }, 2000);
    });

    // --- UI helpers ---
    function showUploading(name) {
        isUploading = true;
        uploadArea.innerHTML = `
            <div class="spinner"></div>
            <p>Uploading ${name}...</p>
        `;
    }

    function showSuccess(name, url) {
        isUploading = false;
        uploadArea.innerHTML = `
            <i class="fas fa-check-circle" style="color:var(--success-color); font-size:3rem; margin-bottom:1rem;"></i>
            <p>${name} uploaded successfully!</p>
        `;
        statusMessage.className = 'status-message success';
        statusMessage.innerHTML = `<i class="fas fa-check-circle"></i><span>File uploaded successfully!</span>`;
        statusMessage.style.display = 'flex';
        setTimeout(() => statusMessage.style.display = 'none', 4000);
    }

    function showError(err) {
        isUploading = false;
        uploadArea.innerHTML = `
            <i class="fas fa-exclamation-circle" style="color:#ef4444; font-size:3rem; margin-bottom:1rem;"></i>
            <p style="color:#ef4444; font-weight:bold;">${err}</p>
        `;
        statusMessage.className = 'status-message error';
        statusMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${err}</span>`;
        statusMessage.style.display = 'flex';
    }
});
</script>
</body>
</html>
