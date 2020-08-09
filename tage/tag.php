<?php
// Wird für den Telegram Bot verwendet, kann aber auch einfach so aufgerufen werden
error_reporting(0);
ini_set('display_errors', 0);

require_once "../config.php";
date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, array('de_DE.UTF-8','de_DE@euro','de_DE','german')); // Setzen der Sprache und Zeitzone (optional)
date_default_timezone_set('Europe/Berlin');

if (!$_GET["tag"]) {
    die("Kein Tag ausgewählt.");
}

if ($_GET["tag"] == "heute" || $_GET["tag"] == "Montag" || $_GET["tag"] == "Dienstag" || $_GET["tag"] == "Mittwoch" || $_GET["tag"] == "Donnerstag" || $_GET["tag"] == "Freitag" || $_GET["tag"] == "Samstag" || $_GET["tag"] == "Sonntag") {
} else {
    echo "<b>";
    echo $_GET["tag"];
    die("</b> ist kein gültiger Wochentag.\n<br /> Gültige Wochentage: Montag, Dienstag, Mittwoch, Donnerstag, Freitag, Samstag und Sonntag");
}

require_once 'class.iCalReader.php';
if ($_GET["ics"] == "woche.ics") {
    $ical   = new ICal('woche.ics');
} else {
    $ical   = new ICal('../aktuell.ics');
}

$events = $ical->events();
if ($_GET["tag"] == "heute") {
    $output = "Diese Stunden hast du ".$_GET["tag"]." :\n\n";
    $_GET["tag"] = strftime("%A");
    $tag="heute";
} else {
    if ($_GET["ics"] != "woche.ics") {
        $output = "Diese Stunden hast du am ".$_GET["tag"]." dieser Woche:\n\n";
    }
}
  try {
      function sortByName($a, $b)
      {
          $a = $a['DTSTART'];
          $b = $b['DTSTART'];

          if ($a == $b) {
              return 0;
          }
          return ($a < $b) ? -1 : 1;
      }

      usort($events, 'sortByName');
  } catch (\Exception $e) {
      $output = "Fehler bei der Sortierung!\n". $e->getMessage();
      die();
  }

  /*
  *
  * Doppelstunden zu einer Stunde machen mit Zeitanpassung  (EP 8:00-8:45 & EP 8:45-9:30 -> EP 8:00-9:30)
  *
  */
  $arraycount = count($events);
  for ($a=0; $a < $arraycount ; $a++) {
      $b = $a + 1;
      if ($events[$a]['DTEND'] == $events[$b]['DTSTART'] && $events[$a]['SUMMARY'] == $events[$b]['SUMMARY'] && $events[$a]['LOCATION'] == $events[$b]['LOCATION']) {
          $events[$a]['DTEND'] = $events[$b]['DTEND'];
          $events[$a]['LOCATION'] = $events[$b]['LOCATION'];
          unset($events[$b]);
      }
  }
  function fachName($fname)
  {
      $fname1 = $fname;
      // Create connection
      $conn = mysqli_connect($DB_servername, $DB_username, $DB_password, $DB_dbname);
      // Check connection
      if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
      }

      $sql = "SELECT ID, Kuerzel, Name FROM Fach ORDER BY `Fach`.`Kuerzel` ASC";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0) {
          // output data of each row
          while ($row = mysqli_fetch_assoc($result)) {
              if ($fname == $row["Kuerzel"]) {
                  if ($_GET["ics"] == "woche.ics") {
                      $fname = $row["Name"]. " (". $row["Kuerzel"] . ")";
                  } else {
                      $fname = "*". $row["Name"]. "* (". $row["Kuerzel"] . ")";
                  }
              }
              //Klausur schriftl. L. Variable
              $arbeit = "[schriftl. L.] " . $row["Kuerzel"];

              if ($fname == $arbeit) {
                  if ($_GET["ics"] == "woche.ics") {
                      $fname = "[schriftl. L.] " . $row["Name"]. " (". $row["Kuerzel"] . ")";
                  } else {
                      $fname = "[schriftl. L.] *". $row["Name"]. "* (". $row["Kuerzel"] . ")";
                  }
              }
          }
          if ($fname == "") {
              return $fname1;
          } else {
              return $fname;
          }
      } else {
          echo "SQL Fehler. Keine Daten gefunden.";
      }
  }
  function lehrerName($lname) {
    $lname1 = $lname;
    // Create connection
    $conn = mysqli_connect($DB_servername, $DB_username, $DB_password, $DB_dbname);
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT ID, Kuerzel, Name, Email FROM Lehrer ORDER BY `Lehrer`.`Kuerzel` ASC";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {

          if ($lname == $row["Kuerzel"]) {
            $lname = $row["Name"]. " (". $row["Kuerzel"] . ")";
            return $lname;
          }

        }
        if ($lname == "" || is_null($lname)) {
            return $lname1;
        } else {
            return $lname;
            //echo "-";
            //echo $lname;
            //echo "-";
        }
    } else {
        echo "SQL Fehler. Keine Daten gefunden.";
    }
  }

  if ($_GET["ics"] == "woche.ics") {
      $woche = array(
            array("0","0","0","0","0"),
            array("0","0","0","0","0"),
            array("0","0","0","0","0"),
            array("0","0","0","0","0"),
            );
  }
$i = 0;
//Output Schleife pro $event

