<?php
  require_once "pdo.php";

  $error = false;
  $msg = false;

  if(!((isset($_GET['name'])) && (strlen($_GET['name']) > 0)))
  {
    die("Name parameter missing");
  }

  if(isset($_POST['logout']))
  {
      // Redirect the browser to index.php
      header("Location: index.php");
      return;
  }
  if(isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']))
  {
    if((strlen($_POST['make']) < 1))
    {
      $error = "Make is required";
    }
    elseif((is_numeric($_POST['year']) === false) || (is_numeric($_POST['mileage']) === false))
    {
      $error = "Mileage and year must be numeric";
    }
    else {
      $sql = "INSERT INTO autos (make, year, mileage) VALUES (:make, :year, :mileage)";
      //echo("<pre>\n".$sql."\n</pre>\n");
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':make' => $_POST['make'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));
      $msg = "Record inserted";
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
      <?php echo("<h1>Tracking Autos for ".htmlentities($_GET['name'])."</h1>\n"); ?>
      <?php
        // Prints error message to the screen
        if($error !== false)
        {
          echo('<p style="color: red;">'.htmlentities($error)."</p>\n");
        }
        elseif($msg !== false)
        {
          echo('<p style="color: green;">'.htmlentities($msg)."</p>\n");
        }
       ?>
      <form method="POST">
        <label>Make:</label>
        <input type="text" name="make" size="50"/><br/>
        <label>Year:</label>
        <input type="text" name="year"/><br/>
        <label>Mileage:</label>
        <input type="text" name="mileage"/><br/>
        <input type="submit" value="Add">
        <input type="submit" name="logout" value="Logout">
      </form>
      <h2>Automobiles</h2>
      <ul>
        <?php
          $stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY make");
          while($row = $stmt->fetch(PDO::FETCH_ASSOC))
          {
            echo("<li>".htmlentities($row['year']).' '.htmlentities($row['make']).' / '.htmlentities($row['mileage'])."</li>\n");
          }
         ?>
      </ul>
    </div>
  </body>
</html>
