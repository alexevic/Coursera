<?php
  session_start();
  require_once "bootstrap.php";
  require_once "pdo.php";
  require_once "utility_functions.php";

  //from utility_functions.php
  isLoggedIn();
  isCancel();

  //*****     Data validation after POST     *****//
  if(isset($_POST["add"]))
  {
    if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"]))
    {
      // First we check are all the fields filled with data, if not then program will return
      if((strlen($_POST["first_name"]) < 1) || (strlen($_POST["last_name"]) < 1) || (strlen($_POST["email"]) < 1) || (strlen($_POST["headline"]) < 1) || (strlen($_POST["summary"]) < 1))
      {
        $_SESSION["error"] = "All fields are required";
        header("Location: add.php");
        return;
      }
      // Email verification
      if(strpos($_POST["email"], '@') === false)
      {
        $_SESSION["error"] = "Email address must contain @";
        header("Location: add.php");
        return;
      }
      // Data input from post to SQLiteDatabase
      $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
      $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']));
      $_SESSION["success"] = "Profile added";
      header("Location: index.php");
      return;
    }
  }
 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="container">
      <h1>Adding Profile for UMSI</h1>
      <?php
        //Error message if the inputs were wrong
        if(isset($_SESSION["error"]))
        {
          echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
          unset($_SESSION["error"]);
        }
       ?>
      <form class="add-form" method="post">
        <label>First Name:</label>
        <input type="text" name="first_name" size="61"><br/>
        <label>Last Name:</label>
        <input type="text" name="last_name" size="61"><br/>
        <label>Email:</label>
        <input type="text" name="email" size="61"><br/>
        <p>Headline:<br/></p>
        <input type="text" name="headline" size="77"><br/>
        <p>Summary:<br/></p>
        <p>
          <textarea name="summary" rows="8" cols="80"></textarea>
        </p>
        <p>
          <input type="submit" name="add" value="Add">
          <input type="submit" name="cancel" value="Cancel">
        </p>
      </form>
    </div>
  </body>
</html>
