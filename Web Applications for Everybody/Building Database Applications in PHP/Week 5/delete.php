<?php
  session_start();
  require_once  "pdo.php";

  // Guardian: Make sure that user_id is present
  if ( ! isset($_GET['autos_id']) ) {
    $_SESSION['error'] = "Bad value for id";
    header('Location: index.php');
    return;
  }

  // Dummy values, we get them when we open the page
  $stmt = $pdo->prepare("SELECT make, model, autos_id FROM autos where autos_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['autos_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row === false)
  {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
  }

  // When we press delete button and if autos_id exists, this code gets executed
  if((isset($_POST['delete'])) && (isset($_POST['autos_id'])))
  {
    $sql = "DELETE FROM autos WHERE autos_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['autos_id']));
    $_SESSION['success'] = "Record deleted";
    header('Location: index.php');
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
      <?php echo('<p>Confirm: Deleting '.htmlentities($row['make']).' '.htmlentities($row['model']).' </p>') ?>
      <form method="post">
        <input type="hidden" name="autos_id" value="<?= $row['autos_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
      </form>
    </div>
  </body>
</html>
