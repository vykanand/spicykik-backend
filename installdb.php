<?php  

?>

<!DOCTYPE html>
<html>
<head>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <title>Install</title>
  <style type="text/css">
.form__group {
  position: relative;
  padding: 15px 0 0;
  margin-top: 10px;
  width: 50%;
}

.form__field {
  font-family: inherit;
  width: 100%;
  border: 0;
  border-bottom: 2px solid #9b9b9b;
  outline: 0;
  font-size: 1.3rem;
  color: #fff;
  padding: 7px 0;
  background: transparent;
  transition: border-color 0.2s;
}
.form__field::placeholder {
  color: transparent;
}
.form__field:placeholder-shown ~ .form__label {
  font-size: 1.3rem;
  cursor: text;
  top: 20px;
}

.form__label {
  position: absolute;
  top: 0;
  display: block;
  transition: 0.2s;
  font-size: 1rem;
  color: #9b9b9b;
}

.form__field:focus {
  padding-bottom: 6px;
  font-weight: 700;
  border-width: 3px;
  border-image: linear-gradient(to right, #11998e, #38ef7d);
  border-image-slice: 1;
}
.form__field:focus ~ .form__label {
  position: absolute;
  top: 0;
  display: block;
  transition: 0.2s;
  font-size: 1rem;
  color: #11998e;
  font-weight: 700;
}

/* reset input */
.form__field:required, .form__field:invalid {
  box-shadow: none;
}

/* demo */
body {
  font-family: "Poppins", sans-serif;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  font-size: 1.5rem;
  background-color: #0D104D;
}
  </style>
</head>
<body>
<h1 style="color: white">Database Configuration</h1>
<div class="form__group field">
  <input type="input" class="form__field" placeholder="Name" name="name" id="hostname" required />
  <label for="name" class="form__label">DB Hostname</label>
</div>

<div class="form__group field">
  <input type="input" class="form__field" placeholder="Name" name="name" id="user" required />
  <label for="name" class="form__label">DB Username</label>
</div>

<div class="form__group field">
  <input type="input" class="form__field" placeholder="Name" name="name" id="pass" required />
  <label for="name" class="form__label">DB Password</label>
</div>

<div class="form__group field">
  <input type="input" class="form__field" placeholder="Name" name="name" id="dbn" required />
  <label for="name" class="form__label">DB Name</label>
</div>

<button type="submit" style="margin-top: 5%;padding: 1%;width: 15%;background: #259399;font-size: 25px;color: white;border-color: aqua;" onclick="inst()">Install</button>

<script type="text/javascript">
  function inst (){
  var hst = $('#hostname').val();
  var usrr = $('#user').val();
  var pss = $('#pass').val();
  var dbn = $('#dbn').val();

if(hst.length > 1 && usrr.length > 1 && pss.length > 1 && dbn.length > 1){

  $.get("dbhook.php?host="+hst+"&username="+usrr+"&password="+pss+"&dbname="+dbn, function(data, status){
    alert(JSON.parse(data).dbhook);
    window.location.href = '../admin'
  });
  
  }else {
    alert('Please Enter Correct Details.')
  }

  }
</script>
</body>
</html>