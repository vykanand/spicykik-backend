<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Blob Uploader</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-700">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
    </button>
  </div>

  <div class="min-h-screen flex flex-col items-center justify-start pt-8 px-4">
    <div class="flex items-center justify-center mb-8">
      <!-- Logo + Title -->
      <img src="./Blob Uploader.png" alt="Blob Converter Logo" class="h-12 w-auto mr-3" />
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Blob Converter</h1>
        <p class="text-gray-600 text-sm mt-1">Transform any file up to 100KB into a secure, shareable blob URL instantly</p>
      </div>
    </div>

    <div class="max-w-4xl w-full bg-white rounded-xl shadow-lg p-6">
      <div class="space-y-4">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-emerald-500 transition-colors duration-200">
          <input type="file" id="fileInput" class="hidden" />
          <label for="fileInput" class="cursor-pointer block">
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
            <p class="text-lg text-gray-600">Click to select a file</p>
            <p class="text-sm text-gray-500 mt-2">Convert any file to a blob URL (Max: 100KB) - instant, secure, and shareable</p>
          </label>
        </div>

        <div id="filePreview" class="mt-4"></div>

        <button onclick="uploadBase64()"
          class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition duration-300">
          Upload File
        </button>

        <div id="uploadStatus" class="mt-4 space-y-2"></div>
      </div>
    </div>
  </div>

  <script>
    const JSONBIN_API = 'https://api.jsonbin.io/v3/b';
    const MASTER_KEY = '$2a$10$PqwDpYIYu0rJ4p9a/hHIRu1iX5eRr2E18hZmh/Id2QUThicDxgfxG';

    document.getElementById('fileInput').addEventListener('change', function(e) {
      const file = this.files[0];
      if (!file) return;

      // Check file size limit (100KB)
      const maxSize = 100 * 1024; // 100KB
      if (file.size > maxSize) {
        alert('File size exceeds 100KB limit. Please select a smaller file.');
        this.value = ''; // Clear the input
        return;
      }

      const previewDiv = document.getElementById('filePreview');
      previewDiv.innerHTML = '';

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
      previewDiv.appendChild(fileDiv);
    });

    function showLoader() {
      document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoader() {
      document.getElementById('loadingOverlay').style.display = 'none';
    }

    async function uploadBase64() {
      const file = document.getElementById('fileInput').files[0];
      if (!file) {
        alert('Select a file first');
        return;
      }

      // Double-check file size limit (100KB)
      const maxSize = 100 * 1024; // 100KB
      if (file.size > maxSize) {
        alert('File size exceeds 100KB limit. Please select a smaller file.');
        return;
      }

      showLoader();

      const reader = new FileReader();
      reader.onload = async () => {
        const base64String = reader.result.split(',')[1]; // get base64 part

        try {
          const response = await fetch(JSONBIN_API, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Master-Key': MASTER_KEY,
              'X-Bin-Name': 'myBase64Upload',
              'X-Collection-Id': '',  // optional, if you use collections
              'X-Access-Key': 'asc'
            },
            body: JSON.stringify({ data: base64String, mime: file.type })
          });

          if (!response.ok) {
            const errorText = await response.text();
            console.error('Upload failed:', errorText);
            alert('Upload failed: ' + response.status);
            hideLoader();
            return;
          }

          const json = await response.json();
          console.log('Upload successful:', json);

          const binId = json.metadata.id;
          const binUrl = `https://api.jsonbin.io/v3/b/${binId}`;

          // Fetch the uploaded data
          const fetchResponse = await fetch(binUrl, {
            headers: {
              'X-Master-Key': MASTER_KEY
            }
          });

          if (!fetchResponse.ok) {
            console.error('Failed to fetch uploaded data');
            alert('Upload succeeded but failed to retrieve data');
            hideLoader();
            return;
          }

          const binData = await fetchResponse.json();
          const base64Data = binData.record.data;
          const mimeType = binData.record.mime || 'application/octet-stream';
          const dataUrl = `data:${mimeType};base64,${base64Data}`;

          // Display success
          const statusDiv = document.getElementById('uploadStatus');
          statusDiv.innerHTML = '';

          const successContainer = document.createElement('div');
          successContainer.className = 'bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded';
          successContainer.innerHTML = `
            <p class="font-medium text-blue-700">File converted to blob successfully!</p>
            <p class="text-sm text-blue-600 mt-1">Your file is now available as a secure blob URL.</p>
          `;

          // Display the content
          const contentDiv = document.createElement('div');
          contentDiv.className = 'mt-4';
          if (mimeType.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = dataUrl;
            img.className = 'max-w-full h-auto rounded-lg shadow-sm';
            contentDiv.appendChild(img);
          } else {
            const viewLink = document.createElement('a');
            viewLink.href = dataUrl;
            viewLink.target = '_blank';
            viewLink.className = 'inline-block bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition duration-300';
            viewLink.textContent = `View Blob: ${file.name}`;
            contentDiv.appendChild(viewLink);
          }

          const copyBtn = document.createElement('button');
          copyBtn.className = 'mt-2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded text-sm';
          copyBtn.textContent = 'Copy Blob URL';
          copyBtn.onclick = (e) => {
            e.preventDefault();
            navigator.clipboard.writeText(dataUrl);
            copyBtn.textContent = 'Copied!';
            setTimeout(() => {
              copyBtn.textContent = 'Copy Blob URL';
            }, 2000);
          };

          statusDiv.appendChild(successContainer);
          statusDiv.appendChild(contentDiv);
          statusDiv.appendChild(copyBtn);

          // Send data URL to parent window
          console.log('Sending data URL to parent window:', dataUrl);
          if (window.parent !== window) {
            window.parent.postMessage(dataUrl, '*');
          }

          hideLoader();
        } catch (err) {
          console.error('Fetch error:', err);
          alert('Fetch error, check console');
          hideLoader();
        }
      };

      reader.readAsDataURL(file);
    }
  </script>

</body>
</html>
