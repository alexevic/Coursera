<?php
  session_start();
  require_once "pdo.php";
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
      <h1>Welcome to the Automobiles Database</h1>
      <?php
        if(isset($_SESSION["name"]))
        {
          //Success message after add.php exec
          if ( isset($_SESSION["success"]) )
          {
            echo('<p style="color: green;">'.$_SESSION["success"]."</p>\n");
            unset($_SESSION["success"]);
          }

          //*****     DB data search     *****//
          // query statement
          $stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos");
          // we search(fetch) for query
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          // if there is no data, row is equal to false
          if($row === false)
          {
            echo("<p>No rows found</p>");
          }
          else
          {
            echo('<p><table class="styled-table">'."\n");
            echo('<thead><tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></thead>');
            $stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos");
            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
            {
              echo "<tbody><tr><td>";
              echo(htmlentities($row['make']));
              echo("</td><td>");
              echo(htmlentities($row['model']));
              echo("</td><td>");
              echo(htmlentities($row['year']));
              echo("</td><td>");
              echo(htmlentities($row['mileage']));
              echo("</td><td>");
              echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
              echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
              echo("</td></tr>\n");
            }
          }
          echo('</tbody></table></p>');
          echo('<p><a href="add.php">Add New Entry</a></p>');
          echo('<p><a href="logout.php">Logout</a></p>');
        }
        else
        {
          echo('<p><a href="login.php">Please log in</a></p>');
          echo('<p>Attempt to <a href="add.php">add data</a> without logging in</p>');
        }
       ?>
    </div>
  </body>
</html>
