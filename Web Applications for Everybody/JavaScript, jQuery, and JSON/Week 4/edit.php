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

      $msg = validatePos();
      if(is_string($msg))
      {
        $_SESSION["error"] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
      }

      $msg = validateEdu();
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
          ':pid' => $_GET['profile_id'],
          ':rank' => $rank,
          ':year' => $year,
          ':desc' => $desc));
        $rank++;
      }

      // Clear out the old education entries
      $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id = :pid');
      $stmt->execute(array(
        ':pid' => $_REQUEST['profile_id']));

      // Insert the education entries
      $rank = 1;
      for($i = 1; $i <= 9; $i++)
      {
        if(!isset($_POST['edu_year'.$i]))
        {
          continue;
        }
        if(!isset($_POST['school'.$i]))
        {
          continue;
        }
        $edu_year = $_POST['edu_year'.$i];
        $school = $_POST['school'.$i];

        // Lookup the school if it is there
        $institution_id = false;
        $stmt = $pdo-> prepare('SELECT institution_id FROM institution WHERE name = :name');
        $stmt->execute(array(
          ':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false)
        {
          $institution_id = $row['institution_id'];
        }

        // If there was no institution, insert it
        if($institution_id === false)
        {
          $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
          $stmt->execute(array(
            ':name' => $school));
            $institution_id = $pdo->lastInsertID();
        }

        $stmt = $pdo-> prepare('INSERT INTO education (profile_id, rank, year, institution_id) VALUES (:pid, :rank, :year, :iid)');
        $stmt->execute(array(
          ':pid' => $_REQUEST['profile_id'],
          ':rank' => $rank,
          ':year' => $edu_year,
          ':iid' => $institution_id));

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
         <p>Education: <input type="submit" style="height:27px; width:27px" id="addEdu" value="+"></p>
         <div id="education_fields">
           <?php

             $countEdu = 0;
             $stmt = $pdo->prepare("SELECT * FROM education WHERE profile_id = :prof ORDER BY rank");
             $stmt->execute(array(
               ':prof' => $_GET['profile_id']));
             $educations = $stmt->fetch(PDO::FETCH_ASSOC);
             while($educations !== false)
             {
               $stmt2 = $pdo->prepare("SELECT name FROM institution WHERE institution_id = :iid");
               $stmt2->execute(array(
                 ':iid' => $educations['institution_id']));
               $institution = $stmt2->fetch(PDO::FETCH_ASSOC);
               echo('<div id="edu'.$educations['rank'].'">');
               echo('<p>Year: ');
               echo('<input type="text" size="10" name="edu_year'.$educations['rank'].'" value="'.htmlentities($educations['year']).'" /> ');
               echo('<input type="button" style="height:27px; width:27px" value="-" onclick="$(\'#edu'.$educations['rank'].'\').remove();return false;"></p>');
               echo('<p>School: <input type="text" size="77" name="school'.$educations['rank'].'" class="school" value="'.htmlentities($institution['name']).'"</p></div>');
               $educations = $stmt->fetch(PDO::FETCH_ASSOC);
               $countEdu++;
             }
            ?>
         </div>
         <p>Position: <input type="submit" style="height:27px; width:27px" id="addPos" value="+"></p>
         <div id="position_fields">
           <?php

             $countPos = 0;
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
               $countPos++;
             }
            ?>
         </div>
         <p>
           <input type="submit" name="save" value="Save">
           <input type="submit" name="cancel" value="Cancel">
         </p>
       </form>
       <script type="text/javascript">


               $(document).ready(
                 function()
                 {
                   window.console && console.log('Document ready called');
                   countPos = <?= $countPos ?>;
                   countEdu = <?= $countEdu ?>;
                   $('#addPos').click(
                     function(event)
                     {
                       event.preventDefault();
                       if(countPos >= 9)
                       {
                         alert("Maximum of nine position entries exceeded");
                         return;
                       }
                       countPos++;
                       window.console && console.log("Adding position "+countPos);

                       $('#position_fields').append(' \
                         <div id="position'+countPos+'"> \
                         <p>Year: \
                         <input type="text" size="10" name="year'+countPos+'" value="" /> \
                         <input type="button" style="height:27px; width:27px" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                         <p><textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></p> \
                         </div>');
                       }
                     );

                     $('#addEdu').click(
                       function(event)
                       {
                         event.preventDefault();
                         if(countEdu >= 9)
                         {
                           alert("Maximum of nine education entries exceeded");
                           return;
                         }
                         countEdu++;
                         window.console && console.log("Adding education "+countEdu);

                         $('#education_fields').append(' \
                           <div id="edu'+countEdu+'"> \
                           <p>Year: \
                           <input type="text" size="10" name="edu_year'+countEdu+'" value="" /> \
                           <input type="button" style="height:27px; width:27px" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p> \
                           <p>School: <input type="text" size="77" name="school'+countEdu+'" class="school" value=""</p> \
                           </div>'
                         );

                           $(".school").autocomplete(
       									    {source: "school.php"}
                           );
                         }
                       );
                       $(".school").autocomplete(
                        {source: "school.php"}
                       );
                 }
               ); //ending bracket

       </script>
     </div>
   </body>
 </html>
