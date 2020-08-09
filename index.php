<?php
error_reporting(0);
@ini_set('display_errors', 0);
$test = 0;
/*
*
* Dateien Laden
*
*/
require_once "config.php";
require_once "sessionid.php";
require_once 'class.iCalReader.php';

  $jsessionid = getUntisSessionId($SCHOOL, $USERNAME, $PASSWORD, $schoolname);



/*
*
* Variablen
*
*/

$ICAL_DATE = date('Y-m-d', strtotime(' + 1 days')); // Aufbau des Datums für den iCal-Kalender
$wochentag = date('N');

$url = iCalURL($ICAL_ELEMTYPE, $ICAL_ELEMID, $ICAL_DATE); // Die URL zum Kalender früher elemID 449

/*
*
* Telegram Code
*
*/
function sendMessage($chatID, $messaggio)
{ // Nachricht senden
    $url = "$locationURL/senden/?parsemode=markdown&submit=Senden&chatid=" . $chatID;
    $url = $url . "&nachricht=" . urlencode($messaggio);

    // Seite öffnen

    $ch = curl_init();
    $optArray = array( // Optionen setzen
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true
  );
    curl_setopt_array($ch, $optArray); // Optionen einstellen
  $result = curl_exec($ch); // Seite öffnen
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // HTTP-Code abrufen
  curl_close($ch);
    echo "Nachricht '$messaggio' gesendet an " . $chatID . "\n"; // Info die auf der Website ausgegeben wird.(optional)
}
function sendImage($chatID, $messaggio)
{ // Nachricht senden
    $messaggio = strtok($messaggio, "\n");
    $messaggio = str_replace(" ", "", $messaggio);
    $url = "$locationURL/senden/?parsemode=image&submit=Senden&chatid=" . $chatID;
    $url = $url . "&nachricht=$messaggio";
    // Seite öffnen

    $ch = curl_init();
    $optArray = array( // Optionen setzen
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true
  );
    curl_setopt_array($ch, $optArray); // Optionen einstellen
  $result = curl_exec($ch); // Seite öffnen
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // HTTP-Code abrufen
  curl_close($ch);
  
    echo "Bild '$messaggio' gesendet an " . $chatID . "\n"; // Info die auf der Website ausgegeben wird.(optional)
}
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
date_default_timezone_set('Europe/Berlin');
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

if ($file === false) {
    echo "Fehler beim laden der iCal-Datei.\n";
    sendMessage($chatIDErr, "Error 7: Fehler beim laden der iCal-Datei."); // Fehlernachricht senden
    die("Error 7");
}else {
  echo "Laden der iCal-Datei erfolgreich.\n";
}

// Datei als aktuell.ics speichern

file_put_contents('aktuell.ics', $file);

if ($file == file_get_contents("leer.ics")) {
  echo "iCal-Datei leer, möglicherweise kein Unterricht.\n";
  sendMessage($chatIDErr, "Error 8: iCal-Datei leer, möglicherweise kein Unterricht."); // Fehlernachricht senden
  die("Error 8");
}

echo "\n<br>\n<br>";



