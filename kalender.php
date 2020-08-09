
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
<form class="" action="" method="get">
  <h3>
  <label for="">Woche:</label>
  <button type="submit" name="Woche" value="diese">Diese</button> - <button type="submit" name="Woche" value="n">Nächste</button>
  </h3>
</form><?php
require_once "config.php";


error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_GET["Woche"])) {
  $_GET["Woche"]="diese";
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
}elseif ($_GET["Woche"] == "n") {
  $datum = date('Y-m-d', strtotime(' + 1 week')); // Aufbau des Datums für den iCal-Kalender
}else {
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
		'header' => "Cookie: schoolname=" . $schoolname . ""
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

try
{
	#echo "Laden der iCal-Datei erfolgreich..\n\n";
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
    }else {
      echo "Fehler bei der Sortierung!"; 
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
        if ($events[$a]['DTEND'] == $events[$b]['DTSTART'] && $events[$a]['SUMMARY'] == $events[$b]['SUMMARY'] && $events[$a]['LOCATION'] == $events[$b]['LOCATION']&& $events[$a]['DESCRIPTION'] == $events[$b]['DESCRIPTION']) {

          $events[$a]['DTEND'] = $events[$b]['DTEND'];
          unset($events[$b]);
        }

      }
// Output Schleife pro $events


	foreach($events as $event)
	{
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
     $termin[] = array("Fach"  => $event['SUMMARY'],
                        'Datum' => strftime("%A, %d. %B %Y", $dtstart),
                        'Start'   => strftime("%H:%M Uhr", $dtstart),
                        "Ende"    => strftime("%H:%M Uhr", $dtend),
                        'Lehrer'  => $event['DESCRIPTION'],
                        "Raum"    => $event['LOCATION']);


	}
}


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

foreach ($termin as $nr => $inhalt)
{
    $fach[$nr]  = strtolower( $inhalt['Fach'] );
    $datum[$nr]   = strtolower( $inhalt['Datum'] );
    $start[$nr] = strtolower( $inhalt['Start'] );
    $ende[$nr]  = strtolower( $inhalt['Ende'] );
    $lehrer[$nr]   = strtolower( $inhalt['Lehrer'] );
    $raum[$nr] = strtolower( $inhalt['Raum'] );
}

ausgabe_tabelle ( $termin );

function ausgabe_tabelle ( $termin )
{
    echo '<table border="1" cellpadding="6" cellspacing="0">';

    // Kopf fuer sortierung
    echo '<tr bgcolor="#6C9DE6">';


    echo '<th>';
        echo 'Fach ';
    echo '</th>';
    echo '<th>';
        echo 'Datum ';
    echo '</th>';
    echo '<th>';
        echo 'Start ';
    echo '</th>';
    echo '<th>';
        echo 'Ende ';
    echo '</th>';
    echo '<th>';
        echo 'Lehrer ';
    echo '</th>';
    echo '<th>';
        echo 'Raum ';
    echo '</th>';


    echo '</tr>';

$zeilenr = 0;
    foreach ($termin AS $inhalt )
    {
        $zeilenr++;
        echo '<tr';
        echo farbwechsel ( $zeilenr );
        echo '>';
        echo '<td>';
            echo $inhalt['Fach'];
        echo '</td>';
        echo '<td>';
            echo $inhalt['Datum'];
        echo '</td>';
        echo '<td>';
            echo $inhalt['Start'];
        echo '</td>';
        echo '<td>';
            echo $inhalt['Ende'];
        echo '</td>';

        echo '<td>';
            echo $inhalt['Lehrer'];
        echo '</td>';
        echo '<td>';
            echo $inhalt['Raum'];
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}



function farbwechsel ( $zeilenr )
{
    if ( bcmod ( $zeilenr , '2' ) == 0 )
    {
        $hintergrundfarbe = ' bgcolor="#ACC8F0" ';
    }
    else
    {
        $hintergrundfarbe = ' bgcolor="#DDE8F9" ';
    }
    return ( $hintergrundfarbe );
}

?>
