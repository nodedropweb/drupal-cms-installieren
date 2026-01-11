---
title: "Level 01 - Ubuntu installieren"
source: "https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f"
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: "Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen."
tags:
  - "clippings"
---
Willkommen in der Matrix. Vor dir blinkt wahrscheinlich gerade ein Cursor in einem schwarzen Fenster. Das ist dein neuer Server. Er ist frisch, er ist schnell, und er ist... **gefährlich offen**.

Wenn du einen VPS (Virtual Private Server) mietest, bekommst du meistens den Schlüssel zum Haupteingang: Den `root`\-Zugang. Viele bleiben einfach `root`. Das ist bequem. Du darfst alles. Aber es ist auch tödlich. Als `root` zu arbeiten ist, wie in einer Ritterrüstung durch ein Minenfeld zu joggen. Ein falscher Befehl (`rm -rf /`), und dein Server ist Geschichte. Ein offener Port, und Hacker haben Vollzugriff.

Heute härten wir dein System. Wir verwandeln die "nackte" Ubuntu-Installation in eine Festung.

### Schritt 1: Der erste Kontakt (Login)

Wir verbinden uns jetzt per SSH (Secure Shell) mit deinem Server. Das ist der verschlüsselte Tunnel, durch den wir arbeiten. Öffne dein Terminal auf dem PC und tippe:

```
ssh root@DEINE-SERVER-IP
```

*(Ersetze `DEINE-SERVER-IP` natürlich mit der Zahlenkolonne, die dir dein Hoster geschickt hat).*

Wirst du nach einem Fingerprint gefragt? Schreib `yes`. Wirst du nach dem Passwort gefragt? Tippe es ein (du siehst keine Sternchen, das ist normal – Linux ist diskret).

Bist du drin? Siehst du etwas wie `root@ubuntu-server:~#`? Perfekt. Du bist der Gott dieses Systems. Aber Götter sollten sich rar machen.

### Schritt 2: Identitätswechsel (Neuen User anlegen)

Wir erstellen jetzt dein "sterbliches Ich". Einen Benutzer, mit dem du arbeitest. Dieser Benutzer darf Befehle ausführen, muss aber für kritische Dinge (wie Installationen) kurz um Erlaubnis fragen (`sudo`). Das ist dein Airbag.

Ersetze `sherpa` durch deinen Wunschnamen:

```
adduser sherpa
```

Beantworte die Fragen (Passwort setzen, Name etc. – Telefonnummer kannst du mit ENTER überspringen).

Jetzt geben wir diesem neuen User die Macht, den Server zu verwalten (sudo-Rechte):

```
usermod -aG sudo sherpa
```
- `usermod`: User modifizieren.
- `-aG`: Add Group (Füge zur Gruppe hinzu).
- `sudo`: Die Gruppe der Administratoren.

**Profi-Move:** Jetzt testen wir den neuen User, *bevor* wir uns ausloggen. Öffne ein **zweites, neues Terminal-Fenster** auf deinem PC und versuche:

```
ssh sherpa@DEINE-SERVER-IP
```

Wenn das klappt: Glückwunsch! Du hast jetzt einen sicheren Zugang. Ab jetzt nutzen wir **nie wieder** `root` für den Login. Logge dich im alten Fenster aus (`exit`) und schließe es. Arbeite nur noch als dein neuer User weiter.

### Schritt 3: Das Schutzschild hochfahren (Firewall)

Aktuell ist dein Server wie ein Haus ohne Türen. Jeder Port ist offen. Wir installieren den Türsteher: **UFW** (Uncomplicated Firewall).

Standardmäßig lassen wir **nichts** rein, außer SSH (damit wir uns nicht selbst aussperren).

Tippe diese Befehle nacheinander ein (du musst jetzt dein Passwort eingeben, weil du `sudo` nutzt):

```
sudo ufw allow OpenSSH
sudo ufw enable
```

Bestätige mit `y`. Zack. Die Schotten sind dicht. Dein Server antwortet jetzt nur noch auf dem SSH-Kanal. Web-Traffic (Port 80/443) lassen wir erst später rein, wenn der Apache-Server bereit ist.

### Schritt 4: Zähneputzen (System Update)

Dein Ubuntu-Image ist vielleicht schon ein paar Wochen alt. Bevor wir Software installieren, bringen wir das System auf den neuesten Stand. Das ist wie Händewaschen vor dem Kochen – Pflicht.

```
sudo apt update && sudo apt upgrade -y
```
- `update`: Holt die neuesten Listen ("Was gibt es Neues?").
- `upgrade`: Installiert die neuen Versionen.
- `-y`: Sagt automatisch "Ja" zu allen Fragen.

Das kann kurz dauern. Hol dir einen Kaffee. ☕ Wenn ein pinker Bildschirm (Daemons restart) erscheint: Einfach ENTER drücken.

### Mission Complete?

Fast. Dein Fundament steht. Du hast einen **Ubuntu 24.04 LTS Server**, der aktuell und abgesichert ist. Du hast einen dedizierten User und eine Firewall, die Wache hält.

Aber noch ist der Server stumm. Er kann keine Webseiten ausliefern. Er ist ein leerer Tresor. Im nächsten Level holen wir den Oberkellner dazu, der die Gäste empfängt.

### Keyfacts: Das nimmst du mit

Bevor wir in die Details gehen, hier das Wichtigste auf einen Blick:

- **Nie als Root:** Arbeite immer mit einem personalisierten User und `sudo`. Das verhindert fatale Unfälle und erhöht die Sicherheit.
- **Firewall First:** Mit `ufw` sperren wir alles aus, was nicht explizit eingeladen ist. Aktuell darf nur SSH rein.
- **Up-to-Date:** Ein `apt upgrade` ist der erste Schritt auf jedem neuen Server. Veraltete Software ist das Tor für Hacker.
- **LTS Stabilität:** Mit Ubuntu 24.04 LTS hast du für Jahre Ruhe bei Sicherheitsupdates.