---
title: "Level 07 - Config und Redis"
source: "https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f"
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: "Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen."
tags:
  - "clippings"
---
Aktueller Status:

- Die Software liegt in `/var/www/cms`.
- Der Webserver schaut aber immer noch auf `/var/www/html`.

Wenn du deine Seite jetzt aufrufst, siehst du entweder die alte Standard-Seite oder eine Fehlermeldung. Der Kellner (Apache) steht im falschen Raum. Außerdem haben wir versprochen, dein System auf **Enterprise-Niveau** zu heben. Das heißt: Wir installieren **Redis**.

Redis ist ein In-Memory-Speicher. Stell dir das vor wie das Kurzzeitgedächtnis eines Genies. Drupal legt dort Dinge ab, die es oft braucht (z.B. Menüs oder Konfigurationen). Statt diese jedes Mal mühsam aus der PostgreSQL-Datenbank zu kramen (was Millisekunden kostet), holt es sie aus dem RAM (was Nanosekunden dauert). Das ist der Unterschied zwischen "schnell" und "sofort".

### Schritt 1: Den Kellner neu einnorden (Apache VHost)

Wir müssen Apache erklären: "Dein neues Zuhause ist `/var/www/cms/web`." Wichtig: `/web`. Das ist eine Sicherheitsfunktion von Drupal. Die Systemdateien liegen eine Ebene höher, unerreichbar für Hacker. Nur der öffentliche Teil liegt in `/web`.

Öffnen wir die Konfiguration:

```
sudo nano /etc/apache2/sites-available/000-default.conf
```

Lösche den alten Inhalt (oder pass ihn an) und füge diesen Block ein. Er ist perfekt auf Drupal abgestimmt:

```
<VirtualHost *:80>
    # Admin-Email (optional anpassen)
    ServerAdmin webmaster@localhost
    
    # WICHTIG: Der Pfad zum "web"-Ordner
    DocumentRoot /var/www/cms/web

    # Logs für Fehler und Zugriffe
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    # Hier erlauben wir Drupal, die URLs zu steuern (.htaccess)
    <Directory /var/www/cms/web>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Speichern (`STRG+O`, `Enter`) und Beenden (`STRG+X`).

### Schritt 2: Den Turbo einbauen (Redis Installation)

Jetzt installieren wir den Redis-Server und das passende PHP-Modul, damit unser Koch mit dem Turbo reden kann.

```
sudo apt install redis-server php8.3-redis -y
```

Ob Redis läuft, prüfen wir kurz mit dem "Ping"-Befehl:

```
redis-cli ping
```

Antwortet er mit `PONG`? Sehr gut. Der Turbo summt.

### Schritt 3: Das System neu starten

Wir haben die Konfiguration von Apache geändert und ein neues PHP-Modul installiert. Damit das wirksam wird, starten wir die Dienste neu.

```
sudo systemctl restart apache2 php8.3-fpm
```

### Schritt 4: Der Browser-Check

Jetzt wird es spannend. Öffne deinen Browser und gib deine **Server-IP** ein.

Statt der langweiligen Apache-Seite solltest du jetzt (hoffentlich!) eine schlichte Seite sehen, die vielleicht etwas von "Installation" murmelt oder zumindest anders aussieht als vorher. Wenn du eine Fehlermeldung siehst, keine Panik – Drupal ist noch nicht installiert, es fehlen noch die Datenbank-Zugangsdaten. Aber wenn sich das *Aussehen* der Fehlermeldung geändert hat (z.B. Drupal-Styles), wissen wir: Der Kellner hat den richtigen Raum gefunden!

### Was haben wir erreicht?

Wir haben die Infrastruktur final verdrahtet:

1. **Apache** weiß jetzt, wo Drupal liegt (`DocumentRoot`).
2. **AllowOverride All** erlaubt Drupal, schöne URLs (`/ueber-uns`) zu generieren.
3. **Redis** läuft im Hintergrund und wartet darauf, Daten zu cachen.

Alles ist bereit für den großen Moment. Im nächsten Level wecken wir das Monster. Wir installieren Drupal und verbinden es mit der PostgreSQL-Datenbank.

Bereit für die Zündung?

### Keyfacts: Das nimmst du mit

Bevor wir weitermachen, hier das Wichtigste auf einen Blick:

- **DocumentRoot:** Moderne Frameworks legen die öffentliche `index.php` oft in einen Unterordner (z.B. `/web` oder `/public`), um den Core-Code zu schützen. Die Apache-Config muss darauf zeigen.
- **AllowOverride All:** Ohne diese Zeile funktionieren Drupals saubere URLs und Sicherheitsregeln (in der `.htaccess`) nicht.
- **Redis:** Ein Key-Value-Store, der Daten im RAM hält. Für High-Performance-Drupal essenziell, um die Datenbank zu entlasten.
- **Service Restart:** Nach jeder Konfigurationsänderung (Apache) oder Modul-Installation (PHP) müssen die Dienste neu gestartet werden (`systemctl restart`).