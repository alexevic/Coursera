<?php
  session_start();
  require_once "head.php";
  require_once "pdo.php";
  require_once "utility_functions.php";

  //from utility_functions.php
  isLoggedIn();
  isCancel();
  Guardian();

  // Load up the profile in question
  $stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :prof AND user_id = :uid');
  $stmt->execute(array(
    ':prof' => $_REQUEST['profile_id'],
    ':uid' => $_SESSION['user_id']));
  $profile = $stmt->fetch(PDO::FETCH_ASSOC);
  if($profile === false)
  {
      $_SESSION["error"] = "Could not load profile";
      header('Location: index.php');
      return;
  }

  //*****     Data validation after POST     *****//
  if(isset($_POST["save"]))
  {
    if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"]))
    {
      $msg = validateProfile();
      if(is_string($msg))
      {
        $_SESSION["error"] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
      }

      // Data update from post to SQLiteDatabase
      $sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid";
      //echo("<pre>\n".$sql."\n</pre>\n");
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':pid' => $_GET['profile_id'],
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']));

      // Clear out the old position entries
      $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
      $stmt->execute(array(
        ':pid' => $_REQUEST['profile_id']));

      // Insert the position entries
      $rank = 1;
      for($i = 1; $i <= 9; $i++)
      {
        if(!isset($_POST['year'.$i]))
        {
          continue;
        }
        if(!isset($_POST['desc'.$i]))
        {
          continue;
        }
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute(array(
          ':pid' => $_REQUEST['profile_id'],
          ':rank' => $rank,
          ':year' => $year,
          ':desc' => $desc));
        $rank++;
      }

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

  /*
  $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $rowOfPosition = $stmt->fetchAll();
  */
  /*
  <?php
            $rank = 1;
            foreach ($rowOfPosition as $row) {
                echo "<div id=\"position" . $rank . "\">
  <p>Year: <input type=\"text\" name=\"year1\" value=\"".$row['year']."\">
  <input type=\"button\" value=\"-\" onclick=\"$('#position". $rank ."').remove();return false;\"></p>
  <textarea name=\"desc". $rank ."\"').\" rows=\"8\" cols=\"80\">".$row['description']."</textarea>
</div>";
                $rank++;
  } ?>
  */

 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <title>Aleksander Dzikevič</title>
     <link rel="stylesheet" href="css/styles.css">
     <script src="js/add_position.js"></script>
   </head>
   <body>
     <div class="container">
       <h1>Editing Profile for UMSI</h1>
       <?php
         //Error message if the inputs were wrong
         flashMessage();
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
         <p>Position: <input type="submit" style="height:27px; width:27px" id="addPos" value="+"></p>
         <div id="position_fields">
           <?php
             $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :prof ORDER BY rank");
             $stmt->execute(array(
               ':prof' => $_GET['profile_id']));
             $positions = $stmt->fetch(PDO::FETCH_ASSOC);
             while($positions == true)
             {
               echo('<div id="position'.$positions['rank'].'">');
               echo('<p>Year: ');
               echo('<input type="text" size="10" name="year'.$positions['rank'].'" value="'.htmlentities($positions['year']).'" /> ');
               echo('<input type="button" style="height:27px; width:27px" value="-" onclick="$(\'#position'.$positions['rank'].'\').remove();return false;"></p>');
               echo('<p><textarea name="desc'.$positions['rank'].'" rows="8" cols="80">'.htmlentities($positions['description']).'</textarea></p></div>');
               $positions = $stmt->fetch(PDO::FETCH_ASSOC);
             }
            ?>
         </div>
         <p>
           <input type="submit" name="save" value="Save">
           <input type="submit" name="cancel" value="Cancel">
         </p>
       </form>
     </div>
   </body>
 </html>
