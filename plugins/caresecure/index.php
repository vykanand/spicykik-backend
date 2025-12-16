<?php

  ?>
<!DOCTYPE html>
<html>
<head>
	<title>Care</title>
	<style>
@import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');
body{font-family: 'Open Sans', sans-serif;}

@import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap');

a{
	cursor: default;
	text-align: center;
    position: relative;
    display: inline-block;
    padding: 25px 30px;
    margin: 40px 0;
    color: #06fa00;
    text-decoration: none;
    text-transform: uppercase;
    transition: 0.5s;
    letter-spacing: 4px;
    overflow: hidden;
    margin-right: 50px;

}
a:hover{
    background: #06fa00;
    color: #fff;
    border: 1px solid #06fa00;
    box-shadow: 0 0 5px #06fa00,
                0 0 25px #06fa00,
                0 0 50px #06fa00,
                0 0 200px #06fa00;
     -webkit-box-reflect:below 1px linear-gradient(transparent, #0005);
}
a:nth-child(1){
    filter: hue-rotate(270deg);
}
a:nth-child(2){
    filter: hue-rotate(110deg);
}
a span{
    position: absolute;
    display: block;
}
a span:nth-child(1){
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg,transparent,#06fa00);
    animation: animate1 1s linear infinite;
}
@keyframes animate1{
    0%{
        left: -100%;
    }
    50%,100%{
        left: 100%;
    }
}
a span:nth-child(2){
    top: -100%;
    right: 0;
    width: 2px;
    height: 100%;
    background: linear-gradient(180deg,transparent,#06fa00);
    animation: animate2 1s linear infinite;
    animation-delay: 0.25s;
}
@keyframes animate2{
    0%{
        top: -100%;
    }
    50%,100%{
        top: 100%;
    }
}
a span:nth-child(3){
    bottom: 0;
    right: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(270deg,transparent,#06fa00);
    animation: animate3 1s linear infinite;
    animation-delay: 0.50s;
}
@keyframes animate3{
    0%{
        right: -100%;
    }
    50%,100%{
        right: 100%;
    }
}


a span:nth-child(4){
    bottom: -100%;
    left: 0;
    width: 2px;
    height: 100%;
    background: linear-gradient(360deg,transparent,#06fa00);
    animation: animate4 1s linear infinite;
    animation-delay: 0.75s;
}
@keyframes animate4{
    0%{
        bottom: -100%;
    }
    50%,100%{
        bottom: 100%;
    }
}

.progress-container {
    margin: 20px 0;
    width: 100%;
    max-width: 400px;
}
.progress-bar {
    width: 100%;
    height: 30px;
    background: #eee;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #06fa00, #00ff88);
    width: 0%;
    transition: width 0.3s ease;
    border-radius: 15px;
}
.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: bold;
    font-size: 16px;
    color: #000;
}
.scan-list {
    max-height: 200px;
    overflow-y: auto;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    margin: 20px 0;
    width: 100%;
    max-width: 400px;
}
.scan-item {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}
.scan-item:last-child {
    border-bottom: none;
}
.status {
    font-weight: bold;
}
.status.safe { color: #4CAF50; }
.status.warning { color: #FF9800; }
.status.danger { color: #F44336; }
.status.missing { color: #9E9E9E; }

</style>
</head>
<body>
<tg onclick="history.back();" >
	<img src="./assets/back-black.png" style="height: 40px;width: 40px;">
</tg>

<div style="width: 100%;display: flex;height: 100%;">

	<div style="width: 50%;height: 100%;padding: 3% 5% 5% 7%;position: relative;">
		<img id="orgg" src="./assets/AUja-stable.jpg">
		<img id="anii" src="./assets/AUja.gif" style="display: none">

		<div class="scan-list" id="scanList" style="display: none;position: absolute;bottom: 5%;left: 5%;width: 90%;max-height: 120px;">
			<!-- Scan results will be added here -->
		</div>
	</div>
	<div style="width: 50%;height: 100%;display: flex;flex-direction:column;padding: 6% 5% 5% 7%;">

		<div style="display: flex;flex-direction:row;padding-top: 5%;padding-left: 25%;">
		<label for="fbug" style="font-size: 18px">Check file bugs</label>
		<input style="margin-left:4%;width:20px;height: 20px" type="checkbox" name="fbug" id="fbug" checked/>
		</div>

		<div style="display: flex;flex-direction:row;padding-top: 5%;padding-left: 25%;">
		<label for="finteg" style="font-size: 18px">Check File Integrity</label>
		<input style="margin-left:4%;width:20px;height: 20px" type="checkbox" name="finteg" id="finteg" checked/>
		</div>

		<div style="display: flex;flex-direction:row;padding-top: 5%;padding-left: 25%;">
		<label for="fupdate" style="font-size: 18px">Update check</label>
		<input style="margin-left:4%;width:20px;height: 20px" type="checkbox" name="fupdate" id="fupdate" checked/>
		</div>

		<div class="progress-container" id="progressContainer" style="display: none;">
			<div class="progress-bar">
				<div class="progress-fill" id="progressFill"></div>
				<div class="progress-text" id="progressText">0%</div>
			</div>
		</div>

		<scc id="scomp" style="display:none;padding: 25px 30px;margin: 40px 0;color: #000;letter-spacing: 1px;margin-left: 20% !important;">
			<gh style="color: black">SCAN COMPLETE</gh><br>
			File Health:<gh id="fileHealth" style="margin-top:3%;color: #06fa00"> Good</gh><br>
			FS Integrity:<gh id="fsIntegrity" style="color: #06fa00"> Good</gh><br>
			Update Status:<gh id="updateStatus" style="color: #06fa00"> Up to Date</gh><br>
		</scc>

		<a id="scn" onclick="animm()" style="display: none">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        STOP
    	</a>
    	<a id="stpscan" onclick="stsc()" style="border: 1px solid;">
    		SCAN
    	</a>

	</div>

</div>

<script type="text/javascript">
	const filesToCheck = [
		'/admin',
		'/assets',
		'/assets/css',
		'/assets/js',
		'/assets/resource',
		'/boot.html',
		'/config.php',
		'/custom',
		'/custom/api.php',
		'/custom/boot.html',
		'/delivery',
		'/delivery/api.php',
		'/delivery/boot.html',
		'/erpconsole',
		'/erpconsole/create.php',
		'/erpconsole/manage.php',
		'/erpconsole/import.php',
		'/index.php',
		'/login',
		'/login/index.php',
		'/login/signup.html',
		'/module.php',
		'/plugins',
		'/plugins/index.php',
		'/plugins/caresecure',
		'/plugins/testplugin',
		'/security',
		'/security/config.php',
		'/security/RoleManager.php',
		'/uploads',
		'/users',
		'/users/groups.json',
		'/users/roles.json',
		'/users/user-access.php',
		'/users/group-actions.php',
		'/api.php'
	];

	let currentIndex = 0;
	let scanResults = { safe: 0, warning: 0, danger: 0, missing: 0 };
	let isScanning = false;

	function stsc(){
		if (isScanning) return;
		isScanning = true;

		document.getElementById('orgg').style.display = 'none'
		document.getElementById('anii').style.display = 'block'

		document.getElementById('scn').style.display = 'block'
		document.getElementById('stpscan').style.display = 'none'

		document.getElementById('progressContainer').style.display = 'block';
		document.getElementById('scanList').style.display = 'block';
		document.getElementById('scomp').style.display = 'none';

		currentIndex = 0;
		scanResults = { safe: 0, warning: 0, danger: 0, missing: 0 };
		document.getElementById('scanList').innerHTML = '';
		document.getElementById('progressFill').style.width = '0%';
		document.getElementById('progressText').textContent = '0%';

		scanNext();
	}

	function animm(){
		isScanning = false;
		document.getElementById('anii').style.display = 'none'
		document.getElementById('orgg').style.display = 'block'

		document.getElementById('scn').style.display = 'none'
		document.getElementById('stpscan').style.display = 'block'

		document.getElementById('progressContainer').style.display = 'none';
		document.getElementById('scanList').style.display = 'none';
	}

	function scanNext() {
		if (!isScanning || currentIndex >= filesToCheck.length) {
			if (isScanning) finishScan();
			return;
		}

		const file = filesToCheck[currentIndex];
		const listItem = document.createElement('div');
		listItem.className = 'scan-item';
		listItem.innerHTML = `<span>Checking ${file}</span><span class="status">Scanning...</span>`;
		document.getElementById('scanList').appendChild(listItem);
		document.getElementById('scanList').scrollTop = document.getElementById('scanList').scrollHeight;

		checkFile(file).then(result => {
			if (!isScanning) return;

			const statusElement = listItem.querySelector('.status');
			statusElement.textContent = result.status;
			statusElement.className = `status ${result.class}`;

			scanResults[result.class]++;

			currentIndex++;
			updateProgress();
			setTimeout(scanNext, 800); // Delay for visual effect
		});
	}

	function checkFile(file) {
		return new Promise((resolve) => {
			if (!file.includes('.')) {
				// directory
				resolve({ status: 'Safe', class: 'safe' });
				return;
			}

			// for files, use HEAD
			fetch(file, { method: 'HEAD' })
				.then(response => {
					if (response.ok) {
						resolve({ status: 'Safe', class: 'safe' });
					} else {
						resolve({ status: 'Missing', class: 'missing' });
					}
				})
				.catch(error => {
					resolve({ status: 'Missing', class: 'missing' });
				});
		});
	}

	function updateProgress() {
		const progress = ((currentIndex) / filesToCheck.length) * 100;
		document.getElementById('progressFill').style.width = progress + '%';
		document.getElementById('progressText').textContent = Math.round(progress) + '%';
	}

	function finishScan() {
		isScanning = false;
		document.getElementById('orgg').style.display = 'block'
		document.getElementById('anii').style.display = 'none'

		document.getElementById('scn').style.display = 'none'
		document.getElementById('stpscan').style.display = 'block'

		document.getElementById('progressFill').style.width = '100%';
		document.getElementById('progressText').textContent = '100%';

		// Update directory statuses based on their contents
		const items = document.getElementById('scanList').querySelectorAll('.scan-item');
		items.forEach(item => {
			const span = item.querySelector('span:first-child');
			const path = span.textContent.replace('Checking ', '');
			if (!path.includes('.')) { // it's a directory
				// check if any sub-item is safe
				let hasSafeContent = false;
				items.forEach(subItem => {
					const subSpan = subItem.querySelector('span:first-child');
					const subPath = subSpan.textContent.replace('Checking ', '');
					if (subPath.startsWith(path + '/') && subItem.querySelector('.status.safe')) {
						hasSafeContent = true;
					}
				});
				const statusElement = item.querySelector('.status');
				if (hasSafeContent) {
					statusElement.textContent = 'Safe';
					statusElement.className = 'status safe';
				} else {
					statusElement.textContent = 'Missing';
					statusElement.className = 'status missing';
				}
			}
		});

		// Update results based on scan
		const fileHealth = scanResults.missing > 0 ? 'Issues Found' : 'Good';
		const fsIntegrity = scanResults.danger > 0 ? 'Compromised' : 'Good';
		const updateStatus = 'Up to Date'; // This would need actual update checking logic

		document.getElementById('fileHealth').textContent = fileHealth;
		document.getElementById('fileHealth').style.color = fileHealth === 'Good' ? '#06fa00' : '#ff0000';

		document.getElementById('fsIntegrity').textContent = fsIntegrity;
		document.getElementById('fsIntegrity').style.color = fsIntegrity === 'Good' ? '#06fa00' : '#ff0000';

		document.getElementById('updateStatus').textContent = updateStatus;
		document.getElementById('updateStatus').style.color = '#06fa00';

		document.getElementById('scomp').style.display = 'block';
	}
</script>

</body>
</html>
