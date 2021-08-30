<?php
  session_start();
  require_once "pdo.php";

  if(!((isset($_SESSION['name'])) && (strlen($_SESSION['name']) > 0)))
  {
    die("ACCESS DENIED");
  }

  if(isset($_POST['cancel']))
  {
      // Redirect the browser to index.php
      header("Location: index.php");
      return;
  }

  //*****     Data validation after POST     *****//
  if(isset($_POST["make"]) && isset($_POST["model"]) && isset($_POST["year"]) && isset($_POST["mileage"]))
  {
    // First we check are all the fields filled with data, if not then program will return
    if((strlen($_POST["make"]) < 1) || (strlen($_POST["model"]) < 1) || (strlen($_POST["year"]) < 1) || (strlen($_POST["mileage"]) < 1))
    {
      $_SESSION["error"] = "All fields are required";
      header("Location: add.php");
      return;
    }

    // If first check is done, then we check is year an integer, if not program stops
    if(!is_numeric($_POST["year"]))
    {
      $_SESSION["error"] = "Year must be an integer";
      header("Location: add.php");
      return;
    }

    // We check if mileage is integer
    if(!is_numeric($_POST["mileage"]))
    {
      $_SESSION["error"] = "Mileage must be an integer";
      header("Location: add.php");
      return;
    }

    // Data input from post to SQLiteDatabase
    // We create string with sql
    $sql = "INSERT INTO autos (make, model, year, mileage) VALUES (:make, :model, :year, :mileage)";
      // We prepare our sql statement
      $stmt = $pdo->prepare($sql);
      // We execute our statement
      $stmt->execute(array(
        ':make' => $_POST['make'],
        ':model' => $_POST['model'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));
      $_SESSION["success"] = "Record added";
      header("Location: index.php");
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
        echo("<h1>Tracking Automobiles for ".htmlentities($_SESSION["name"])."</h1>\n");

        //Error message if the inputs were wrong
        if ( isset($_SESSION["error"]) )
        {
          echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
          unset($_SESSION["error"]);
        }
       ?>
       <form method="POST">
         <label>Make:</label>
         <input type="text" name="make"/><br/>
         <label>Model:</label>
         <input type="text" name="model"/><br/>
         <label>Year:</label>
         <input type="text" name="year"/><br/>
         <label>Mileage:</label>
         <input type="text" name="mileage"/><br/>
         <input type="submit" name='add' value="Add">
         <input type="submit" name="cancel" value="Cancel">
       </form>
    </div>
  </body>
</html>
