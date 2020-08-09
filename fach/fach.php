<?php
error_reporting(0);
require_once "../config.php";
if (isset($_GET["swift"])){
  echo '<!DOCTYPE html>
  <html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

      <title></title>
    </head>
    <body>';
}
?>


<?php
$jsessionid = file_get_contents("../sessionid.php");
if (!isset($_GET["Fach"])) {
    die("Kein Fach gewählt.");
} elseif ($_GET["Fach"] == "help") {
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
        if (isset($_GET["swift"])) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "\"";
                echo utf8_encode($row["Kuerzel"]);
                echo "\",";
                // echo utf8_encode($row["Name"]);
        // echo ";";
            }
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "*";
                echo utf8_encode($row["Kuerzel"]);
                echo "* - ";
                echo utf8_encode($row["Name"]);
                echo "\n";
            }
        }
    } else {
        echo "SQL Fehler. Keine Daten gefunden.";
    }

    mysqli_close($conn);




    die();
}

error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_GET["Woche"])) {
    die("Du hast die Woche nicht eingegeben!");
}
/*
*
* Dateien Laden
*
*/
require 'class.iCalReader.php';

/*
*
* Variablen
*
*/


if ($_GET["Woche"] == "diese") {
    $datum = date('Y-m-d'); // Aufbau des Datums für den iCal-Kalender
} elseif ($_GET["Woche"] == "n") {
    $datum = date('Y-m-d', strtotime(' + 1 week')); // Aufbau des Datums für den iCal-Kalender
} else {
    echo "Du hast die Woche nicht eingegeben!";
    die;
}


$url = "https://$UNTIS_DOMAIN/WebUntis/Ical.do?elemType=$ICAL_ELEMTYPE&elemId=$ICAL_ELEMID&rpt_sd=" . $datum; // Die URL zum Kalender


/*
*
* Setzen der Sprache und Zeitzone (optional)
*
*/
date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, array(
    'de_DE.UTF-8',
    'de_DE@euro',
    'de_DE',
    'german'
));
/*
*
* Kalender.ics mithilfe des Cookies runterladen
*
*/
$opts = array(
    'http' => array(
        'method' => "GET",
        'header' => "Cookie: schoolname=" . $schoolname . "",
		'header' => "Cookie: JSESSIONID=" . $jsessionid . ""
    )
);


// Datei mit dem oben gesetzen Cookie öffnen (erforderlich, wird ohne Cookie nicht erlaubt)

$context = stream_context_create($opts);
$file = file_get_contents($url, false, $context);

if ($file == false) {
    echo "Fehler beim laden der iCal-Datei.\n";
    die();
}

function fachName($fname)
{
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
              if (isset($_GET["swift"])) {
                $fname = $row["Name"];
                return $fname;
              }else {
                // code...

                $fname = "*". $row["Name"]. "* (". $row["Kuerzel"] . ")";
                return $fname;
                  }
            }
            //Arbeit schriftl. L. Variable
            $arbeit = "schriftl. L. " . $row["Kuerzel"];

            if ($fname == $arbeit) {
                if ($_GET["ics"] == "woche.ics") {
                    $fname = "schriftl. L. " . $row["Name"]. " (". $row["Kuerzel"] . ")";
                } else {
                    $fname = "schriftl. L. *". $row["Name"]. "* (". $row["Kuerzel"] . ")";
                }
            }
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

// Datei als aktuell.ics speichern

file_put_contents('woche.ics', $file);
/*
*
* Setzen der Sprache und Zeitzone (optional)
*
*/

try {
    #echo "Laden der iCal-Datei erfolgreich..\n\n";
    if ($_GET["Woche"]== "n") {
        $output = "In der nächsten Woche hast du dieses Fach in diesen Stunden:\n\n";
    } elseif ($_GET["Woche"]== "diese") {
        $output = "In dieser Woche hast du dieses Fach in diesen Stunden:\n\n";
    }

    /*
    *
    * iCal auslesen
    *
    */
    $ical = new ICal("woche.ics");
    $events = $ical->events();
    /*
    *
    * iCal Events nach Datum und Uhrzeit sortieren
    *
    */
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
        if (usort($events, 'sortByName')) {
            # code...
        } else {
            //echo "Fehler bei der Sortierung!"; // Neuerungsnachricht senden

        }
    } catch (Exception $e) {
        $sortierfehler = "Fehler bei der Sortierung!\n" . $e->getMessage();
        echo $sortierfehler;
        die();
    }

    /*
    *
    * iCal Daten sortiert und strukturiert ausgeben
    *
    */



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
    // Output Schleife pro $events

