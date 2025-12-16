<!DOCTYPE html>

<html>
<head>
	<title>Userole</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	<style type="text/css">

button {
	overflow: visible;
}
a {
	background-color: transparent;
	-webkit-text-decoration-skip: objects;
}
a:active, a:hover {
	outline-width: 0;
}
button {
	font-family: sans-serif;
	font-size: 100%;
	line-height: 1.15;
	margin: 0;
}
button {
	text-transform: none;
}
button {
	-webkit-appearance: button;
}
button::-moz-focus-inner {
	border-style: none;
	padding: 0;
}
button:-moz-focusring {
	outline: ButtonText dotted 1px;
}

ul {
	margin-top: 0;
	margin-bottom: 1rem;
}
a {
	color: #007bff;
	text-decoration: none;
	background-color: transparent;
	-webkit-text-decoration-skip: objects;
}
a:hover {
	color: #0056b3;
	text-decoration: underline;
}
button {
	border-radius: 0;
}
button:focus {
	outline: 1px dotted;
	outline: 5px auto -webkit-focus-ring-color;
}
button {
	margin: 0;
	font-family: inherit;
	font-size: inherit;
	line-height: inherit;
}
button {
	overflow: visible;
}
button {
	text-transform: none;
}
button {
	-webkit-appearance: button;
}
button::-moz-focus-inner {
	padding: 0;
	border-style: none;
}
.btn {
	display: inline-block;
	font-weight: 400;
	text-align: center;
	white-space: nowrap;
	vertical-align: middle;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	border: 1px solid transparent;
	padding: .375rem .75rem;
	font-size: 1rem;
	line-height: 1.5;
	border-radius: .25rem;
	transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
@media screen and (prefers-reduced-motion:reduce) {
	.btn {
		transition: none;
	}
}
.btn:focus, .btn:hover {
	text-decoration: none;
}
.btn:focus {
	outline: 0;
	box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
}
.btn:disabled {
	opacity: .65;
}
.btn-primary {
	color: #fff;
	background-color: #007bff;
	border-color: #007bff;
}
.btn-primary:hover {
	color: #fff;
	background-color: #0069d9;
	border-color: #0062cc;
}
.btn-primary:focus {
	box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .5);
}
.btn-primary:disabled {
	color: #fff;
	background-color: #007bff;
	border-color: #007bff;
}
.btn-sm {
	padding: .25rem .5rem;
	font-size: .875rem;
	line-height: 1.5;
	border-radius: .2rem;
}


.card {
	position: relative;
	display: -ms-flexbox;
	display: flex;
	-ms-flex-direction: column;
	flex-direction: column;
	min-width: 0;
	word-wrap: break-word;
	background-color: #fff;
	background-clip: border-box;
	border: 1px solid rgba(0, 0, 0, .125);
	border-radius: .25rem;
}
.card-link:hover {
	text-decoration: none;
}
.card-header {
	padding: 1.5rem 1.25rem;
	margin-bottom: 0;
	background-color: rgba(0, 0, 0, .03);
	border-bottom: 1px solid rgba(0, 0, 0, .125);
}
.card-header:first-child {
	border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
}
.list-group {
	display: -ms-flexbox;
	display: flex;
	-ms-flex-direction: column;
	flex-direction: column;
	padding-left: 0;
	margin-bottom: 0;
}
.list-group-item {
	position: relative;
	display: block;
	padding: .75rem 1.25rem;
	margin-bottom: -1px;
	background-color: #fff;
	border: 1px solid rgba(0, 0, 0, .125);
}
.list-group-item:first-child {
	border-top-left-radius: .25rem;
	border-top-right-radius: .25rem;
}
.list-group-item:last-child {
	margin-bottom: 0;
	border-bottom-right-radius: .25rem;
	border-bottom-left-radius: .25rem;
}
.list-group-item:focus, .list-group-item:hover {
	z-index: 1;
	text-decoration: none;
}
.list-group-item:disabled {
	color: #6c757d;
	background-color: #fff;
}
.align-middle {
	vertical-align: middle!important;
}
.float-right {
	float: right!important;
}
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@600&display=swap');
body{
	font-family: 'Open Sans', sans-serif;
	font-size: 20px;
	padding:2% 10% 5% 10%;
}

.pure-material-button-contained {
    position: relative;
    display: inline-block;
    box-sizing: border-box;
    border: none;
    border-radius: 4px;
    padding: 0 16px;
    min-width: 64px;
    height: 36px;
    vertical-align: middle;
    text-align: center;
    text-overflow: ellipsis;
    text-transform: uppercase;
    color: rgb(var(--pure-material-onprimary-rgb, 255, 255, 255));
    background-color: rgb(var(--pure-material-primary-rgb, 33, 150, 243));
    box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
    font-family: var(--pure-material-font, "Roboto", "Segoe UI", BlinkMacSystemFont, system-ui, -apple-system);
    font-size: 14px;
    font-weight: 500;
    line-height: 36px;
    overflow: hidden;
    outline: none;
    cursor: pointer;
    transition: box-shadow 0.2s;
}

