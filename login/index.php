<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log in - BillionERP</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://code.jquery.com/jquery-3.6.0.min.js" as="script">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    
    <!-- Inline critical CSS -->
    <style>
        /* Critical CSS */
        body, html { 
            margin: 0; 
            padding: 0; 
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        #login {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 1.5em;
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #target {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 10px;
        }
        #target:hover {
            background-color: #2980b9;
        }
        .logo {
            display: block;
            width: 120px;
            height: auto;
            margin: 0 auto 20px;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .notie-container {
            z-index: 999999999;
            box-shadow: none;
        }
    </style>
    
    <!-- Load non-critical CSS asynchronously -->
    <link rel="preload" href="https://unpkg.com/notie/dist/notie.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://unpkg.com/notie/dist/notie.min.css"></noscript>
    
    <!-- Inline minimal JavaScript for initial interaction -->
    <script>
        // Function to handle form submission
        function handleSubmit() {
            const form = document.forms[0];
            const formData = new FormData(form);
            const values = {};
            
            formData.forEach((value, key) => {
                if (value) values[key] = value;
            });
            
            // Show loading state
            const submitBtn = document.getElementById('target');
            const originalText = submitBtn.value;
            submitBtn.value = 'Logging in...';
            submitBtn.disabled = true;
            
            // Send login request
            fetch('api/userapi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(values)
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    localStorage.setItem("userdat", JSON.stringify(data[0]));
                    if (window.notie) {
                        notie.alert({ type: 'success', text: 'Login successful.', stay: false });
                    }
                    window.location.href = "../";
                } else {
                    if (window.notie) {
                        notie.alert({ type: 'error', text: 'Login failed. Please check your credentials.', stay: false });
                    } else {
                        alert('Login failed. Please check your credentials.');
                    }
                    submitBtn.value = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.notie) {
                    notie.alert({ type: 'error', text: 'An error occurred. Please try again.', stay: false });
                } else {
                    alert('An error occurred. Please try again.');
                }
                submitBtn.value = originalText;
                submitBtn.disabled = false;
            });
            
            return false;
        }
        
        // Load non-critical resources after page load
        function loadDeferredResources() {
            // Load jQuery if not already loaded
            if (!window.jQuery) {
                var script = document.createElement('script');
                script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
                script.integrity = 'sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=';
                script.crossOrigin = 'anonymous';
                document.head.appendChild(script);
            }
            
            // Load notie if not already loaded
            if (!window.notie) {
                var notieCss = document.createElement('link');
                notieCss.rel = 'stylesheet';
                notieCss.href = 'https://unpkg.com/notie/dist/notie.min.css';
                document.head.appendChild(notieCss);
                
                var notieJs = document.createElement('script');
                notieJs.src = 'https://unpkg.com/notie';
                document.head.appendChild(notieJs);
            }
        }
        
        // Add event listeners when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set up form submission
            var form = document.forms[0];
            if (form) {
                form.onsubmit = handleSubmit;
            }
            
            // Load non-critical resources after a short delay
            if (requestIdleCallback) {
                requestIdleCallback(loadDeferredResources);
            } else {
                setTimeout(loadDeferredResources, 1000);
            }
        });
    </script>
</head>
<body>
    <div id="login">
        <div style="text-align: center">
            <img 
                src="https://media.licdn.com/dms/image/v2/C4D0BAQGeBVOzN-zYEw/company-logo_200_200/company-logo_200_200/0/1630479283732/appsthink_logo?e=2147483647&v=beta&t=mLajjRHltAyP0wckez65YppObm5BsiIrwpHa-NuhSVA" 
                alt="BillionERP Logo" 
                class="logo"
                width="120"
                height="120"
                loading="eager"
                style="max-width: 100%; height: auto; border-radius: 50%;"
            >
        </div>
        <h1><strong>Welcome to BillionERP.</strong><br>Please login.</h1>

        <form onsubmit="return handleSubmit();" method="post">
            <input type="hidden" name="login" value="true">
            <input 
                type="email" 
                name="name" 
                placeholder="Enter your Email" 
                required 
                aria-label="Email address"
                autocomplete="username"
            >
            <input 
                type="password" 
                name="password" 
                placeholder="Enter Password" 
                required 
                aria-label="Password"
                autocomplete="current-password"
            >
            <p style="text-align: right; margin: 5px 0 15px 0;">
                <a href="#" style="font-size: 0.9em;">Forgot Password?</a>
            </p>
            <input 
                type="submit" 
                id="target" 
                value="Login"
                aria-label="Login to your account"
            >
            <p style="text-align: center; margin-top: 15px; font-size: 0.9em;">
                Not a member yet? <a href="./signup">Register here.</a>
            </p>
        </form>
    </div>
    
</body>
</html>
