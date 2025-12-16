<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>File Upload & Share Interface</title>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="jszip.js"></script>
  <script src="xlsx.js"></script>
  <style>
    /* Reset and base styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
      background-color: #f9fafb;
      color: #1f2937;
      line-height: 1.5;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .main-content {
      display: flex;
      gap: 3rem;
      max-width: 64rem;
      width: 100%;
    }

    /* Upload Card Styles */
    .upload-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      width: 20rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .upload-header {
      font-weight: 700;
      font-size: 1.125rem;
      color: #374151;
      padding: 0.75rem 0;
      border-radius: 1rem 1rem 0 0;
      background: #f3f4f6;
      width: 100%;
      text-align: center;
    }

    .upload-content {
      padding: 0 1rem 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
    }

    .input-container {
      margin-top: 2rem;
      margin-bottom: 1rem;
      width: 100%;
      max-width: 15rem;
    }

    .app-name-input {
      width: 100%;
      height: 3rem;
      border-radius: 0.5rem;
      border: 2px solid #3b82f6;
      padding: 0 1rem;
      font-size: 1rem;
      color: #1f2937;
      outline: none;
      transition: border-color 0.2s ease;
    }

    .app-name-input:focus {
      border-color: #2563eb;
    }

    .drag-drop-area {
      position: relative;
      width: 15rem;
      height: 15rem;
      border-radius: 50%;
      border: 2px dashed #3b82f6;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .drag-drop-area:hover {
      border-color: #2563eb;
      background-color: #dbeafe;
      transform: scale(1.02);
    }

    .drag-drop-area.dragging {
      border-color: #2563eb;
      background-color: #dbeafe;
      transform: scale(1.05);
    }

    .circle-outer,
    .circle-middle {
      position: absolute;
      border-radius: 50%;
      border: 1px dashed;
      pointer-events: none;
    }

    .circle-outer {
      width: 16rem;
      height: 16rem;
      border-color: rgba(59, 130, 246, 0.25);
      top: -0.5rem;
      left: -0.5rem;
    }

    .circle-middle {
      width: 17rem;
      height: 17rem;
      border-color: rgba(59, 130, 246, 0.15);
      top: -1rem;
      left: -1rem;
    }

    .add-button {
      background-color: #3b82f6;
      border: none;
      border-radius: 50%;
      width: 3rem;
      height: 3rem;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 1rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
      outline: none;
    }

    .add-button:hover,
    .add-button:focus {
      background-color: #2563eb;
    }

    .drag-text {
      font-size: 1rem;
      font-weight: 500;
      color: #374151;
      margin-bottom: 0.5rem;
      text-align: center;
    }

    .select-file-link {
      color: #3b82f6;
      text-decoration: underline;
      font-weight: 500;
      font-size: 0.875rem;
      cursor: pointer;
      margin-bottom: 0.75rem;
      outline: none;
    }

    .select-file-link:hover,
    .select-file-link:focus {
      color: #1d4ed8;
    }

    .limit-info {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.25rem;
      text-align: center;
    }

    .lightning-icon {
      color: #3b82f6;
      fill: #3b82f6;
    }

    .increase-limit-link {
      color: #3b82f6;
      font-weight: 600;
      text-decoration: none;
      outline: none;
    }

    .increase-limit-link:hover,
    .increase-limit-link:focus {
      color: #1d4ed8;
    }

    .upload-footer {
      font-size: 0.75rem;
      color: #6b7280;
      text-align: center;
      margin-top: auto;
      margin-bottom: 1.5rem;
      max-width: 17.5rem;
      line-height: 1.4;
      padding: 0 1rem;
    }

    .footer-link {
      color: #3b82f6;
      text-decoration: none;
      outline: none;
    }

    .footer-link:hover,
    .footer-link:focus {
      text-decoration: underline;
    }

    /* Info Section Styles */
    .info-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 1rem;
      max-width: 32.5rem;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: 1px solid #d1d5db;
      border-radius: 9999px;
      padding: 0.25rem 1rem;
      font-size: 0.875rem;
      color: #1d4ed8;
      background-color: #dbeafe;
      width: fit-content;
    }

    .lock-icon {
      color: #1d4ed8;
      fill: #1d4ed8;
    }

    .main-heading {
      font-weight: 700;
      font-size: 2.5rem;
      line-height: 1.1;
      color: #059669;
      margin: 0;
    }

    .heading-emphasis {
      color: #1e293b;
      font-weight: 900;
      display: block;
    }

    .description {
      font-size: 1rem;
      color: #4b5563;
      line-height: 1.6;
      margin: 0;
    }

    .feature-list {
      list-style: none;
      padding: 0;
      margin: 2rem 0 0;
      display: flex;
      gap: 2rem;
    }

    .feature-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      color: #111827;
      cursor: default;
      min-width: 4.5rem;
      transition: transform 0.2s ease;
      outline: none;
    }

    .feature-item:hover,
    .feature-item:focus {
      transform: scale(1.1);
    }

    .feature-icon {
      width: 2rem;
      height: 2rem;
      transition: transform 0.2s ease;
    }

    .feature-label {
      text-align: center;
      font-weight: 500;
    }

    /* Notification styles */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10b981;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .main-content {
        flex-direction: column;
        gap: 2rem;
        align-items: center;
      }
      
      .upload-card {
        width: 100%;
        max-width: 20rem;
      }
      
      .info-section {
        text-align: center;
      }
      
      .main-heading {
        font-size: 2rem;
      }
      
      .feature-list {
        justify-content: center;
        flex-wrap: wrap;
      }
    }

    @media (max-width: 480px) {
      .container {
        padding: 1rem;
      }
      
      .main-content {
        gap: 1.5rem;
      }
      
      .drag-drop-area {
        width: 12rem;
        height: 12rem;
      }
      
      .circle-outer {
        width: 13rem;
        height: 13rem;
      }
      
      .circle-middle {
        width: 14rem;
        height: 14rem;
      }
      
      .main-heading {
        font-size: 1.75rem;
      }
      
      .feature-list {
        gap: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <main class="main-content" role="main">
      <!-- Upload Card -->
      <section class="upload-card">
        <!-- Header -->
        <header class="upload-header">
          App Name
        </header>

        <!-- Main upload area -->
        <div class="upload-content">
          <!-- App name input -->
          <div class="input-container">
            <input
              type="text"
              id="appNameInput"
              placeholder="Enter app name"
              class="app-name-input"
              aria-label="App name input"
            />
          </div>

          <!-- Circular drag and drop area -->
          <div
            class="drag-drop-area"
            id="dragDropArea"
            role="region"
            aria-label="Drag and drop file here or select a file"
          >
            <!-- Concentric circles -->
            <div class="circle-outer"></div>
            <div class="circle-middle"></div>

            <!-- Plus button -->
            <button
              class="add-button"
              id="addButton"
              aria-label="Add file"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
            </button>

            <!-- Text -->
            <p class="drag-text">
              Drag and Drop file here
            </p>
            <a
              href="#"
              class="select-file-link"
              tabindex="0"
            >
              Or select a file
            </a>

            <!-- Limit info -->
            <p class="limit-info">
              Up to 4GB free
              <svg class="lightning-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              <a
                href="#"
                class="increase-limit-link"
                tabindex="0"
              >
                Increase Limit
              </a>
            </p>
          </div>
        </div>

        <!-- Footer -->
        <footer class="upload-footer">
          By uploading files you agree to the LimeWire
          <a
            href="#"
            class="footer-link"
            tabindex="0"
          >
            Terms & Conditions
          </a>
          and
          <a
            href="#"
            class="footer-link"
            tabindex="0"
          >
            Privacy Policy
          </a>.
        </footer>
      </section>

      <!-- Info Section -->
      <section class="info-section">
        <!-- Badge -->
        <div class="badge">
          <svg class="lock-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6z"/>
          </svg>
          <span>
            <strong>The new Importer</strong> — Easy app creator!
          </span>
        </div>

        <!-- Main heading -->
        <h1 class="main-heading">
          Upload & Easily 
          <strong class="heading-emphasis">
            convert your excel CSV to apps!
          </strong>
        </h1>

        <!-- Description -->
        <p class="description">
          Billion Importer can import bulk excel, CSV files and create your modules with all your data in Billion platform!
        </p>

        <!-- Feature list -->
        <ul class="feature-list" role="list">
          <li class="feature-item" tabindex="0" aria-label="Upload Files">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7,10 12,15 17,10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
            </div>
            <span class="feature-label">Upload Files</span>
          </li>
          <li class="feature-item" tabindex="0" aria-label="Edit with AI">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12,2 15.09,8.26 22,9 17,14.74 18.18,21.02 12,17.77 5.82,21.02 7,14.74 2,9 8.91,8.26 12,2"/>
              </svg>
            </div>
            <span class="feature-label">Edit with AI</span>
          </li>
          <li class="feature-item" tabindex="0" aria-label="Share Files">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"/>
                <circle cx="6" cy="12" r="3"/>
                <circle cx="18" cy="19" r="3"/>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
              </svg>
            </div>
            <span class="feature-label">Share Files</span>
          </li>
          <li class="feature-item" tabindex="0" aria-label="Track Downloads">
            <div class="feature-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17,8 12,3 7,8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
            </div>
            <span class="feature-label">Track Downloads</span>
          </li>
        </ul>
      </section>
    </main>
  </div>

  <script>
    function createModule(osp, myJSONText, ec, retryCount) {
      if (retryCount > 3) {
        console.error('Module creation failed after 3 retries');
        window.top.postMessage('error^Could not create module after retries', '*');
        return;
      }
      $.ajax({
        type: "POST",
        url: "/module.php",
        data: { dest: osp, dta: myJSONText },
        success: function(res) {
          console.log('Module creation response:', res);
          try {
            var response = JSON.parse(res);
            if (response && response.response === 'success') {
              console.log('Module created successfully');
              // Step 2: Bulk insert data
              insertData(osp, ec, 0);
            } else {
              console.error('Module creation failed:', response.error);
              // Retry
              setTimeout(function() { createModule(osp, myJSONText, ec, retryCount + 1); }, 1000);
            }
          } catch (e) {
            console.error('Invalid JSON response:', res);
            // Retry
            setTimeout(function() { createModule(osp, myJSONText, ec, retryCount + 1); }, 1000);
          }
        },
        error: function(xhr, status, error) {
          console.error('Module creation AJAX failed:', error);
          // Retry
          setTimeout(function() { createModule(osp, myJSONText, ec, retryCount + 1); }, 1000);
        }
      });
    }

    function insertData(osp, ec, retryCount) {
      if (retryCount > 3) {
        console.error('Data insert failed after 3 retries');
        window.top.postMessage('error^Failed to insert data after retries', '*');
        return;
      }
      $.ajax({
        type: "POST",
        url: "/customapi.php",
        data: { dest: osp, dta: ec, bulkins: true },
        success: function(insertRes) {
          console.log('Bulk insert response:', insertRes);
          try {
            var insertResponse = JSON.parse(insertRes);
            if (insertResponse && insertResponse.response === true) {
              window.top.postMessage('success^App Created and Data Inserted', '*');
              window.location.href = '../erpconsole/manage';
            } else {
              console.error('Data insert failed:', insertResponse.error);
              // Retry
              setTimeout(function() { insertData(osp, ec, retryCount + 1); }, 1000);
            }
          } catch (e) {
            console.error('Invalid insert JSON:', insertRes);
            // Retry
            setTimeout(function() { insertData(osp, ec, retryCount + 1); }, 1000);
          }
        },
        error: function(xhr, status, error) {
          console.error('Bulk insert failed:', error);
          // Retry
          setTimeout(function() { insertData(osp, ec, retryCount + 1); }, 1000);
        }
      });
    }

    function eco(ec) {
      var dkec = JSON.parse(ec);
      var trr = Object.keys(dkec[0]);
      console.log('Headers:', trr);
      console.log('Data:', ec);

      var osp = $('#appNameInput').val().trim();
      osp = osp.replace(/\s+/g, '_');

      if (osp.length > 3 && trr.length > 1) {
        var myJSONText = JSON.stringify(trr);

        // Step 1: Create the module with retries
        createModule(osp, myJSONText, ec, 0);
      } else {
        if (osp.length < 3) {
          window.top.postMessage('error^Please Enter App Name (at least 4 characters)', '*');
        }
        if (trr.length < 1) {
          window.top.postMessage('error^Please ensure the file has at least 1 field', '*');
        }
      }
    }

    function parseExcel(file) {
      var reader = new FileReader();
      console.log('prse');
      reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
          type: 'binary'
        });

        workbook.SheetNames.forEach(function(sheetName) {
          // Here is your object
          var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
          var json_object = JSON.stringify(XL_row_object);
          // console.log(json_object);
          eco(json_object);
        });
      };

      reader.onerror = function(ex) {
        console.log(ex);
      };

      reader.readAsBinaryString(file);
    }

    function csvJSON(file) {
      var reader = new FileReader();
      console.log('crse');
      reader.onload = function(e) {
        var data = e.target.result;
        // console.log(data);
        var lines = data.split("\n");

        var result = [];

        // NOTE: If your columns contain commas in their values, you'll need
        // to deal with those before doing the next step
        // (you might convert them to &&& or something, then covert them back later)
        // jsfiddle showing the issue https://jsfiddle.net/
        var headers = lines[0].split(",");

        for (var i = 1; i < lines.length; i++) {
          var obj = {};
          var currentline = lines[i].split(",");

          for (var j = 0; j < headers.length; j++) {
            if (currentline[j]) {
              obj[headers[j].replace(/(\r\n|\n|\r)/gm, "")] = currentline[j].replace(/(\r\n|\n|\r)/gm, "");
            }
          }
          // dont push blank obj
          if (obj && Object.keys(obj).length === 0 && obj.constructor === Object) {
            // Do nothing
          } else {
            result.push(obj);
          }
        }
        // console.log(JSON.stringify(result));
        // return result; //JavaScript object
        // return JSON.stringify(result); //JSON
        eco(JSON.stringify(result));
      };

      reader.onerror = function(ex) {
        console.log(ex);
      };

      reader.readAsBinaryString(file);
    }

    // DOM Elements
    const dragDropArea = document.getElementById('dragDropArea');
    const addButton = document.getElementById('addButton');
    const appNameInput = document.getElementById('appNameInput');
    const selectFileLink = document.querySelector('.select-file-link');
    const increaseLimitLink = document.querySelector('.increase-limit-link');
    const featureItems = document.querySelectorAll('.feature-item');

    // State
    let isDragging = false;

    // Drag and Drop Functionality
    function handleDragEnter(e) {
      e.preventDefault();
      isDragging = true;
      dragDropArea.classList.add('dragging');
    }

    function handleDragLeave(e) {
      e.preventDefault();
      // Only remove dragging state if we're leaving the drag area entirely
      if (!dragDropArea.contains(e.relatedTarget)) {
        isDragging = false;
        dragDropArea.classList.remove('dragging');
      }
    }

    function handleDragOver(e) {
      e.preventDefault();
    }

    function handleDrop(e) {
      e.preventDefault();
      isDragging = false;
      dragDropArea.classList.remove('dragging');
      
      const files = e.dataTransfer.files;
      handleFiles(files);
    }

    function handleFiles(files) {
      if (files.length !== 1) {
        showNotification('Please select only one Excel or CSV file.');
        return;
      }
      const file = files[0];
      const fnm = file.name;
      const fext = fnm.split('.').pop().toLowerCase();
      const appName = document.getElementById('appNameInput').value.trim();
      if (appName.length < 4) {
        showNotification('Please enter a valid app name (at least 4 characters) before uploading.');
        return;
      }
      if (fext === 'xlsx' || fext === 'xls') {
        parseExcel(file);
      } else if (fext === 'csv') {
        csvJSON(file);
      } else {
        showNotification('Please select a valid Excel or CSV file.');
      }
    }

    function showNotification(message) {
      // Create a simple notification
      const notification = document.createElement('div');
      notification.textContent = message;
      notification.className = 'notification';
      
      document.body.appendChild(notification);
      
      // Remove notification after 3 seconds
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
          if (document.body.contains(notification)) {
            document.body.removeChild(notification);
          }
        }, 300);
      }, 3000);
    }

    // File Input Handler
    function createFileInput() {
      const input = document.createElement('input');
      input.type = 'file';
      input.multiple = false;
      input.accept = '.csv,.xls,.xlsx';
      input.style.display = 'none';

      input.addEventListener('change', (e) => {
        const files = e.target.files;
        if (files.length > 0) {
          handleFiles(files);
        }
      });

      return input;
    }

    // Event Listeners
    dragDropArea.addEventListener('dragenter', handleDragEnter);
    dragDropArea.addEventListener('dragleave', handleDragLeave);
    dragDropArea.addEventListener('dragover', handleDragOver);
    dragDropArea.addEventListener('drop', handleDrop);

    // Click to upload
    dragDropArea.addEventListener('click', () => {
      const fileInput = createFileInput();
      document.body.appendChild(fileInput);
      fileInput.click();
      document.body.removeChild(fileInput);
    });

    addButton.addEventListener('click', (e) => {
      e.stopPropagation();
      const fileInput = createFileInput();
      document.body.appendChild(fileInput);
      fileInput.click();
      document.body.removeChild(fileInput);
    });

    // Select file link
    selectFileLink.addEventListener('click', (e) => {
      e.preventDefault();
      const fileInput = createFileInput();
      document.body.appendChild(fileInput);
      fileInput.click();
      document.body.removeChild(fileInput);
    });

    // Increase limit link
    increaseLimitLink.addEventListener('click', (e) => {
      e.preventDefault();
      showNotification('Redirecting to upgrade options...');
      console.log('Upgrade limit clicked');
    });

    // App name input handling
    appNameInput.addEventListener('input', (e) => {
      const value = e.target.value;
      console.log('App name changed:', value);
      localStorage.setItem('appName', value);
    });

    // Feature item interactions
    featureItems.forEach(item => {
      item.addEventListener('mouseenter', () => {
        item.style.transform = 'scale(1.1)';
      });
      
      item.addEventListener('mouseleave', () => {
        item.style.transform = 'scale(1)';
      });
      
      item.addEventListener('click', () => {
        const label = item.getAttribute('aria-label');
        showNotification(`${label} feature clicked`);
        console.log(`Feature clicked: ${label}`);
      });
    });

    // Keyboard navigation for feature items
    featureItems.forEach(item => {
      item.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          item.click();
        }
      });
    });

    // Footer links
    document.querySelectorAll('.footer-link').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const text = link.textContent;
        showNotification(`Opening ${text}...`);
        console.log(`Footer link clicked: ${text}`);
      });
    });

    // Prevent default drag behavior on the document
    document.addEventListener('dragover', (e) => {
      e.preventDefault();
    });

    document.addEventListener('drop', (e) => {
      e.preventDefault();
    });

    // Initialize
    console.log('File Upload & Share Interface initialized');

    // Load saved app name from localStorage
    document.addEventListener('DOMContentLoaded', () => {
      console.log('DOM fully loaded');
      
      const savedAppName = localStorage.getItem('appName');
      if (savedAppName) {
        appNameInput.value = savedAppName;
      }
    });
  </script>
</body>
</html>