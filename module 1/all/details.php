<form method="post">
  Name: <input type="text" name="name">
  Age: <input type="number" name="age">
  Email address: <input type="email" name="email">
  <input type="submit" name="submit1" value="Submit">
</form>

<?php
if (isset($_POST['submit1'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $email = $_POST['email'];

    echo "<br><hr>";
    echo "Welcome $name<br>";
    echo "You are $age years old<br>";
    echo "Your email address $email";
}
?>