function fachName($fname)
{
    $fname1 = $fname;

    // Create connection
    $conn = mysqli_connect($DB_servername, $DB_username, $DB_password, $DB_dbname);
    // Check connection
    if (!$conn) {
        sendMessage($chatIDErr, "Error 200: Connection failed: " . mysqli_connect_error()); // Fehlernachricht senden
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

            //Arbeit schriftl. L. Variable
            $arbeit = "[schriftl. L.] " . $row["Kuerzel"];

            if ($fname == $arbeit) {
                if ($_GET["ics"] == "woche.ics") {
                    $fname = "[schriftl. L.] " . $row["Name"]. " (". $row["Kuerzel"] . ")";
                } else {
                    $fname = "[schriftl. L.] *". $row["Name"]. "* (". $row["Kuerzel"] . ")";
                }
            }
        }
        if ($fname == "" || is_null($fname)) {
            return $fname1;
        } else {
            return $fname;
            //echo "-";
            //echo $fname;
            //echo "-";
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
    sendMessage($chatIDErr, "Error 201: Connection failed: " . mysqli_connect_error()); // Fehlernachricht senden
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
/*
*
* Setzen der Sprache und Zeitzone (optional)
*
*/
try {

    /*
    *
    * iCal auslesen
    *
    */
    $ical = new ICal("aktuell.ics");
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
            /* echo "-".file_get_contents("fehler.txt")."-";
            if(file_get_contents("fehler.txt") == "0"){
            sendMessage($chatID, "Untis ist down."); // Neuerungsnachricht senden
            file_put_contents("fehler.txt", "1");
            }else{
            sendMessage($chatID, "Untis wieder up."); // Neuerungsnachricht senden
            file_put_contents("fehler.txt", "0");
            }*/
            sendMessage($chatIDErr, "Error 10"); // Fehlernachricht senden
            die("Error 10"); // Sofort beenden
        }
    } catch (Exception $e) {
        $sortierfehler = "Fehler bei der Sortierung!\n" . $e->getMessage();
        echo $sortierfehler;        
        sendMessage($chatIDErr, "Error 11: ".$sortierfehler); // Fehlernachricht senden
        die("Error 11");

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


//              0           1           2           3           4           5         6
    $tag = array("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");

    for ($i=0; $i < 7; $i++) {
        // (Re-)Initialisierung
        $output = "";

        //Output Schleife pro $event
        foreach ($events as $event) {
if (isset($event['LOCATION'])) {
  // code...

        if ($event['LOCATION'] != "") {
          // code...


  // DTSTART und DTEND zu einem normalen Datum machen
            $dtstart = strtotime($event['DTSTART']);
            $dtend = strtotime($event['DTEND']);

            if (strftime("%A", $dtstart) == $tag[$i]) {
                $output .= "Fach: ".fachName($event['SUMMARY'])."\n";
                $output .= "Start: ". strftime("%H:%M Uhr", $dtstart)."\n";
                $output .= "Ende: ". strftime("%H:%M Uhr", $dtend)."\n";
                $output .= "Lehrer: Keine Angaben\n";
                $output .= "Raum: * ".$event['LOCATION']." *\n";
                $output .= "\n\n";
            }
            }
          }
        }
        $dertag = $output;
        $taghtml = $tag[$i].".html";

        // aktuell.html in $aktuell schreiben

        $aktuell = file_get_contents("$taghtml");
        /*
        *
        * Stundenplan vergleichen
        *
        */
        if ($aktuell == $dertag) { // Wenn die aktuelle Datei == die alte Datei ist..
        echo "Keine Neuerung am ".$tag[$i]." seit: ". strftime("%A, %d. %B %Y %X", filemtime("$taghtml")). " Uhr.\n<br>"; // Ausgabe: Keine Neuerung..
        } else {

            // Bei Neuerung..
            file_put_contents("$taghtml", $dertag); // Bei Neuerung aktuell.html mit neuen Stundenplan Daten erstellen
            echo "Neuerung am ".$tag[$i]."\n<br>"; // Ausgabe: Neuerung

            /*
            *
            * Nachrichten senden
            *
            */
            file_put_contents("diff/old.txt", $aktuell);
            file_put_contents("diff/new.txt", $dertag);
            if (date("H") > 3) {
                $screenshoturl = file_get_contents("$locationURL/diff/screenshot.php");

                sendMessage($chatID, "Vorher:\n$aktuell"); // Vorherigen Stundenplan senden
                sendMessage($chatID, "Nachher:\n$dertag"); // Neuen Stundenplan senden
                sendImage($chatID, $screenshoturl); // Neuen Stundenplan senden
                sendMessage($chatID, "Irgendwas hat sich am ".$tag[$i]." geändert."); // Neuerungsnachricht senden
                if ($test == 0) {
                }else {
                echo "\n!TEST - TEST - TEST!\n";
                }
            }
        }
       
    }
}



/*
*
* Fehler abgreifen
*
*/
catch (Exception $e) { // Fehlercode auf $e schreiben
    $error = "Fehler bei der Verarbeitung. " . $e->getMessage() . "\n"; // Fehler auf der Website ausgeben
    sendMessage($chatIDErr, $error); // Fehlernachricht senden
    mysqli_close($conn);
    die(); // Sofort beenden
}
