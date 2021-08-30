<?php
  session_start();
  require_once "pdo.php";

  $error = false;
  $msg = false;

  if(!((isset($_SESSION['name'])) && (strlen($_SESSION['name']) > 0)))
  {
    die("Not logged in");
  }

  if(isset($_POST['cancel']))
  {
      // Redirect the browser to index.php
      header("Location: view.php");
      return;
  }

  if(isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']))
  {
    if((strlen($_POST['make']) < 1))
    {
      $_SESSION["error"] = "Make is required";
      header("Location: add.php");
      return;
    }
    elseif((is_numeric($_POST['year']) === false) || (is_numeric($_POST['mileage']) === false))
    {
      $_SESSION["error"] = "Mileage and year must be numeric";
      header("Location: add.php");
      return;
    }
    else {
      $sql = "INSERT INTO autos2 (make, year, mileage) VALUES (:make, :year, :mileage)";
      //echo("<pre>\n".$sql."\n</pre>\n");
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':make' => $_POST['make'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));
      $_SESSION["success"] = "Record inserted";
      header("Location: view.php");
      return;
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
      <?php echo("<h1>Tracking Autos for ".htmlentities($_SESSION['name'])."</h1>\n"); ?>
      <?php
        // Prints error message to the screen
        if(isset($_SESSION["error"]))
        {
          echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
          unset($_SESSION["error"]);
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
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>
  </body>
</html>
