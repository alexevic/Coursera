<?php
  session_start();
  require_once "head.php";
  require_once "pdo.php";
  require_once "utility_functions.php";

  //from utility_functions.php
  isCancel();

  if(isset($_POST["login"]))
  {
    $salt = 'XyZzy12*_';

    // Error checking on the input data:
    if(isset($_POST["email"]) && isset($_POST["pass"]))
    {
      unset($_SESSION["email"]); //Logout current user
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
        // Repeat next 3 lines for better understanding
        $stmt = $pdo->prepare('SELECT user_id, name FROM users  WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false)
        {
          error_log("Login success ".$_POST['email']);
          $_SESSION['name'] = $row['name'];
          $_SESSION['user_id'] = $row['user_id'];
          header("Location: index.php");
          return;
        }
        else
        {
          error_log("Login fail ".$_POST['email']." $check");
          // Change error message
          $_SESSION["error"] = "Incorrect password";
          header("Location: login.php");
          return;
        }
      }
    }
  }

 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/myscripts.js"></script>
  </head>
  <body>
    <div class="container">
      <h1>Please Log In</h1>
      <?php
        flashMessage();
       ?>
      <form method="POST">
        <label for="nam"> Email </label>
        <input type="text" name="email" id="nam"><br/>
        <label for="id_1723"> Password </label>
        <input type="password" name="pass" id="id_1723"><br/>
        <input type="submit" name="login" onclick="return doValidate();" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>
  </body>
</html>
