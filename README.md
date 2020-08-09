# Untis-Stundenplan-Push
![Untis Stundenplan Push](https://firatcan.de/untis/logo.png)

**Untis Stundenplan Push über Telegram Bot**


Dieser Bot fragt regelmäßig über einen Cronjob den aktuellen Stundenplan deiner Klasse ab und vergleicht ihn auf Veränderungen.

Dafür werden wird der Stundenplan bei einer Änderung auf *aktuell.html* und *aktuell.ics* gespeichert.

Wenn der aktuelle Stundenplan eine Änderung aufweist wird über Telegram eine Nachricht (Push) an die angegebene Chat-ID gesendet.


> Der Bot ist zurzeit alles andere als Effizient, da er jede Minute den Kalender abfragt/runterlädt!

## Was wird benötigt?
* PHP *(ich nutze PHP-7)*
* [iCalReader *(class.iCalReader.php)*](https://github.com/MartinThoma/ics-parser)
* [Telegram Bot](https://core.telegram.org/bots/api)
* Cronjobs

## Einrichtung / Installation
1. Die Dateien der Repository in ein Web-Verzeichnis kopieren.
2. Token, ChatID, Schulname-Cookie usw in der ```index.php``` eintragen. 
3. ```chmod 777 aktuell.html && aktuell.ics ``` ausführen.
4. Cronjobs einrichten.
5. Fertig!

### Cronjobs (beispiel)
Jede Minute von 6-15 Uhr von Mo-Fr.

Einmal jede Stunde jeden Tag.
```
#m	h	dom	mon	dow	command

*	6-15	*	*	1-5	wget -O /dev/null -q https://example.com/path/to/index.php
0	*	*	*	*	wget -O /dev/null -q https://example.com/path/to/index.php
```
