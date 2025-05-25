<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('127.0.0.1:3307', 'root', '', 'mypetakom');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $id = mysqli_real_escape_string($db, $_POST['id']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($name)) { array_push($errors, "Name is required"); }
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password)) { array_push($errors, "Password is required"); }
  if (empty($id)) { array_push($errors, "ID is required"); }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
$name = mysqli_real_escape_string($db, $_POST['name']);
$username = mysqli_real_escape_string($db, $_POST['username']);
$email = mysqli_real_escape_string($db, $_POST['email']);
$role = mysqli_real_escape_string($db, $_POST['role']);
$id = mysqli_real_escape_string($db, $_POST['id']);

  if ($role === 'student') {
    $check_query = "SELECT * FROM student WHERE student_username='$username' OR student_email='$email' OR student_id = '$id'  LIMIT 1";
} elseif ($role === 'advisor') {
    $check_query = "SELECT * FROM advisor WHERE advisor_username='$username' OR advisor_email='$email' OR advisor_id = '$id' LIMIT 1";
} elseif ($role === 'coordinator') {
    $check_query = "SELECT * FROM petakom_coordinator WHERE staff_username='$username' OR staff_email='$email' OR staff_id = '$id' LIMIT 1";
} else {
    array_push($errors, "Invalid user role selected");
}

if (!empty($check_query)) {
    $result = mysqli_query($db, $check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Check based on role-specific fields
        if (
            ($role === 'student' && $user['student_username'] === $username) ||
            ($role === 'advisor' && $user['advisor_username'] === $username) ||
            ($role === 'coordinator' && $user['staff_username'] === $username)
        ) {
            array_push($errors, "Username already exists");
        }

        if (
            ($role === 'student' && $user['student_email'] === $email) ||
            ($role === 'advisor' && $user['advisor_email'] === $email) ||
            ($role === 'coordinator' && $user['staff_email'] === $email)
        ) {
            array_push($errors, "Email already exists");
        }

        if (
            ($role === 'student' && $user['student_id'] === $id) ||
            ($role === 'advisor' && $user['advisor_id'] === $id) ||
            ($role === 'coordinator' && $user['staff_id'] === $id)
        ) {
            array_push($errors, "ID already exists");
        }
    }
}

  // Finally, register user if there are no errors in the form
$username = mysqli_real_escape_string($db, $_POST['username']);
$email = mysqli_real_escape_string($db, $_POST['email']);
$password = mysqli_real_escape_string($db, $_POST['password']);
$role = mysqli_real_escape_string($db, $_POST['role']);

if (count($errors) == 0) {
    $password = md5($password); // use password_hash() in future

    if ($role === 'student') {
        $query = "INSERT INTO student (student_username, student_email, student_pass) 
                  VALUES ('$username', '$email', '$password')";
    } elseif ($role === 'advisor') {
        $query = "INSERT INTO advisor (advisor_username, advisor_email, advisor_pass) 
                  VALUES ('$username', '$email', '$password')";
    } elseif ($role === 'coordinator') {
        $query = "INSERT INTO petakom_coordinator (staff_username, staff_email, staff_pass) 
                  VALUES ('$username', '$email', '$password')";
    } else {
        array_push($errors, "Invalid role selected");
    }

    if (!empty($query)) {
        mysqli_query($db, $query);
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['success'] = "You are now registered";

        // Redirect based on role
        if ($role === 'student') {
            header('location: login.php');
        } elseif ($role === 'advisor') {
            header('location: login.php');
        } elseif ($role === 'coordinator') {
            header('location: login.php');
        }
        exit();
    }
}

}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $password = md5($password); // consider upgrading to password_hash() in future

    // Check student table
    $query_student = "SELECT * FROM student WHERE student_username='$username' AND student_pass='$password'";
    $result_student = mysqli_query($db, $query_student);

    // Check advisor table
    $query_advisor = "SELECT * FROM advisor WHERE advisor_username='$username' AND advisor_pass='$password'";
    $result_advisor = mysqli_query($db, $query_advisor);

    // Check coordinator table
    $query_coordinator = "SELECT * FROM petakom_coordinator WHERE staff_username='$username' AND staff_pass='$password'";
    $result_coordinator = mysqli_query($db, $query_coordinator);

    if (mysqli_num_rows($result_student) == 1) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'student';
        $_SESSION['success'] = "You are now logged in as Student";
        header('location: studentpage.php');

    } elseif (mysqli_num_rows($result_advisor) == 1) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'advisor';
        $_SESSION['success'] = "You are now logged in as Advisor";
        header('location: advisorpage.php');

    } elseif (mysqli_num_rows($result_coordinator) == 1) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'coordinator';
        $_SESSION['success'] = "You are now logged in as Coordinator";
        header('location: coordinatorpage.php');

    } else {
        array_push($errors, "Wrong username/password combination");
    }
}

}

?>