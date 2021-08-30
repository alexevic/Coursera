<?php
  session_start();
  require_once "bootstrap.php";
  require_once "pdo.php";

 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="container">
      <h1>Profile information</h1>
      <?php
        $stmt = $pdo->prepare('SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :pi');
        $stmt->execute(array( ':pi' => $_GET['profile_id']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false)
        {
          echo('<p>First Name: '.htmlentities($row["first_name"]).'</p>');
          echo('<p>Last Name: '.htmlentities($row["last_name"]).'</p>');
          echo('<p>Email: '.htmlentities($row["email"]).'</p>');
          echo('<p>Headline:<br/>'.htmlentities($row["headline"]).'</p>');
          echo('<p>Summary:<br/>'.htmlentities($row["summary"]).'</p>');
        }
        else
        {
          $_SESSION["error"] = "Could not load profile";
          header("Location: index.php");
          return;
        }
       ?>
      <a href="index.php">Done</a>
    </div>
  </body>
</html>
