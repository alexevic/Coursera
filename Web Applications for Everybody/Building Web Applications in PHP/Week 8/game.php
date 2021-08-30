<?php
  // Demand GET parameter (if there is no name param)
  if( !isset($_GET['name']) || strlen($_GET['name']) < 1 )
  {
    die("Name parameter missing");
  }

  // We get data from "Logout submit", if it's clicked, page redirects to index.php
  if ( isset($_POST['logout']) )
  {
      header('Location: index.php');
      return;
  }

  $names = array('Rock', 'Paper', 'Scissors'); // Setting up values for the game
  // If there is input, it is changed to integer value, else it's -1 (default)
  $human = isset($_POST["human"]) ? $_POST['human']+0 : -1;

  $computer = rand(0,2); // Randomize computer choice

  /* Our "hard" function (if someone is reading this, I need help here,
  how to make this function more simple?)*/

  function check($computer, $human)
  {
    if ( $human === $computer )
    {
      return "Tie";
    }
    elseif ($human == 0)
    {
      if($computer == 1)
      {
        return "You Lose";
      }
      else
      {
        return "You Win";
      }
    }
    elseif ($human == 1)
    {
      if($computer == 2)
      {
        return "You Lose";
      }
      else
      {
        return "You Win";
      }
    }
    else
    {
      if($computer == 0)
      {
        return "You Lose";
      }
      else
      {
        return "You Win";
      }
    }
  }

  // Result message to print to the page
  $result = check($computer, $human);
 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič fb0e8c3d</title>
    <?php require_once "bootstrap.php"; ?>
  </head>
  <body>
    <div class="container">
    <h1>Rock Paper Scissors</h1>
    <?php
      // Prints welcome message and name of the player
      if ( isset($_REQUEST['name']) )
      {
        echo "<p>Welcome: ".htmlentities($_REQUEST['name'])."</p>\n";
      }
     ?>
    <!-- Our form -->
    <form method="post">
    <select name="human">
    <option value="-1">Select</option>
    <option value="0">Rock</option>
    <option value="1">Paper</option>
    <option value="2">Scissors</option>
    <option value="3">Test</option>
    </select>
    <!-- Buttons -->
    <input type="submit" value="Play">
    <input type="submit" name="logout" value="Logout">
    </form>

    <pre>
<?php
    if ( $human == -1 )
    {
      print "Please select a strategy and press Play.";
    }
    else if($human == 3)
    {
      for ( $c = 0; $c < 3; $c++ )
      {
        for ( $h = 0; $h < 3; $h++ )
        {
          $r = check($c, $h);
          print "Human=$names[$h] Computer=$names[$c] Result=$r";
          // No extra new line
          if($c == 2 && $h == 2)
          {
            break;
          }
          else
          {
            print "\n";
          }
        }
      }
    }
    else
    {
      print "Your Play=$names[$human] Computer Play=$names[$computer] Result=$result";
    }
 ?>
    </pre>
    </div>
  </body>
</html>
