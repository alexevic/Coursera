<!DOCTYPE html>
<html>
  <head>
    <title> Aleksander Dzikevič MD5 cracker </title>
  </head>
  <body>
    <h1>MD5 cracker</h1>
    <p> This application takes an MD5 hash of a four
      digit pin and check all 10,000 possible four digit
      PINs to determine the PIN. </p>
    <pre>
<?php
  $infomsg = "Not found";

  // Here we check if there is parameter, this code works only after entering parameter
  if( isset($_GET['md5']))
  {
    $time_pre = microtime(true);            // For time measure
    echo "Debug Output:\n";

    $md5 = $_GET['md5'];                    // Our input value
    $checks = 0;                            // To count how many checks we did
    $num = "0123456789";                    // String digits array

    // Here we have 4 loops for 4 digits of the PIN
    for($i = 0; $i < strlen($num); $i++)
    {
      $dig1 = $num[$i]; // First digit
      for($j = 0; $j < strlen($num); $j++)
      {
        $dig2 = $num[$j]; // Second digit
        for($l = 0; $l < strlen($num); $l++)
        {
          $dig3 = $num[$l]; // Third digit
          for($o = 0; $o < strlen($num); $o++)
          {
            $dig4 = $num[$o]; // Fourth digit

            $maybepin = $dig1.$dig2.$dig3.$dig4;  // We connect all 4 digits

            // Check if $maybepin in hash md5 matches
            $comp = hash('md5', $maybepin);

            // Print first 15 md5 and pins
            if($checks < 15)
            {
              print $comp." ".$maybepin."\n";
            }

            $checks++;

            if($comp == $md5)
            {
              $infomsg = $maybepin;
              break;
            }
          }
        }
      }
    }
    print "Total checks: $checks\n";

    $time_post = microtime(true);
    print "Elapsed time: ";
    print $time_post-$time_pre;
    print "\n";
    print "</pre> <p>PIN: $infomsg</p>\n";
  }
 ?>
    </pre>
    <!-- Here we input value -->
    <form>
      <input type="text" name="md5" size="40" />
      <input type="submit" value="Crack MD5" />
    </form>
    <br>
    <a href="index.php"> Reset page </a>
  </body>
</html>
