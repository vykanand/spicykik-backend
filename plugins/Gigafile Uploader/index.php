<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gigafile Uploader</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .loader {
      width: 48px;
      height: 48px;
      border: 5px solid #FFF;
      border-bottom-color: #FF3D00;
      border-radius: 50%;
      display: inline-block;
      box-sizing: border-box;
      animation: rotation 1s linear infinite;
    }
    @keyframes rotation {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .loading-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.7);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

  <div id="loadingOverlay" class="loading-overlay">
    <span class="loader"></span>
  </div>

  <!-- Back Button -->
  <div class="fixed top-4 left-4 z-50">
    <button onclick="history.back()" class="bg-white p-2 rounded-full shadow-lg hover:shadow-xl transition-shadow">
      <img src="./assets/back-black.png" class="w-8 h-8" alt="Back" />
    </button>
  </div>

  <div class="min-h-screen flex flex-col items-center justify-start pt-8 px-4">
    <div class="flex items-center justify-center mb-8">
      <!-- Logo + Title -->
      <img src="./Gigafile Uploader.png" alt="Gigafile Logo" class="h-12 w-auto mr-3" />
      <h1 class="text-3xl font-bold text-gray-800">Gigafile Uploader</h1>
    </div>

    <div class="max-w-4xl w-full bg-white rounded-xl shadow-lg p-6">
      <div class="space-y-4">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-emerald-500 transition-colors duration-200">
          <input type="file" id="fileInput" multiple class="hidden" />
          <label for="fileInput" class="cursor-pointer block">
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
            <p class="text-lg text-gray-600">Drag files here or click to browse</p>
            <p class="text-sm text-gray-500 mt-2">Max file size: 5GB per file</p>
          </label>
        </div>

        <div id="fileList" class="mt-4 space-y-2"></div>

        <button onclick="uploadFiles()" 
          class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition duration-300">
          Upload Files
        </button>

        <div id="uploadStatus" class="mt-4 space-y-2"></div>
      </div>
    </div>
  </div>

  <script>
    // ImgBB API endpoint for image uploads
    const IMGBB_API = 'https://api.imgbb.com/1/upload';
    const API_KEY = 'a44a6352445eaa78a9233b5e03f860d3';

    document.getElementById('fileInput').addEventListener('change', function(e) {
      const fileList = document.getElementById('fileList');
      fileList.innerHTML = '';
      
      Array.from(this.files).forEach(file => {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'flex flex-col bg-gray-50 p-4 rounded mb-4';
        
        const previewContainer = document.createElement('div');
        previewContainer.className = 'flex flex-col items-center gap-4 mb-3';
        
        if (file.type.startsWith('image/')) {
          const img = document.createElement('img');
          img.className = 'w-full max-w-md h-48 object-contain rounded-lg shadow-sm';
          const reader = new FileReader();
          reader.onload = (e) => {
            img.src = e.target.result;
          };
          reader.readAsDataURL(file);
          previewContainer.appendChild(img);
        } else {
          const fileIcon = document.createElement('div');
          fileIcon.className = 'w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-600 text-xl font-bold';
          fileIcon.textContent = file.type.split('/')[1]?.toUpperCase() || 'FILE';
          previewContainer.appendChild(fileIcon);
        }
        
        const fileInfo = document.createElement('div');
        fileInfo.className = 'text-center';
        fileInfo.innerHTML = `
          <span class="block text-base font-medium text-gray-700 mb-1">${file.name}</span>
          <span class="block text-sm text-gray-500">${(file.size/1024).toFixed(2)} KB</span>
        `;
        previewContainer.appendChild(fileInfo);
        
        fileDiv.appendChild(previewContainer);
        fileList.appendChild(fileDiv);
      });
    });

    function showLoader() {
      document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoader() {
      document.getElementById('loadingOverlay').style.display = 'none';
    }

    async function uploadFiles() {
      showLoader();
      const files = document.getElementById('fileInput').files;
      const statusDiv = document.getElementById('uploadStatus');
      statusDiv.innerHTML = '';

      if (!files.length) {
        alert('Please select at least one file');
        hideLoader();
        return;
      }

      try {
        // Upload each file individually to ImgBB
        for (const file of files) {
          const formData = new FormData();
          formData.append('key', API_KEY);
          formData.append('image', file);

          const uploadResponse = await fetch(`${IMGBB_API}`, {
            method: 'POST',
            body: formData
          });

          if (!uploadResponse.ok) {
            throw new Error(`Failed to upload ${file.name}`);
          }

          const fileData = await uploadResponse.json();
          const fileUrl = fileData.data.url;

          // Create a container for each file's info
          const fileContainer = document.createElement('div');
          fileContainer.className = 'bg-gray-50 p-4 rounded-lg mb-4';

          // Add file name and size
          const fileInfo = document.createElement('div');
          fileInfo.className = 'mb-2';
          fileInfo.innerHTML = `
            <span class="font-medium">${file.name}</span>
            <span class="text-sm text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
          `;

          // Add clickable link
          const link = document.createElement('a');
          link.href = fileUrl;
          link.target = '_blank';
          link.className = 'block text-emerald-600 hover:text-emerald-800 break-all';
          link.textContent = fileUrl;

          // Add copy button
          const copyBtn = document.createElement('button');
          copyBtn.className = 'mt-2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded text-sm';
          copyBtn.textContent = 'Copy URL';
          copyBtn.onclick = (e) => {
            e.preventDefault();
            navigator.clipboard.writeText(fileUrl);
            copyBtn.textContent = 'Copied!';
            setTimeout(() => {
              copyBtn.textContent = 'Copy URL';
            }, 2000);
          };

          fileContainer.appendChild(fileInfo);
          fileContainer.appendChild(link);
          fileContainer.appendChild(copyBtn);
          statusDiv.appendChild(fileContainer);

          // Send URL to parent window if embedded
          if (window.parent !== window) {
            window.parent.postMessage(fileUrl, '*');
          }
        }

        // Add success message at the top
        const successContainer = document.createElement('div');
        successContainer.className = 'bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded';
        successContainer.innerHTML = `
          <p class="font-medium text-blue-700">All files uploaded successfully!</p>
          <p class="text-sm text-blue-600 mt-1">Files are available for download via the links below.</p>
        `;
        statusDiv.insertBefore(successContainer, statusDiv.firstChild);
      } catch (error) {
        console.error('Upload error:', error);
        alert('Upload failed, check console for details');
      } finally {
        hideLoader();
      }
    }

    function fileToBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = (err) => reject(err);
        reader.readAsDataURL(file);
      });
    }
  </script>

</body>
</html>
