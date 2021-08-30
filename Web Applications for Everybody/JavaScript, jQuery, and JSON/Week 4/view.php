<?php
  session_start();
  require_once "head.php";
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
          // If my values exist then do this

          $stmt = $pdo->prepare("SELECT * FROM education where profile_id = :prof ORDER BY rank");
          $stmt->execute(array(
            ':prof' => $_GET['profile_id']));
          $education = $stmt->fetch(PDO::FETCH_ASSOC);
          if($education == true)
          {
            echo('<p>Education<br/>');
            echo('<ul>');
            while($education !== false)
            {
              echo('<li>'.htmlentities($education['year']).': ');
              $stmt2 = $pdo->prepare('SELECT name FROM institution WHERE institution_id = :iid');
              $stmt2->execute(array(
                ':iid' => $education['institution_id']));
              $row = $stmt2->fetch(PDO::FETCH_ASSOC);
              echo(htmlentities($row['name']));
              $education = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            echo('</ul>');
            echo('</p>');
          }

          $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :prof ORDER BY rank");
          $stmt->execute(array(
            ':prof' => $_GET['profile_id']));
          $positions = $stmt->fetch(PDO::FETCH_ASSOC);
          if($positions !== false)
          {
            echo('<p>Position<br/>');
            echo('<ul>');
            while($positions == true)
            {
              echo('<li>'.htmlentities($positions['year']).': '.htmlentities($positions['description']));
              $positions = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            echo('</ul>');
            echo('</p>');
          }
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