foreach ($events as $event) {
    //echo $event['LOCATION'];

    if (isset($event['LOCATION'])) {
        // code...

        if ($event['LOCATION'] != "") {
            // code...
            //$output = "";
            // DTSTART und DTEND zu einem normalen Datum machen
            $dtstart = strtotime($event['DTSTART']);

            $dtend = strtotime($event['DTEND']);
            $i = 0;
            if (strftime("%A", $dtstart) == $_GET["tag"]) {

                if ($_GET["ics"] == "woche.ics") {
                    $startzeitH = strftime("%H", $dtstart);
                    $startzeitM = strftime("%M", $dtstart);
                    $startzeitALT = $startzeitH . $startzeitM;
                    $startzeit = strftime("%H:%M Uhr", $dtstart);

                    if ($startzeitALT < 914) {
                        $i = 0;
                    } elseif ($startzeitALT < 1114) {
                        $i = 1;
                    } elseif ($startzeitALT < 1259) {
                        $i = 2;
                    } elseif ($startzeitALT < 1459) {
                        $i = 3;
                    } else {
                        switch ($startzeit) {
/// Die Uhrzeiten müssen der Schule angepasst werden
  case '08:45 Uhr':
    $i = 0;
    $x = 1;
    break;
      case '10:30 Uhr':
        $i = 1;
        $x = 1;
        break;
          case '12:30 Uhr':
            $i = 2;
            $x = 1;
            break;
              case '14:15 Uhr':
                $i = 3;
                $x = 1;
                break;
                  case '08:00 Uhr':
                    $i = 0;
                    break;
                      case '09:45 Uhr':
                        $i = 1;
                        break;
                          case '11:45 Uhr':
                            $i = 2;
                            break;
                              case '13:30 Uhr':
                                $i = 3;
                                break;

  case '07:30 Uhr':
    $i = 0;
    break;
      case '09:15 Uhr':
        $i = 1;
        break;
          case '11:15 Uhr':
            $i = 2;
            break;
              case '13:00 Uhr':
                $i = 3;
                break;

  default:
    $i = 0;
    break;
}
                    }
                    if ($x == 1) {
                        $woche[$i][0] = fachName($event['SUMMARY']);
                        $woche[$i][2] = strftime("%H:%M Uhr", $dtend);
                    } else {
                        if (is_null(fachName($event['SUMMARY']))) {
                            $woche[$i][0] = "Keine Angaben";
                        } else {
                            $woche[$i][0] = fachName($event['SUMMARY']);
                        }
                        if (is_null(strftime("%H:%M Uhr", $dtstart))) {
                            $woche[$i][1] = "Keine Angaben";
                        } else {
                            $woche[$i][1] = strftime("%H:%M Uhr", $dtstart);
                        }
                        if (is_null(strftime("%H:%M Uhr", $dtend))) {
                            $woche[$i][2] = "Keine Angaben";
                        } else {
                            $woche[$i][2] = strftime("%H:%M Uhr", $dtend);
                        }

                        if (is_null($event['LOCATION'])) {
                            $woche[$i][4] = "Keine Angaben";
                        } else {
                            $woche[$i][4] = $event['LOCATION'];
                        }
                    }

                    // $output = json_encode($woche);

// $output = print_r($woche);
} elseif(!$_GET["ics"]) {
                    $output .= "Fach: ".fachName($event['SUMMARY'])."\n";
                    $output .= "Start: ". strftime("%H:%M Uhr", $dtstart)."\n";
                    $output .= "Ende: ". strftime("%H:%M Uhr", $dtend)."\n";
                    $output .= "Lehrer: Keine Angaben\n";
                    $output .= "Raum: *".$event['LOCATION']."*\n";
                    $output .= "\n\n";
                }
            }
        }
    }

    $i += 1;
}
if ($_GET["ics"] == "woche.ics") {
  $output .= $woche[0][0]. ";". $woche[0][1]. ";". $woche[0][2]. ";". $woche[0][3]. ";Raum ". $woche[0][4]. ";ENDE";
  $output .= $woche[1][0]. ";". $woche[1][1]. ";". $woche[1][2]. ";". $woche[1][3]. ";Raum ". $woche[1][4]. ";ENDE";
  $output .= $woche[2][0]. ";". $woche[2][1]. ";". $woche[2][2]. ";". $woche[2][3]. ";Raum ". $woche[2][4]. ";ENDE";
  $output .= $woche[3][0]. ";". $woche[3][1]. ";". $woche[3][2]. ";". $woche[3][3]. ";Raum ". $woche[3][4]. ";ENDE";
}



if ($output == "Diese Stunden hast du am ".$_GET["tag"]." dieser Woche:\n\n" || $output == "Diese Stunden hast du ".$tag." :\n\n") {
    if ($_GET["tag"] == "heute") {
        $output = "Du hast ".$_GET["tag"]." keinen Unterricht.";
    } else {
        $output = "Du hast an diesem ".$_GET["tag"]." keinen Unterricht.";
    }
}
if ($output == '0;0;0;0;Raum 0;ENDE0;0;0;0;Raum 0;ENDE0;0;0;0;Raum 0;ENDE0;0;0;0;Raum 0;ENDE') {
    $output = "Kein Unterricht an diesem Tag!;;;;ENDE0;0;0;0;ENDE0;0;0;0;ENDE0;0;0;0;ENDE";
}
echo $output;
