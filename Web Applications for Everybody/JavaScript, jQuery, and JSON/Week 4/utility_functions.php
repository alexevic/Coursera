<?php
  // To check if user is logged in
  function isLoggedIn() {
    if(!((isset($_SESSION["name"])) && (strlen($_SESSION["name"]) > 0)))
    {
      die("ACCESS DENIED");
    }
  }

  // When cancel is pressed
  function isCancel() {
    if(isset($_POST["cancel"]))
    {
      header("Location: index.php");
      return;
    }
  }

  // Guardian: Make sure that profile_id is present
  function Guardian()
  {
    if(!isset($_GET["profile_id"]))
    {
      $_SESSION["error"] = "Missing profile_id";
      header('Location: index.php');
      return;
    }
  }

  // Flash messages
  function flashMessage()
  {
    if(isset($_SESSION["success"]))
    {
      echo('<p style="color: green;">'.$_SESSION["success"]."</p>\n");
      unset($_SESSION["success"]);
    }

    if(isset($_SESSION["error"]))
    {
      echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
      unset($_SESSION["error"]);
    }
  }

  // Input data for profile validation
  function validateProfile()
  {
    // First we check are all the fields filled with data, if not then program will return
    if((strlen($_POST["first_name"]) < 1) || (strlen($_POST["last_name"]) < 1) || (strlen($_POST["email"]) < 1) || (strlen($_POST["headline"]) < 1) || (strlen($_POST["summary"]) < 1))
    {
      return "All fields are required";
    }

    // Email verification
    if(strpos($_POST["email"], '@') === false)
    {
      return "Email address must contain @";
    }
  }

  // Input data for positions validation
  function validatePos()
  {
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

      // First we check are all the fields filled with data, if not then program will return
      if((strlen($year) < 1) || (strlen($desc) < 1))
      {
        return "All fields are required";
      }

      // Year verification
      if(!is_numeric($year))
      {
        return "Position year must be numeric";
      }
    }
    return true;
  }

  // Input data for education validation
  function validateEdu()
  {
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
      $eyear = $_POST['edu_year'.$i];
      $school = $_POST['school'.$i];

      // First we check are all the fields filled with data, if not then program will return
      if((strlen($eyear) < 1) || (strlen($school) < 1))
      {
        return "All fields are required";
      }

      // Year verification
      if(!is_numeric($eyear))
      {
        return "Education year must be numeric";
      }
    }
    return true;
  }

  // Position load (I don't use it)
  function loadPos($pdo, $profile_id)
  {
    $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :prof ORDER BY rank");
    $stmt->execute(array(
      ':prof' => $_GET['profile_id']));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
  }
