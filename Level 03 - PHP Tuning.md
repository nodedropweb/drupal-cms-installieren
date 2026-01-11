---
title: "Level 03 - PHP Tuning"
source: "https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f"
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: "Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen."
tags:
  - "clippings"
---
Dein Kellner (Apache) steht bereit. Er hat Block und Stift in der Hand. Aber wenn jetzt ein Gast reinkommt und „Einmal Drupal, bitte!“ bestellt, passiert... nichts. Die Küche ist leer.

Wir brauchen einen Chefkoch. Wir brauchen **PHP**. Aber für unser Enterprise-Setup stellen wir keinen Hobby-Koch ein, der in einer Suppe rührt. Wir engagieren eine ganze Küchenbrigade: **PHP-FPM** (FastCGI Process Manager).

Während das alte PHP jede Bestellung einzeln abgearbeitet hat, kann FPM hunderte Teller gleichzeitig anrichten. Und weil wir auf Ubuntu 24.04 sind, bekommen wir direkt die neueste, stabilste Version serviert: **PHP 8.3**.

### Schritt 1: Die Brigade anheuern (Installation)

Drupal ist anspruchsvoll. Es braucht nicht nur den Koch (Core), sondern auch viele Spezialwerkzeuge (Extensions) für Bilder, Datenbanken und Verschlüsselung.

Da wir später **PostgreSQL** nutzen wollen (für die AI-Features), installieren wir direkt den passenden Treiber (`php-pgsql`) statt MySQL.

Kopiere diesen Befehl (er ist lang, aber er enthält alles, was Drupal CMS braucht):

```
sudo apt install php8.3-fpm php8.3-common php8.3-pgsql php8.3-gd php8.3-cli php8.3-curl php8.3-zip php8.3-xml php8.3-mbstring php8.3-intl php8.3-bcmath php8.3-opcache -y
```
- `fpm`: Der Turbo-Modus.
- `pgsql`: Die Verbindung zu unserer zukünftigen Datenbank.
- `gd`: Für Bildbearbeitung (Thumbnails).
- `opcache`: Ganz wichtig für den Speed (dazu gleich mehr).

### Schritt 2: Den Motor frisieren (Konfiguration)

Standardmäßig ist PHP sehr "konservativ" eingestellt. Es darf nur 128 MB RAM nutzen. Für ein kleines Blog okay, aber für Drupal CMS + AI? Keine Chance. Wir schrauben das Limit hoch.

Wir bearbeiten die Konfigurationsdatei `php.ini` für FPM:

```
sudo nano /etc/php/8.3/fpm/php.ini
```

Jetzt nutzen wir die Suchfunktion von Nano (`STRG + W`) und suchen nach folgenden Begriffen. Ändere die Werte wie folgt:

1. Suche: `memory_limit`
	- Ändere zu: `memory_limit = 512M`
	- *Warum?* Drupal braucht Platz zum Atmen. 512 MB sind der Sweetspot für schnelle Ladezeiten.
2. Suche: `upload_max_filesize`
	- Ändere zu: `upload_max_filesize = 64M`
	- *Warum?* Damit du auch größere Bilder oder PDFs hochladen kannst.
3. Suche: `post_max_size`
	- Ändere zu: `post_max_size = 64M`
	- *Warum?* Muss mindestens so groß sein wie der Upload.
4. Suche: `max_execution_time`
	- Ändere zu: `max_execution_time = 60`
	- *Warum?* Gibt komplexen Skripten etwas mehr Zeit, bevor sie abgebrochen werden.
5. **Der Opcache-Turbo:** Suche nach `[opcache]`. Entferne das Semikolon `;` am Anfang der Zeilen, um sie zu aktivieren, und setze diese Werte:
	```
	opcache.enable=1
	opcache.memory_consumption=256
	opcache.max_accelerated_files=20000
	opcache.revalidate_freq=0
	```
	- *Was passiert hier?* Opcache speichert vorkompilierten Code im RAM. PHP muss Skripte nicht bei jedem Klick neu lesen. `revalidate_freq=0` bedeutet: Prüfe immer sofort, ob sich Dateien geändert haben (wichtig für Entwickler).

Speichere mit `STRG + O`, `Enter` und beende mit `STRG + X`.

### Schritt 3: Hochzeit (Apache & PHP verbinden)

Der Kellner (Apache) und der Koch (PHP-FPM) arbeiten standardmäßig noch nicht zusammen. Wir müssen Apache sagen: "Alles, was auf `.php` endet, gibst du bitte an die FPM-Brigade weiter."

```
sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php8.3-fpm
```

Und jetzt starten wir beide neu, damit sie sich kennenlernen:

```
sudo systemctl restart apache2 php8.3-fpm
```

### Schritt 4: Der Geschmackstest

Läuft die Küche wirklich? Wir testen das mit einer kleinen Info-Datei.

Erstelle eine Datei im Web-Ordner:

```
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/info.php
```

Öffne deinen Browser und rufe auf: `http://DEINE-SERVER-IP/info.php`

Siehst du eine lila-graue Tabelle mit dem großen Logo **PHP Version 8.3...**? Scrolle runter. Steht bei `memory_limit` jetzt `512M`?

**Perfekt!** 🎉 Dein Server hat jetzt Gehirn-Power.

⚠️ **Sicherheits-Sherpa-Hinweis:** Diese Datei verrät Hackern alles über deinen Server. Lösche sie sofort wieder:

```
sudo rm /var/www/html/info.php
```

### Was haben wir erreicht?

Du hast jetzt einen Webserver, der nicht nur HTML ausliefert, sondern echte Anwendungen berechnen kann. Und zwar mit **512 MB RAM** und **Opcache**. Das ist schneller als 90% der Shared-Hosting-Pakete da draußen.

Aber ein Gehirn ohne Gedächtnis ist vergesslich. PHP vergisst alles, sobald die Seite fertig geladen ist. Wir brauchen ein Langzeitgedächtnis. Wir brauchen eine Datenbank. Und zwar die beste für AI.

Bereit für PostgreSQL?

### Keyfacts: Das nimmst du mit

Bevor wir weitermachen, hier das Wichtigste auf einen Blick:

- **PHP-FPM ist Pflicht:** Für High-Traffic-Seiten nutzen wir immer den *FastCGI Process Manager* statt des alten Apache-Moduls.
- **Postgres-Treiber:** Da wir PostgreSQL nutzen wollen, haben wir `php8.3-pgsql` statt `php-mysql` installiert.
- **Memory Tuning:** Standard-PHP (128MB) reicht für Drupal CMS oft nicht. Wir gehen direkt auf **512MB**.
- **Opcache:** Der wichtigste Performance-Schalter in PHP. Er hält den Code im Arbeitsspeicher. Ohne Opcache ist Drupal langsam.