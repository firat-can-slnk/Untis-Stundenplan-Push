<?php

error_reporting(0);
ini_set('display_errors', 0);

require_once "../config.php";
$jsessionid = file_get_contents("../sessionid.php");
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
$eigenes = 0;

if ($_GET["swift"] == 1) { // Wird für die iOS App genutzt

  list($y, $m, $d) = explode("-", $_GET["Woche"]);

  if(checkdate($m, $d, $y)){
      $eigenes = 1;
      $ICAL_DATE = date("Y-m-d", strtotime($_GET["Woche"]));
      //echo $ICAL_DATE;
  }else {

  }
}

if ($_GET["bot"] == 1) {  // Telegram Bot

  list($y, $m, $d) = explode("-", $_GET["Woche"]);

  if(checkdate($m, $d, $y)){
      $eigenes = 1;
      $ICAL_DATE = date("Y-m-d", strtotime($_GET["Woche"]));
      //echo $ICAL_DATE;
  }else {

  }
}

if ($_GET["Woche"] == "diese") {
  $ICAL_DATE = date('Y-m-d'); // Aufbau des Datums für den iCal-Kalender
}elseif ($_GET["Woche"] == "n") {
  $ICAL_DATE = date('Y-m-d', strtotime(' + 1 week')); // Aufbau des Datums für den iCal-Kalender
}else {
  if ($eigenes = 0) {
    echo "Du hast die Woche nicht eingegeben!";
    die;
  }

}

$url = iCalURL($ICAL_ELEMTYPE, $ICAL_ELEMID, $ICAL_DATE); // Die URL zum Kalender


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

if ($file == false)
{
	echo "Fehler beim laden der iCal-Datei.\n";
	die();
}


// Datei als aktuell.ics speichern

file_put_contents('woche.ics', $file);
/*
*
* Setzen der Sprache und Zeitzone (optional)
*
*/

if ($_GET["swift"] == "1") {
  $wochentag = "tag=". $_GET["Wochentag"];
  echo file_get_contents("tag.php?ics=woche.ics&$wochentag");


}else {


try
{
	#echo "Laden der iCal-Datei erfolgreich..\n\n";
if ($_GET["Woche"]== "n") {
  echo "Diese Stunden hast du in der nächsten Woche:\n\n";
}elseif ($_GET["Woche"]== "diese") {
  echo "Diese Stunden hast du in dieser Woche:\n\n";
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
	try
	{
		function sortByName($a, $b)
		{
			$a = $a['DTSTART'];
			$b = $b['DTSTART'];
			if ($a == $b) return 0;
			return ($a < $b) ? -1 : 1;
		}
    if (usort($events, 'sortByName')) {
      # code...
    }else {
      echo "Fehler bei der Sortierung!"; // Neuerungsnachricht senden
      die(); // Sofort beenden
    }

	}

	catch(Exception $e)
	{
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

      function fachName($fname) {
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
            while($row = mysqli_fetch_assoc($result)) {
              if ($fname == $row["Kuerzel"]) {
                $fname = "*". $row["Name"]. "* (". $row["Kuerzel"] . ")";
                return $fname;
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
                $lname1;
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



// Output Schleife pro $events


	foreach($events as $event)
	{
    if (isset($event['LOCATION'])) {
      // code...

            if ($event['LOCATION'] != "") {
              // code...
if (isset($event['DTSTART'])) {
  # code...

		// DTSTART und DTEND zu einem normalen Datum machen

		$dtstart = strtotime($event['DTSTART']);
		$dtend = strtotime($event['DTEND']);
		/*
		*
		* Ausgaben der Kalender Datei
		*
		*/
      echo "Fach: ".fachName($event['SUMMARY'])."\n";
  		echo "Start: " . strftime("%A, %d. %B %Y %H:%M Uhr", $dtstart) . "\n"; // Ausgabe: Start   = Montag, 01. Januar 2018 06:00 Uhr
  		echo "Ende: " . strftime("%A, %d. %B %Y %H:%M Uhr", $dtend) . "\n"; // Ausgabe: Ende    = Montag, 01. Januar 2018 13:00 Uhr
  		echo "Lehrer: Keine Angaben\n"; // Ausgabe: Lehrer  = HIN, HNG, ...
  		echo "Raum: *" . $event['LOCATION'] . "*\n"; // Ausgabe: Raum    = HNN101, HNE201, HNG002, ...
  		echo "\n\n"; // Ausgabe: 2 Neue Zeilen


	}
}}}

  die();
}



/*
*
* Fehler abgreifen
*
*/
catch(Exception $e)
{ // Fehlercode auf $e schreiben
	$error = "Fehler bei der Verarbeitung. " . $e->getMessage() . "\n"; // Fehler auf der Website ausgeben
  echo "$error";
	die(); // Sofort beenden
}
}
?>
