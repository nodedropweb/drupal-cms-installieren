---
title: Level 08 Installation mit Drush
source: https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen.
tags:
  - clippings
---
Der Moment der Wahrheit. In deinem Ordner `/var/www/cms` liegen tausende Dateien, aber sie schlafen noch. Es gibt keine Verbindung zur Datenbank, keinen Admin-User, keine Seele.

Wir werden das System jetzt "booten". Dazu nutzen wir **Drush** (The **Dru**pal **Sh**ell). Das ist das Schweizer Taschenmesser für Drupal-Entwickler. Drush liegt bereits in deinem Projekt (Composer hat es mitgebracht), wir müssen es nur aufrufen.

### Schritt 1: Der Drush-Check

Geh sicher, dass du im richtigen Ordner bist:

```
cd /var/www/cms
```

Frag Drush kurz nach dem Status, um zu sehen, ob es wach ist:

```
vendor/bin/drush --version
```

Siehst du `Drush version 13.x.x`? Perfekt. Das Werkzeug liegt bereit.

### Schritt 2: Der Installations-Befehl (The Magic Spell)

Jetzt kommt der längste Befehl der ganzen Serie. Wir müssen Drush alles in einem Rutsch sagen:

1. **Wer** soll installiert werden? (Drupal CMS)
2. **Wohin** soll die Datenbank-Verbindung gehen? (Zu PostgreSQL!)
3. **Wie** soll die Seite heißen?
4. **Wer** ist der Chef (Admin)?

**WICHTIG:** Im Befehl unten nutzen wir `pgsql` für PostgreSQL. Wenn du MySQL nutzen würdest, stünde da `mysql`. Das ist der entscheidende Unterschied für unser AI-Setup.

Kopiere den Befehl (pass das Passwort `Geheim123!` an, falls du in Level 4 ein anderes gewählt hast):

```
vendor/bin/drush site:install --db-url=pgsql://drupal:Geheim123!@localhost/drupal_db --site-name="Mein AI Drupal" --account-name=admin --account-pass=admin --locale=de -y
```
- `site:install`: Der Befehl zum Installieren.
- `--db-url`: Der Connection-String. Aufbau: `treiber://user:passwort@host/datenbankname`.
- `--account-pass=admin`: **WARNUNG!** Das machen wir nur für die erste Installation. Ändere das Passwort sofort, wenn du online bist!
- `-y`: Yes, mach einfach.

**Was jetzt passiert:** Du siehst Text über den Bildschirm fliegen.

- *Starting Drupal installation...*
- *Created database tables...*
- *Importing translations...* (Das kann kurz dauern, er lädt das deutsche Sprachpaket).

Wenn am Ende grün steht: **\[success\] Installation complete.** Dann hast du es geschafft. Dein Server lebt.

### Schritt 3: Nacharbeiten (Permissions & Trust)

Drupal ist paranoid (zu Recht). Nach der Installation schließt es die Schreibrechte für die Konfigurationsdatei (`settings.php`) ab. Das ist gut. Aber manchmal gehören Dateien danach dem falschen User, wenn man `sudo` falsch genutzt hat.

Wir machen einen letzten "Permission-Fix", damit du (`sherpa`) und der Webserver (`www-data`) glücklich bleiben:

```
sudo chown -R $USER:www-data /var/www/cms/web
sudo chmod -R 775 /var/www/cms/web/sites/default/files
```

Damit erlauben wir Uploads (Bilder), schützen aber den Kern-Code.

### Schritt 4: Der erste Login

Öffne deinen Browser. Tippe deine Server-IP ein. Du solltest jetzt eine fertige, weiße Drupal-Seite sehen. Oben rechts ist wahrscheinlich schon ein "Log in"-Link oder du bist direkt drin.

Falls nicht, logge dich ein:

- User: `admin`
- Pass: `admin`

**Sherpa-Aufgabe:** Gehe sofort auf *Verwalten > Personen* und ändere das Passwort des Admins. "admin" ist das erste Passwort, das Hacker probieren.

### Schritt 5: Der Status-Bericht

Gehe auf *Verwalten > Berichte > Statusbericht*. Hier siehst du die Gesundheit deines Patienten. Suche nach **"Datenbank"**. Steht dort **PostgreSQL 16.x**? Dann hast du alles richtig gemacht. Du hast Drupal erfolgreich auf einem Enterprise-Tech-Stack installiert.

### Was haben wir erreicht?

Wir haben das Ziel erreicht:

1. **High-Performance:** PHP 8.3 + Opcache + Redis.
2. **Future-Ready:** PostgreSQL für Vektorsuche (RAG).
3. **Secure:** SSH-Only, Firewall, Non-Root-User.
4. **Installed:** Drupal CMS läuft.

Aber ein leeres Drupal ist erst der Anfang. Die Reise endet hier für die Installation, aber sie beginnt erst für den Content.

### Keyfacts: Das nimmst du mit

Bevor wir zum Fazit kommen, hier das Wichtigste:

- **Drush ist King:** Installiere Drupal niemals manuell im Browser, wenn du es automatisieren kannst. `drush site:install` ist reproduzierbar und schnell.
- **Connection Strings:** Der Schlüssel zur Datenbank liegt in der URL. Für Postgres immer `pgsql://...` nutzen.
- **Post-Install Fix:** Prüfe nach der Installation immer kurz die Dateirechte im `files`\-Ordner, um Upload-Fehler zu vermeiden.
- **Security:** Ein Admin-Passwort wie "admin" darf keine 5 Minuten überleben. Ändere es sofort.