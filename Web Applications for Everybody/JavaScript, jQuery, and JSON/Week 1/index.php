<?php
  session_start();
  require_once "bootstrap.php";
  require_once "pdo.php";
 ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Aleksander Dzikevič</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>
    <div class="container">
      <h1>Aleksander Dzikevič's Resume Registry</h1>
      <?php
        if(isset($_SESSION["name"]))
        {
          //Success message after different actions
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

          echo('<p><a href="logout.php">Logout</a></p>');
          //*****     DB data search     *****//
          // query statement
          $stmt = $pdo->query("SELECT first_name, last_name, headline FROM profile");
          // we search(fetch) for query
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          // if there is no data, row is equal to false
          if($row !== false)
          {
            echo('<p><table class="styled-table">'."\n");
            echo('<thead><tr><th>Name</th><th>Headline</th><th>Action</th></thead>');
            $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
            {
              echo "<tbody><tr><td>";
              echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name'])." ".htmlentities($row['last_name']).'</a>');
              echo("</td><td>");
              echo(htmlentities($row['headline']));
              echo("</td><td>");
              echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
              echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
              echo("</td></tr>\n");
            }
          }
          echo('</tbody></table></p>');
          echo('<p><a href="add.php">Add New Entry</a></p>');
        }
        else
        {
          if ( isset($_SESSION["error"]) )
          {
            echo('<p style="color: red;">'.$_SESSION["error"]."</p>\n");
            unset($_SESSION["error"]);
          }

          echo('<p><a href="login.php">Please log in</a></p>');
          //*****     DB data search     *****//
          // query statement
          $stmt = $pdo->query("SELECT first_name, last_name, headline FROM profile");
          // we search(fetch) for query
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          // if there is no data, row is equal to false
          if($row !== false)
          {
            echo('<p><table class="styled-table">'."\n");
            echo('<thead><tr><th>Name</th><th>Headline</th></thead>');
            $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
            {
              echo "<tbody><tr><td>";
              echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name'])." ".htmlentities($row['last_name']).'</a>');
              echo("</td><td>");
              echo(htmlentities($row['headline']));
              echo("</td>");
            }
          }
          echo('</tbody></table></p>');
        }
       ?>
    </div>
  </body>
</html>
