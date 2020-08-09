<?php 
// WIRD NICHT GENUTZT ... glaub ich
date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, array('de_DE.UTF-8','de_DE@euro','de_DE','german')); // Setzen der Sprache und Zeitzone (optional)
date_default_timezone_set('Europe/Berlin');

require_once 'class.iCalReader.php';

$ical   = new ICal('../aktuell.ics');
$events = $ical->events();

$output = "Diese Stunden hast du am Montag dieser Woche:\n\n";

  try {
    function sortByName($a, $b)
    {
        $a = $a['DTSTART'];
        $b = $b['DTSTART'];

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    usort($events, 'sortByName');
  } catch (\Exception $e) {
    $output = "Fehler bei der Sortierung!\n". $e->getMessage();
    die();
  }



//Output Schleife pro $event
foreach ($events as $event) {

// DTSTART und DTEND zu einem normalen Datum machen
$dtstart = strtotime($event['DTSTART']);
$dtend = strtotime($event['DTEND']);

if (strftime("%A", $dtstart) == "Montag") {
  $output .= "Fach: *".$event['SUMMARY']."*\n";
  $output .= "Start: ". strftime("%H:%M Uhr", $dtstart)."\n";
  $output .= "Ende: ". strftime("%H:%M Uhr", $dtend)."\n";
  $output .= "Lehrer: ".$event['DESCRIPTION']."\n";
  $output .= "Raum: *".$event['LOCATION']."*\n";
  $output .= "\n\n";
}


}
echo $output;
?>