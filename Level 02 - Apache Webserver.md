---
title: Level 02 - Apache Webserver
source: https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen.
tags:
  - clippings
---
Dein Server ist aktuell noch ein Eremit. Er ist sicher, er ist stark, aber er spricht mit niemandem außer dir (via SSH). Damit deine zukünftige Drupal-Seite im Internet sichtbar wird, brauchen wir jemanden, der Anfragen entgegennimmt und Webseiten ausliefert.

Wir engagieren **Apache2**. Apache ist der "Oberkellner" des Internets. Er ist der Industriestandard – robust, verlässlich und (wenn man es richtig macht) rasend schnell.

Wenn ein Besucher (der Gast) deine Seite aufruft, steht Apache an der Tür, nimmt die Bestellung auf ("Ich hätte gerne die Startseite"), rennt zur Küche (Drupal/PHP), holt das fertige Gericht und serviert es.

Wir installieren ihn heute nicht nur, wir geben ihm direkt Rollschuhe (**HTTP/2**), damit er schneller serviert als die Konkurrenz.

### Schritt 1: Den Kellner einstellen (Installation)

Das Schöne an Ubuntu 24.04 ist: Der Kellner wartet schon im Vorzimmer. Wir müssen ihn nur reinholen.

Tippe in dein Terminal:

```
sudo apt install apache2 -y
```

Das war’s schon. Die Software ist drauf. Aber der Kellner steht noch vor verschlossener Tür. Erinnerst du dich an unsere Firewall (UFW) aus Level 1? Die lässt aktuell niemanden rein.

### Schritt 2: Die Türen öffnen (Firewall-Update)

Wir müssen dem Türsteher (UFW) sagen: "Lass den Traffic für Webseiten durch!" Wir öffnen Port 80 (HTTP) und Port 443 (HTTPS). Ubuntu hat dafür ein praktisches Profil namens "Apache Full".

```
sudo ufw allow "Apache Full"
```

Checken wir kurz, ob das geklappt hat:

```
sudo ufw status
```

Du solltest jetzt sehen, dass `OpenSSH` und `Apache Full` auf `ALLOW` stehen. Perfekt. Das Restaurant ist eröffnet.

### Schritt 3: Den Turbo zünden (Module aktivieren)

Ein Standard-Apache ist solide, aber wir wollen **Performance**. Drupal liebt bestimmte Funktionen, die standardmäßig oft aus sind. Wir schalten sie jetzt ein.

1. **mod\_rewrite:** Erlaubt Drupal, schöne URLs zu bauen (z.B. `deineseite.de/ueber-uns` statt `deineseite.de/?node=1`).
2. **mod\_headers & mod\_expires:** Wichtig für das Caching im Browser (damit Besucher nicht jedes Bild bei jedem Klick neu laden müssen).
3. **mod\_http2:** Das Protokoll der modernen Welt. Statt Anfragen nacheinander abzuarbeiten (wie an der Kasse im Supermarkt), werden viele Daten gleichzeitig durch die Leitung geschoben.

Kopiere diesen Befehl:

```
sudo a2enmod rewrite headers expires http2 ssl
```
- `a2enmod`: Apache 2 Enable Module.

### Schritt 4: Neustart & Test

Damit der Kellner seine neuen Fähigkeiten nutzt, müssen wir ihn kurz durchschütteln (neu starten).

```
sudo systemctl restart apache2
```

**Der Moment der Wahrheit:** Öffne jetzt auf deinem Computer den Browser (Chrome/Firefox/Edge). Tippe in die Adresszeile die **IP-Adresse deines Servers** ein (die gleiche, die du für SSH nutzt).

Beispiel: `http://123.456.78.90`

Siehst du eine Seite mit dem Titel **"Apache2 Default Page"**? Boom! 💥 Dein Server ist live. Er antwortet. Du hast soeben physische Hardware (oder virtuelle Cloud-Hardware) dazu gebracht, mit der Welt zu sprechen.

### Was passiert hier gerade?

Aktuell serviert Apache nur eine statische HTML-Datei (`index.html`), die Ubuntu als Platzhalter angelegt hat. Später werden wir diese Datei löschen und durch Drupal ersetzen. Aber für heute wissen wir: Die Infrastruktur steht. Der Kellner ist bereit, die Küche (PHP) ist aber noch geschlossen.

Das ändern wir im nächsten Level. Dann kommt der Chefkoch ins Spiel.

### Keyfacts: Das nimmst du mit

Bevor wir weitermachen, hier das Wichtigste auf einen Blick:

- **Rolle des Webservers:** Apache nimmt Anfragen aus dem Internet entgegen und liefert Dateien aus.
- **Firewall nicht vergessen:** Nach der Installation muss der Zugriff via `ufw allow "Apache Full"` explizit erlaubt werden.
- **Drupal-Optimierung:** Module wie `rewrite` (für saubere URLs) und `http2` (für Speed) sollten direkt am Anfang aktiviert werden.
- **Status-Check:** Der Aufruf der Server-IP im Browser ist der einfachste Weg zu prüfen, ob der Webserver läuft.