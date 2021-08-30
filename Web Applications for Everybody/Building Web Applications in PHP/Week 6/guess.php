<html>
  <head>
    <title> Aleksander Dzikevič 42122642 </title>
  </head>
  <body>
    <h1> Welcome to my guessing game </h1>
<?php
    if (!isset($_GET['guess']))
    {
      exit("Missing guess parameter");
    }
    if (isset($_GET['guess']) && empty($_GET['guess']))
    {
      exit("Your guess is too short");
    }
    if (!is_numeric($_GET['guess']))
    {
      exit("Your guess is not a number");
    }
    if ($_GET['guess'] < 16)
    {
      exit("Your guess is too low");
    }
    if ($_GET['guess'] > 16)
    {
      exit("Your guess is too high");
    }
    if ($_GET['guess'] == 16)
    {
      exit("Congratulations - You are right");
    }

    /*
    <html>
    <head>
    <title>Guessing Game for Charles Severance</title>
    </head>
    <body>
    <h1>Welcome to my guessing game</h1>
    <p>
    <?php
      if ( ! isset($_GET['guess']) ) {
        echo("Missing guess parameter");
      } else if ( strlen($_GET['guess']) < 1 ) {
        echo("Your guess is too short");
      } else if ( ! is_numeric($_GET['guess']) ) {
        echo("Your guess is not a number");
      } else if ( $_GET['guess'] < 42 ) {
        echo("Your guess is too low");
      } else if ( $_GET['guess'] > 42 ) {
        echo("Your guess is too high");
      } else {
        echo("Congratulations - You are right");
      }
    ?>
    </p>
    </body>
    </html>
    */
 ?>
  </body>
</html>