$o = 0;
    foreach ($events as $event) {
        if (isset($event['DTSTART'])) {
            # code...
            if (strpos($event['SUMMARY'], $_GET["Fach"]) !== false) {


        // DTSTART und DTEND zu einem normalen Datum machen

                $dtstart = strtotime($event['DTSTART']);
                $dtend = strtotime($event['DTEND']);
                /*
                *
                * Ausgaben der Kalender Datei
                *
                */
                if (isset($_GET["swift"])) {
                    $getWochenuebersicht = "fach=".$event['SUMMARY']."&";
                    $getWochenuebersicht .= "start=" . $dtstart . "&"; // Ausgabe: Start   = Montag, 01. Januar 2018 06:00 Uhr
                    $getWochenuebersicht .= "ende=" . $dtend . "&"; // Ausgabe: Ende    = Montag, 01. Januar 2018 13:00 Uhr
                    $getWochenuebersicht .= "lehrer=Keine%20Angaben&"; // Ausgabe: Lehrer  = HIN, HNG, ...
                    $getWochenuebersicht .= "raum=" . $event['LOCATION']; // Ausgabe: Raum    = HNN101, HNE201, HNG002, ...
                    $urlWochenuebersicht = "$URL_SWIFTWOUE?". $getWochenuebersicht;
                    $o++;
                  echo file_get_contents($urlWochenuebersicht);

                } else {
                    $output .= "Fach: ".fachName($event['SUMMARY'])."\n";
                    $output .= "Start: " . strftime("%A, %d. %B %Y %H:%M Uhr", $dtstart) . "\n"; // Ausgabe: Start   = Montag, 01. Januar 2018 06:00 Uhr
                    $output .= "Ende: " . strftime("%A, %d. %B %Y %H:%M Uhr", $dtend) . "\n"; // Ausgabe: Ende    = Montag, 01. Januar 2018 13:00 Uhr
                    $output .= "Lehrer: Keine Angaben\n"; // Ausgabe: Lehrer  = HIN, HNG, ...
                    $output .= "Raum: *" . $event['LOCATION'] . "*\n\n"; // Ausgabe: Raum    = HNN101, HNE201, HNG002, ...
                }
            }
        }
    }
    if (isset($_GET["swift"])) {
      if ($o == 0) {

        if ($_GET["Woche"] == "n") {
        echo file_get_contents("$URL_SWIFTWOUE?keinunterricht=Kein%20Unterricht%20mit%20dem%20Fach%20in%20der%20nächsten%20Woche!");
        }elseif ($_GET["Woche"] == "diese") {
          echo file_get_contents("$URL_SWIFTWOUE?keinunterricht=Kein%20Unterricht%20mit%20dem%20Fach%20in%20dieser%20Woche!");
        }

    }
        ?>


        <?php
    } else {
        if ($output == "In dieser Woche hast du dieses Fach in diesen Stunden:\n\n") {
            $output = "In dieser Woche hast du keinen Unterricht mit diesem Fach.\n" ;
        } elseif ($output == "In der nächsten Woche hast du dieses Fach in diesen Stunden:\n\n") {
            $output = "In der nächsten Woche hast du keinen Unterricht mit diesem Fach.\n" ;
        }
        echo $output;
    }
}

/*
*
* Fehler abgreifen
*
*/
catch (Exception $e) { // Fehlercode auf $e schreiben
    $error = "Fehler bei der Verarbeitung. " . $e->getMessage() . "\n"; // Fehler auf der Website ausgeben
  echo "$error";
    die(); // Sofort beenden
}

?>
