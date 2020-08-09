<?php

$ICAL_ELEMTYPE = "X"; // elemType - zu finden im Downloadlink der ics Datei auf Untis
$ICAL_ELEMID = "XXX"; // elemId - zu finden im Downloadlink der ics Datei auf Untis
$ICAL_DATE = date('Y-m-d');

$locationURL = ""; // URL zum Ort wo diese Dateien gespeichert sind (http://domain.de/untis z.B.) 

$UNTIS_DOMAIN = "mese.webuntis.com"; // WebUntis URL, ggf ändern

$schoolname = "_xxxxxxxxxxxxxxxxxxxxxx=="; // Schulname - Zu finden in den Cookies auf Untis

function iCalURL($ICAL_ELEMTYPE,$ICAL_ELEMID, $ICAL_DATE){
	$url = "https://$UNTIS_DOMAIN/WebUntis/Ical.do?elemType=$ICAL_ELEMTYPE&elemId=$ICAL_ELEMID&rpt_sd=$ICAL_DATE"; // URL für die ICS Datei
		return $url;
}
$SCHOOL = 	"XXXXXX"; // Schulname - zu finden in der Untis URL (mese.webuntis.com/WebUntis/?school=xxxxxxx)
$USERNAME = "XXXXXX"; // Benutzername für Untis 
$PASSWORD = "XXXXXX"; // Passwort für Untis

$files = array("leer.ics", "aktuell.ics", "aktuell.html", "Montag.html", "Dienstag.html", "Mittwoch.html", "Donnerstag.html", "Freitag.html", "Samstag.html", "Sonntag.html");
foreach ($files as $file) {
	if (!file_exists($file)) {
		file_put_contents($file, "");
	}
}
$token = "botXXXXXXXXX:XXXXXXXXXXX-XXXXXXXXXXXXXXXXXXXXXXX"; // Telegram BOT Token (optional, wenn kein Bot vorhanden ist, leer lassen)

$URL_SWIFTWOUE = ""; // Hier URL zur swiftWochenuebersicht.php einfügen

// Datenbank daten
$DB_servername = "";
$DB_username = "";
$DB_password = "";
$DB_dbname = "";

$telegramChatID = ""; // Chat ID für den Untis Channel in Telegram
$chatIDErr = ""; // Chat ID für den Fehler Channel in Telegram (genutzt für Fehlermeldungen)

?>