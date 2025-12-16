<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin</title>

  <!-- Favicon -->
  <link rel="icon" href="https://appsthink.com/fav.png" sizes="64x64" />
  <link rel="preload" as="image" href="logo.png" />
  <link rel="preload" as="image" href="https://appsthink.com/fav.png" />

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/notie.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="modalstyle.css" />

  <style>
    /* Top bar alignment only */
    ._14IZ- {
      display: flex;
      align-items: center;
      width: 100%;
    }
    
    ._1XpNO.izfMl {
      display: flex;
      align-items: center;
      width: 100%;
      justify-content: space-between;
    }
    
    ._5PQRU {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    #app {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    ._3rEQk {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .containerz {
      flex: 1;
      min-height: calc(100vh - 200px);
    }
    ._31VCP {
      margin-top: auto;
    }
    .notie-container {
      z-index: 999999999;
      box-shadow: none;
    }

    /* Make App Market button visible on mobile */
    @media (max-width: 61.98rem) {
      ._1sUiN._3hobi#mrket {
        display: flex !important;
      }
    }

    .progress {
      position: fixed;
      height: 4px;
      width: 100%;
      background-color: #ace8bc;
      border-radius: 2px;
      z-index: 99999;
      overflow: hidden;
    }
    .progress .determinate {
      transition: width 0.3s linear;
    }
    .progress .indeterminate {
      background-color: #489cc4;
    }
    .progress .indeterminate:before,
    .progress .indeterminate:after {
      content: '';
      position: absolute;
      top: 0;
      bottom: 0;
      background-color: inherit;
      will-change: left, right;
    }
    .progress .indeterminate:before {
      left: 0;
      animation: indeterminate 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
    }
    .progress .indeterminate:after {
      left: 0;
      animation: indeterminate-short 2.1s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
      animation-delay: 1.15s;
    }
    @keyframes indeterminate {
      0% { left: -35%; right: 100%; }
      60%,100% { left: 100%; right: -90%; }
    }
    @keyframes indeterminate-short {
      0% { left: -200%; right: 100%; }
      60%,100% { left: 107%; right: -8%; }
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="_1fqjz">
      <div class="_14IZ-">
        <div class="_1XpNO izfMl" id="apnd">
          <a href="/"><img height="39" width="91" src="logo.png" loading="eager" alt="Logo"></a>
          <div class="_5PQRU" role="navigation">
            <!-- Navigation items will be added by JavaScript -->
          </div>
        </div>
      </div>

      <div class="_3rEQk">
        <?php include 'sidebar.html'; ?>
        <div id="prg" class="progress"><div class="indeterminate"></div></div>
      </div>

      <script>
        // Define iframeLoaded function globally before iframe loads
        function iframeLoaded() {
          window.onmessage = function (e) {
            if (e.data === 'responseact') {
              irun();
            } else if (!isNaN(e.data)) {
              iruncust(e.data);
            } else if (typeof e.data === 'string') {
              if (e.data.includes("^")) ialert(e.data);
              if (e.data.includes("~")) {
                let qkey = e.data.split("~");
                let urni = 'https://appsthink.com/appmarket/repo/' + qkey[1] + '/' + qkey[1] + '.zip';

                $.post("./plugins/push.php", { dest: urni, appn: qkey[1] }, function (res) {
                  let sc = JSON.parse(res);
                  if (sc.response === 'extracted') {
                    window.top.postMessage('success^Plugin Installed', '*');
                    window.location.reload();
                  }
                });
              }
            }
          };
        }

        function ialert(msg) {
          let [type, text] = msg.split("^");
          notie.alert({ type, text, stay: false });
        }

        function irun() {
          const frame = document.getElementById('themeframe');
          if (frame) {
            const height = frame.contentWindow.document.body.scrollHeight + 30;
            frame.style.height = height + "px";
            frame.style.width = screen.width + "px";
            document.getElementById('prg').style.display = 'none';
          }
        }

        function iruncust(height) {
          const frame = document.getElementById('themeframe');
          if (frame) {
            frame.style.height = height + "px";
            frame.style.width = screen.width + "px";
            document.getElementById('prg').style.display = 'none';
          }
        }
      </script>

      <iframe
        id="themeframe"
        class="containerz"
        onload="iframeLoaded();"
        loading="lazy"
        referrerpolicy="no-referrer"
        sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-top-navigation allow-modals allow-popups-to-escape-sandbox"
        style="width:100%; display:block; border:0;"
        title="Main Content Frame"
      ></iframe>

      <div class="_31VCP">
        <div class="_1XpNO S_k2f">
          <div class="_2rh-B">
            <h2 class="_11tU4">Need More?</h2>
            <p class="_249s1">We can deliver what you need.</p>
          </div>
          <div class="_1KzjG"><a href="#" class="_3_Ozh _7ei3C zYyzg _2IHdo">Learn more</a></div>
        </div>
      </div>

      <section class="_3dP95">
        <!-- Footer -->
        <!-- KEEP SAME as original: Footer structure is lightweight and already optimized -->
      </section>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="myModal" style="z-index:55555">
    <div class="modal-content">
      <button id="clss" style="position: absolute; top: 16px; right: 45px; width: 40px; height: 40px; background-color: #ef4444; color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
      </button>
      <iframe src="appmarket/" onload="iframeLoaded();" style="width:100%; height:100%;" frameborder="0"></iframe>
    </div>
  </div>

  <!-- JS Scripts (Defer everything non-blocking) -->
  <script src="assets/js/jquery.min.js" defer></script>
  <script src="modalscript.js" defer></script>
  <script src="https://unpkg.com/notie" defer></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const userData = localStorage.getItem('userdat');

      if (!userData) {
        notie.alert({ type: 'error', text: 'Login Failed. Please Login Again', stay: true });
        window.location.href = './login/';
        return;
      }

      const user = JSON.parse(userData);

      const identElement = document.getElementById('ident');
      if (identElement) {
        identElement.innerText = user.email;
      }

      const apnd = document.getElementById('apnd');
      // Check for admin status in multiple possible properties
      const isAdmin = user.role === 'admin' || user.isAdmin === true;
      const nameTag = `<span class="_3gKg- _3mdaE">Signed in as: ${user.name}</span>`;
      const logoutBtn = `<a class="_1sUiN qn2E4" id="logut" onclick="logu()">Logout <img src="logut.png" loading="lazy" style="height:20px;width:20px;margin-left:5px;"></a>`;
      
      // Add user info and logout button to the navigation
      let nav = apnd.querySelector('._5PQRU');
      
      if (!nav) {
        console.log('Creating new nav element');
        nav = document.createElement('div');
        nav.className = '_5PQRU';
        nav.setAttribute('role', 'navigation');
        apnd.appendChild(nav);
      }
      
      if (isAdmin) {
        nav.innerHTML = `
          <a class="_1sUiN _3hobi qn2E4" id="mrket" href="#">App Market</a>
          ${nameTag}
          ${logoutBtn}
        `;
      } else {
        nav.innerHTML = `
          ${nameTag}
          ${logoutBtn}
        `;
      }
      
      // Add to DOM if not already there
      if (!nav.parentNode) {
        apnd.appendChild(nav);
      }

      // Use event delegation for dynamically added elements
      document.addEventListener('click', function(event) {
        // Handle App Market link click
        if (event.target && event.target.id === 'mrket') {
          event.preventDefault();
          document.getElementById("myModal").style.display = "block";
        }
        // Handle close button click
        if (event.target && (event.target.id === 'clss' || event.target.closest('#clss'))) {
          event.preventDefault();
          document.getElementById("myModal").style.display = "none";
        }
      });
    });

    function logu() {
      localStorage.clear();
      window.location.href = './login';
    }
  </script>
</body>
</html>
