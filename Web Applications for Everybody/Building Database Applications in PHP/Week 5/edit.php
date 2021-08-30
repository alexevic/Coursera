<?php
  session_start();
  require_once "pdo.php";

  if(!((isset($_SESSION["name"])) && (strlen($_SESSION["name"]) > 0)))
  {
    die("ACCESS DENIED");
  }

  // Guardian: Make sure that autos_id is present
  if(!isset($_GET['autos_id']))
  {
    $_SESSION['error'] = "Bad value for id";
    header('Location: index.php');
    return;
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
      header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
      return;
    }

    // If first check is done, then we check is year an integer, if not program stops
    if(!is_numeric($_POST["year"]))
    {
      $_SESSION["error"] = "Year must be an integer";
      header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
      return;
    }

    // We check if mileage is integer
    if(!is_numeric($_POST["mileage"]))
    {
      $_SESSION["error"] = "Mileage must be an integer";
      header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
      return;
    }

    // Data input from post to SQLiteDatabase
    // We create string with sql
    $sql = "UPDATE autos SET autos_id = :autos_id, make = :make, model = :model, year = :year, mileage = :mileage WHERE autos_id = :autos_id";
    //echo("<pre>\n".$sql."\n</pre>\n");
    // We prepare our sql statement
    $stmt = $pdo->prepare($sql);
    // We execute our statement
    $stmt->execute(array(
      ':autos_id' => $_GET['autos_id'],
      ':make' => $_POST['make'],
      ':model' => $_POST['model'],
      ':year' => $_POST['year'],
      ':mileage' => $_POST['mileage']));
    $_SESSION["success"] = "Record edited";
    header("Location: index.php");
    return;
  }

  // To get dummy values
  $stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['autos_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row === false)
  {
    $_SESSION['error'] = 'Bad value for id';
    header( 'Location: index.php' ) ;
    return;
  }
  else
  {
    $make_dummy = htmlentities($row['make']);
    $model_dummy = htmlentities($row['model']);
    $year_dummy = htmlentities($row['year']);
    $mileage_dummy = htmlentities($row['mileage']);
    $autos_id = $row['autos_id'];
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
      <h1>Editing Automobile</h1>
      <?php
        //Error message if the inputs were wrong
        if(isset($_SESSION["error"]))
        {
          echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
          unset($_SESSION["error"]);
        }
       ?>
       <form method="POST">
         <label>Make:</label>
         <input type="text" name="make" value="<?= $make_dummy ?>"/><br/>
         <label>Model:</label>
         <input type="text" name="model" value="<?= $model_dummy ?>"/><br/>
         <label>Year:</label>
         <input type="text" name="year" value="<?= $year_dummy ?>"/><br/>
         <label>Mileage:</label>
         <input type="text" name="mileage" value="<?= $mileage_dummy ?>"/><br/>
         <input type="submit" name='save' value="Save">
         <input type="submit" name="cancel" value="Cancel">
       </form>
    </div>
  </body>
</html>
