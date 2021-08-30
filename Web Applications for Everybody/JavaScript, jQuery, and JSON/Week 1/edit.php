<?php
  session_start();
  require_once "bootstrap.php";
  require_once "pdo.php";
  require_once "utility_functions.php";

  //from utility_functions.php
  isLoggedIn();
  isCancel();
  Guardian();

  //*****     Data validation after POST     *****//
  if(isset($_POST["save"]))
  {
    if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"]))
    {
      // First we check are all the fields filled with data, if not then program will return
      if((strlen($_POST["first_name"]) < 1) || (strlen($_POST["last_name"]) < 1) || (strlen($_POST["email"]) < 1) || (strlen($_POST["headline"]) < 1) || (strlen($_POST["summary"]) < 1))
      {
        $_SESSION["error"] = "All fields are required";
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
      }
      // Email verification
      if(strpos($_POST["email"], '@') === false)
      {
        $_SESSION["error"] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
      }
      // Data update from post to SQLiteDatabase
      $sql = "UPDATE profile SET profile_id = :pi, user_id = :uid, first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pi";
      echo("<pre>\n".$sql."\n</pre>\n");
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':pi' => $_GET['profile_id'],
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']));
      $_SESSION["success"] = "Profile updated";
      header("Location: index.php");
      return;
    }
  }

  // To get dummy values
  $stmt = $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, email, headline, summary FROM profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($row === false)
  {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
  }
  else
  {
    $first_name_dummy = htmlentities($row['first_name']);
    $last_name_dummy = htmlentities($row['last_name']);
    $email_dummy = htmlentities($row['email']);
    $headline_dummy = htmlentities($row['headline']);
    $summary_dummy = htmlentities($row['summary']);
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
       <h1>Editing Profile for UMSI</h1>
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
         <input type="text" name="first_name" value="<?= $first_name_dummy ?>" size="61"><br/>
         <label>Last Name:</label>
         <input type="text" name="last_name" value="<?= $last_name_dummy ?>" size="61"><br/>
         <label>Email:</label>
         <input type="text" name="email" value="<?= $email_dummy ?>" size="61"><br/>
         <p>Headline:<br/></p>
         <input type="text" name="headline" value="<?= $headline_dummy ?>" size="77"><br/>
         <p>Summary:<br/></p>
         <p>
           <textarea name="summary" rows="8" cols="80"><?= htmlentities($summary_dummy); ?></textarea>
         </p>
         <p>
           <input type="submit" name="save" value="Save">
           <input type="submit" name="cancel" value="Cancel">
         </p>
       </form>
     </div>
   </body>
 </html>
