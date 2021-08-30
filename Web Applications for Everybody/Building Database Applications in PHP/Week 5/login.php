<?php
  session_start();

  $salt = 'XyZzy12*_';
  $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';

  // Error checking on the input data:
  if(isset($_POST["email"]) && isset($_POST["pass"]))
  {
    unset($_SESSION["name"]); //Logout current user
    if((strlen($_POST["email"]) < 1) || (strlen($_POST["pass"]) < 1))
    {
      $_SESSION["error"] = "User name and password are required";
      header("Location: login.php");
      return;
    }
    else
    {
      // Generating salted hash:
      $check = hash('md5', $salt.$_POST['pass']);
      if($check == $stored_hash)
      {
        error_log("Login success ".$_POST['email']);
        $_SESSION["name"] = $_POST["email"];
        header("Location: index.php");
        return;
      }
      else
      {
        error_log("Login fail ".$_POST['email']." $check");
        $_SESSION["error"] = "Incorrect password";
        header("Location: login.php");
        return;
      }
    }
  }

 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič</title>
    <?php require_once "bootstrap.php"; ?>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="container">
      <h1>Please Log In</h1>
      <?php
        if(isset($_SESSION["error"]))
        {
          echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
          unset($_SESSION["error"]);
        }
       ?>
      <form method="POST">
        <label for="nam"> User Name </label>
        <input type="text" name="email" id="nam"><br/>
        <label for="id_1723"> Password </label>
        <input type="text" name="pass" id="id_1723"><br/>
        <input type="submit" value="Log In">
        <a href="index.php">Cancel</a></p>
      </form>
    </div>
  </body>
</html>
