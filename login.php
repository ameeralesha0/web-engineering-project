<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <div class="form-side">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Login to MyPetakom</h2>
  		<form method="post" action="login.php">
  		<?php include('errors.php'); ?>
		<input type="text" name="username" placeholder="Username" required>
		<input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
			<option value="">-- Select Role --</option>
			<option value="student">Student</option>
            <option value="advisor">Event Advisor</option>
            <option value="coordinator">Petakom Coordinator</option>
        </select>
  		<button type="submit" class="btn" name="login_user">Login</button>
  		<div class="links">
            <a href="register.php">Register</a> | <a href="forgotpassword.php">Forgot Password?</a>
        </div>
  		</form>
	</div>
	<div class="title-side">
        <h1>WELCOME</h1>
        <h2>to MyPetakom</h2>
    </div>
</div>
</body>
</html>

