<?php
  // To check if user is logged in
  function isLoggedIn() {
    if(!((isset($_SESSION["name"])) && (strlen($_SESSION["name"]) > 0)))
    {
      die("Not logged in");
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
