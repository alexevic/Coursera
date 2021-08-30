<?php
  // If cancel button is pressed
  if ( isset($_POST['cancel'] ) )
  {
      // Redirect the browser to index.php
      header("Location: index.php");
      return;
  }

  $salt = 'XyZzy12*_';                                  // Given values
  $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';

  $error = false; // If we have no POST data

  // Error checking on the input data:
  if ( isset($_POST['who']) && isset($_POST['pass']))
  {
    if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 )
    {
      $error = "User name and password are required"; // If error is found
    }
    else
    {
      // Generating salted hash:
      $check = hash('md5', $salt.$_POST['pass']);

      // Checking hash:
      if( $check == $stored_hash )
      {
        //Redirect the browser if pass matches
        header("Location: game.php?name=".urlencode($_POST['who']));
        return;
      }
      else
      {
        $error = "Incorrect password";
      }
    }
  }
 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič fb0e8c3d</title>
    <?php require_once "bootstrap.php"; ?>
  </head>
  <body>
  <div class="container">
    <h1> Please Log In </h1>
    <?php
      // Prints error message to the screen
      if( $error !== false)
      {
        echo('<p style="color: red;">'.htmlentities($error)."</p>\n");
      }
     ?>
    <form method="POST">
      <label for="nam"> User Name </label>
      <input type="text" name="who" id="nam"><br/>
      <label for="id_1723"> Password </label>
      <input type="text" name="pass" id="id_1723"><br/>
      <input type="submit" value="Log In">
      <input type="submit" name="cancel" value="Cancel">
    </form>
  </div>
  </body>
</html>