.pure-material-button-contained::-moz-focus-inner {
    border: none;
}

/* Overlay */
.pure-material-button-contained::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgb(var(--pure-material-onprimary-rgb, 255, 255, 255));
    opacity: 0;
    transition: opacity 0.2s;
}

/* Ripple */
.pure-material-button-contained::after {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    border-radius: 50%;
    padding: 50%;
    width: 32px; /* Safari */
    height: 32px; /* Safari */
    background-color: rgb(var(--pure-material-onprimary-rgb, 255, 255, 255));
    opacity: 0;
    transform: translate(-50%, -50%) scale(1);
    transition: opacity 1s, transform 0.5s;
}

/* Hover, Focus */
.pure-material-button-contained:hover,
.pure-material-button-contained:focus {
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.14), 0 1px 10px 0 rgba(0, 0, 0, 0.12);
}

.pure-material-button-contained:hover::before {
    opacity: 0.08;
}

.pure-material-button-contained:focus::before {
    opacity: 0.24;
}

.pure-material-button-contained:hover:focus::before {
    opacity: 0.3;
}

/* Active */
.pure-material-button-contained:active {
    box-shadow: 0 5px 5px -3px rgba(0, 0, 0, 0.2), 0 8px 10px 1px rgba(0, 0, 0, 0.14), 0 3px 14px 2px rgba(0, 0, 0, 0.12);
}

.pure-material-button-contained:active::after {
    opacity: 0.32;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0s;
}

/* Disabled */
.pure-material-button-contained:disabled {
    color: rgba(var(--pure-material-onsurface-rgb, 0, 0, 0), 0.38);
    background-color: rgba(var(--pure-material-onsurface-rgb, 0, 0, 0), 0.12);
    box-shadow: none;
    cursor: initial;
}

.pure-material-button-contained:disabled::before {
    opacity: 0;
}

.pure-material-button-contained:disabled::after {
    opacity: 0;
}
</style>

</head>

<body>

		
	<div class="card">
		<div id="accordion">
			<div class="card">

				<div class="card-header">
					<tg onclick="history.back();" >
	<img src="back-black.png" style="height: 40px;width: 40px;margin-top: -5px;
    position: absolute;">
</tg>
					<a class="card-link" style="margin-left: 50px;">Manage User Access For </a>
					<span id="acrd" style="color: black"></span>
					<button class="pure-material-button-contained" style="float: right;" onclick="upd()">Update</button>
				</div>


				<div class="collapse show" data-parent="#accordion" id="#collapse_0">
					<ul class="list-group">

						<?php
						include '../config.php';
						$nvn='navigation';
						$sql = "SELECT * FROM $nvn order by created_at DESC";
    					$result = mysqli_query($db, $sql) or die(mysqli_error($db));
    					while ($r = mysqli_fetch_assoc($result))
    					{	
    					?>
						<li class="list-group-item"><span><?php echo $r['nav']; ?></span> 
						<input style="float: right;height: 25px;width: 35px;" type="checkbox" class="toggle-box" name="<?php echo $r['nav']; ?>">
						</li>
						<?php
						}
						?>



					</ul>
				</div>
			</div>

		</div>
	</div>

	<script type="text/javascript">
	 var hgh = JSON.parse(localStorage.getItem('tmprol'));
	 document.getElementById('acrd').innerText = hgh.name
	
	 $.get("api.php?shid=true&id="+hgh.id, function(data, status){

    var jll = JSON.parse( data[0].access);
    if (jll.modules) {
      ec(jll.modules);
    } else {
      ec(jll);
    }

  },"json");

	 function ec(sgh){
	 	for (var i = sgh.length - 1; i >= 0; i--) {
	 		
	 		$('.list-group').find('input').each(function() {
            // addata[this.name] = $(this).val();
            if(sgh[i] == this.name){
            	this.checked = true
            }
        });

	 	};
	 	

	 }
	 


	 function upd(){
	var ah = []
$('.list-group').find('input').each(function() {
	if(this.checked == true){
		console.log(this.name);
		ah.push(this.name);
	}
})
console.log(ah);
var access = JSON.stringify({modules: ah, groups: [], roles: []});
var yu = "api.php?updatrole=true&id="+hgh.id+"&ah="+access
console.log(yu);
$.get(yu, function(data, status){

    console.log(data);
    window.top.postMessage('success^User Role Updated', '*')
    window.history.back();

  },"json");


}







	</script>

</body>
</html>