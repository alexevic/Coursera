<?php
  session_start();
  require_once "head.php";
  require_once "pdo.php";
  require_once "utility_functions.php";

  // from utility_functions.php
  isLoggedIn();
  isCancel();
  Guardian();

  // When we press delete button and if autos_id exists, this code gets executed
  if((isset($_POST['delete'])) && (isset($_GET['profile_id'])))
  {
    $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id = :zip');
    $stmt->execute(array(':zip' => $_GET['profile_id']));
    $_SESSION['success'] = "Profile deleted";
    header('Location: index.php');
    return;
  }

  // Dummy values, we get them when we open the page
  $stmt = $pdo->prepare("SELECT first_name, last_name FROM profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row === false)
  {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
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
      <h1>Deleting Profile</h1>
      <?php
        echo('<p>First Name: '.htmlentities($row['first_name']));
        echo('</p><p>Last Name: '.htmlentities($row['last_name']).'</p>');
       ?>
      <form method="post">
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>
  </body>
</html>
