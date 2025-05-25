<?php include('server.php') ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <div class="form-side1" >
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Register a New Account</h2>
  	<form method="post" action="register.php">
  		<?php include('errors.php'); ?>
  	  <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Username" required>
  	  <input type="email" name="email" value="<?php echo $email; ?>" placeholder="Email" required>
	  <select name="role" required>
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="advisor">Event Advisor</option>
        <option value="coordinator">Petakom Coordinator</option>
      </select>
  	  <input type="password" name="password_1" placeholder="Password" required>
  	  <input type="password" name="password_2" placeholder="Confirm password" required>
  	  <button type="submit" class="btn" name="reg_user">Register</button>
	</form>
  	<div class="links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
    <div class="title-side">
        <h1>WELCOME</h1>
        <p>Join the PETAKOM community today!</p>
    </div>
</div>
</body>
</html>