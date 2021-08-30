<?php
  session_start();
  require_once "pdo.php";

  if(!((isset($_SESSION['name'])) && (strlen($_SESSION['name']) > 0)))
  {
    die("Not logged in");
  }

  if(isset($_POST['logout']))
  {
      // Redirect the browser to logout.php
      header("Location: logout.php");
      return;
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
      <?php
        echo("<h1>Tracking Autos for ".htmlentities($_SESSION['name'])."</h1>\n");

        //Success message after add.php exec
        if ( isset($_SESSION["success"]) )
        {
          echo('<p style="color: green;">'.$_SESSION["success"]."</p>\n");
          unset($_SESSION["success"]);
        }
       ?>
      <h2>Automobiles</h2>
      <ul>
        <?php
          $stmt = $pdo->query("SELECT make, year, mileage FROM autos2 ORDER BY make");
          while($row = $stmt->fetch(PDO::FETCH_ASSOC))
          {
            echo("<li>".htmlentities($row['year']).' '.htmlentities($row['make']).' / '.htmlentities($row['mileage'])."</li>\n");
          }
         ?>
      </ul>
      <p>
        <a href="add.php">Add New</a> | <a href="logout.php">Logout</a>
      </p>
    </div>
    <!-- <img src="https://c.tenor.com/cXlrPENTVkEAAAAj/chika-dance.gif" class="center"> -->
  </body>
</html
