---
title: "Level 06 - Das System - Drupal CMS herunterladen"
source: "https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f"
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: "Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen."
tags:
  - "clippings"
---
Es ist soweit. Das Fundament ist gegossen (Ubuntu), der Türsteher steht (Apache), der Koch hat den Herd vorgeheizt (PHP), das Gedächtnis ist aktiv (Postgres) und der Logistiker (Composer) wartet auf Anweisungen.

Jetzt holen wir den Star auf die Bühne.

Wir laden das **Drupal CMS** herunter. Wichtig: Wir laden keine Zip-Datei. Wir nutzen Composer. Warum? Weil Drupal aus tausenden kleinen Bausteinen besteht (Symfony, Guzzle, Twig). Composer weiß genau, welche Versionen zusammenpassen.

### Schritt 1: Das Bauland vorbereiten (Rechte setzen)

Unter Linux gehört der Ordner `/var/www` (wo Webseiten wohnen) standardmäßig dem `root`\-Benutzer. Wenn du als normaler User (`sherpa`) dort etwas speichern willst, knallt dir Linux die Tür vor der Nase zu: *"Permission denied"*.

Wir ändern das. Wir sagen dem Server: "Dieser Ordner gehört jetzt mir und der Webserver-Gruppe."

Kopiere diesen Befehl (er nutzt die Variable `$USER`, um automatisch deinen Namen einzusetzen):

```
sudo chown -R $USER:www-data /var/www
```

Und wir stellen sicher, dass wir (und der Webserver) dort auch schreiben dürfen:

```
sudo chmod -R 775 /var/www
```
- `chown`: Change Owner (Besitzer wechseln).
- `chmod`: Change Mode (Rechte ändern). 775 bedeutet: Ich darf alles, die Gruppe darf alles, der Rest darf nur gucken.

### Schritt 2: Die Werkzeugkiste (Unzip & Git)

Damit Composer die Pakete auspacken kann, braucht er kleine Helferlein, die auf manchen Minimal-Servern fehlen. Wir installieren sie schnell nach:

```
sudo apt install unzip zip git -y
```

### Schritt 3: Der Download (Composer Action)

Jetzt kommt der Befehl, der alles verändert. Wir sagen Composer: "Erstelle ein neues Projekt basierend auf dem Paket `drupal/cms`. Packe es in den Ordner `/var/www/cms`."

Tippe ein:

```
composer create-project drupal/cms /var/www/cms
```

**Was jetzt passiert:** Lehn dich zurück. ☕ Du wirst sehen, wie hunderte Zeilen Text über deinen Bildschirm rattern.

- *Downloading drupal/cms...*
- *Installing symfony/http-kernel...*
- *Installing guzzlehttp/guzzle...*

Das ist Composer bei der Arbeit. Er rennt durch das Internet, holt die neuesten, sichersten Versionen aller Bibliotheken und baut dir daraus das modernste CMS der Welt zusammen.

**Wichtig:** Falls er dich am Ende fragt: *"Do you want to remove the existing VCS (.git) history?"* -> Antworte mit `Y` (Yes). Wir wollen ein frisches Projekt ohne die Entwicklungs-Historie von Drupal selbst.

### Schritt 4: Der Inventar-Check

Wenn der Cursor wieder blinkt und keine roten Fehlermeldungen zu sehen sind: Glückwunsch!

Schauen wir nach, was wir bekommen haben:

```
ls -la /var/www/cms
```

Du siehst jetzt eine Struktur wie:

- `composer.json` (Der Bauplan)
- `vendor/` (Hier liegen die Bibliotheken)
- `web/` (Hier liegt das eigentliche Drupal, das öffentlich sichtbar sein wird)

### Warum funktioniert die Seite noch nicht?

Wenn du jetzt deine Server-IP im Browser aufrufst, siehst du immer noch die alte Apache-Standardseite ("It works!"). Warum? Weil unser Kellner (Apache) immer noch auf das alte Regal `/var/www/html` starrt. Er weiß noch nicht, dass das 5-Gänge-Menü jetzt im neuen Raum `/var/www/cms/web` serviert wird.

Wir müssen dem Kellner einen neuen Laufzettel geben. Das machen wir im nächsten Level.

### Keyfacts: Das nimmst du mit

Bevor wir weitermachen, hier das Wichtigste auf einen Blick:

- **Drupal CMS vs. Core:** Wir nutzen `drupal/cms` (Starshot), weil es moderne Features und "Rezepte" mitbringt, im Gegensatz zum nackten `drupal/core`.
- **Kein Root für Composer:** Wir haben erst die Rechte des Ordners (`chown`) an unseren User übergeben, damit wir Composer *ohne* sudo ausführen können.
- **Document Root:** Die eigentliche Website liegt nicht im Hauptordner, sondern im Unterordner `/web`. Das ist ein Sicherheits-Feature (Vendor-Dateien bleiben so unerreichbar).
- **Git & Unzip:** Diese Tools sind essenziell für Composer, da viele Pakete als Zip oder Git-Repo geladen werden.