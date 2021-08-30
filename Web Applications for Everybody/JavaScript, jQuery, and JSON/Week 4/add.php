<?php
  session_start();
  require_once "head.php";
  require_once "pdo.php";
  require_once "utility_functions.php";

  //from utility_functions.php
  isLoggedIn();
  isCancel();

  //*****     Data validation after POST     *****//
  if(isset($_POST["add"]))
  {

    if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"]))
    {
      $msg = validateProfile();
      if(is_string($msg))
      {
        $_SESSION["error"] = $msg;
        header("Location: add.php");
        return;
      }

      $msg = validatePos();
      if(is_string($msg))
      {
        $_SESSION["error"] = $msg;
        header("Location: add.php");
        return;
      }

      $msg = validateEdu();
      if(is_string($msg))
      {
        $_SESSION["error"] = $msg;
        header("Location: add.php");
        return;
      }

      // Data input from post to SQLiteDatabase
      $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
      $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']));
      $profile_id = $pdo->lastInsertID();

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
          ':pid' => $profile_id,
          ':rank' => $rank,
          ':year' => $year,
          ':desc' => $desc));
        $rank++;
      }

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
        $year = $_POST['edu_year'.$i];
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
          ':pid' => $profile_id,
          ':rank' => $rank,
          ':year' => $year,
          ':iid' => $institution_id));
        $rank++;
      }

      $_SESSION["success"] = "Profile added";
      header("Location: index.php");
      return;
    }
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
      <h1>Adding Profile for UMSI</h1>
      <?php
        //Error message if the inputs were wrong
        flashMessage();
       ?>
      <form class="add-form" method="post">
        <label>First Name:</label>
        <input type="text" name="first_name" size="61"><br/>
        <label>Last Name:</label>
        <input type="text" name="last_name" size="61"><br/>
        <label>Email:</label>
        <input type="text" name="email" size="61"><br/>
        <p>Headline:<br/></p>
        <input type="text" name="headline" size="77"><br/>
        <p>Summary:<br/></p>
        <p>
          <textarea name="summary" rows="8" cols="80"></textarea>
        </p>
        <p>Education: <input type="submit" style="height:27px; width:27px" id="addEdu" value="+"></p>
        <div id="education_fields">
        </div>
        <p>
        <p>Position: <input type="submit" style="height:27px; width:27px" id="addPos" value="+"></p>
        <div id="position_fields">
        </div>
        <p>
          <input type="submit" name="add" value="Add">
          <input type="submit" name="cancel" value="Cancel">
        </p>
      </form>
      <script type="text/javascript">

              countPos = 0;
              countEdu = 0;

              $(document).ready(
                function()
                {
                  window.console && console.log('Document ready called');

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
                }
              ); //ending bracket

      </script>
    </div>
  </body>
</html>
