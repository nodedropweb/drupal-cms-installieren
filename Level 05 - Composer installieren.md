---
title: Level 05 Composer installieren
source: https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen.
tags:
  - clippings
---
Stell dir vor, du willst ein Auto bauen. Früher (in der „guten“ alten FTP-Zeit) bist du zum Schrottplatz gefahren, hast dir Reifen von hier und ein Lenkrad von dort geholt, alles in einen ZIP-Ordner geworfen und gehofft, dass es passt. Meistens hat es geklemmt.

Heute bauen wir professionell. Wir nutzen **Composer**. Das ist dein Logistik-Manager. Du sagst ihm nur: „Ich will Drupal CMS.“ Composer sagt: „Okay, dafür brauche ich Symfony-Komponenten in Version 6.4, Guzzle für HTTP-Requests und Twig für das Template. Ich hole das alles, prüfe die Versionen auf Kompatibilität und lege es dir sauber ins Regal.“

Ohne Composer ist modernes Drupal unmöglich. Mit Composer ist es ein Kinderspiel.

### Schritt 1: Den Manager anheuern (Download & Check)

Wir installieren Composer nicht einfach blind über `apt` (die Version dort ist oft veraltet). Wir holen uns das Original direkt von der Quelle.

Aber Vorsicht: Da wir Skripte aus dem Internet laden, prüfen wir den **Hash** (den digitalen Fingerabdruck). Wenn der Download manipuliert wurde, bricht die Installation ab.

Kopiere diesen Block **komplett** in dein Terminal. Er lädt den Installer, prüft ihn und führt ihn aus:

```
php -r "copy('[https://getcomposer.org/installer](https://getcomposer.org/installer)', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

*(Hinweis: Der Hash-Code ändert sich manchmal. Wenn hier "Installer corrupt" steht, besuche getcomposer.org/download für den aktuellsten Code – aber meistens passt dieser hier lange).*

### Schritt 2: Beförderung zur Globalen Instanz

Aktuell liegt die Datei `composer.phar` nur in deinem aktuellen Ordner rum. Wir wollen den Logistiker aber im ganzen Haus rufen können, egal in welchem Zimmer (Verzeichnis) wir sind.

Wir verschieben ihn in den System-Ordner `/usr/local/bin` und benennen ihn um:

```
sudo mv composer.phar /usr/local/bin/composer
```

Jetzt testen wir, ob er auf Zuruf reagiert. Tippe einfach:

```
composer --version
```

Siehst du etwas wie `Composer version 2.8.x`? Perfekt. Der Manager ist eingestellt.

### Schritt 3: Die Goldene Regel (Don't be Root)

Jetzt ein **Sherpa-Warnhinweis**, der dir später viel Schmerz erspart: **Benutze Composer NIEMALS als `root`!**

Wenn du als `root` Pakete installierst, gehören die Dateien auch `root`. Dein Webserver (Apache) und dein normaler User dürfen sie dann nicht lesen oder ändern. Das führt zu bizarren „Permission Denied“-Fehlern, an denen schon Profis verzweifelt sind.

- ✅ Richtig: `composer require drupal/modulname` (als User `sherpa`)
- ❌ Falsch: `sudo composer require ...`

Composer wird dich sogar warnen, wenn du es versuchst. Hör auf ihn.

### Schritt 4: Tuning (Parallel Downloads)

Composer ist schnell, aber wir können ihn schneller machen. Früher brauchte man dafür Plugins (`hirak/prestissimo`), heute ist das meiste schon eingebaut. Wir stellen sicher, dass Composer den Cache effizient nutzt.

Führe diesen Befehl aus (als dein normaler User, **nicht** mit sudo!), um zu prüfen, ob alles grün ist:

```
composer diagnose
```

Wenn er sich über fehlende Rechte oder falsche PHP-Einstellungen beschwert, fixen wir das jetzt. Meistens sollte aber alles „OK“ sein, weil wir in Level 3 unser PHP-Memory-Limit schon auf 512MB gesetzt haben. Composer liebt RAM.

### Was haben wir erreicht?

Die Infrastruktur ist jetzt komplett handlungsfähig:

1. **OS:** Ubuntu stabil.
2. **Web:** Apache empfangsbereit.
3. **App:** PHP getunt.
4. **Data:** Postgres vector-ready.
5. **Logistics:** Composer startklar.

Das Team steht. Aber das Stadion ist noch leer. Im nächsten Level rufen wir den Star auf die Bühne. Wir laden das **Drupal CMS** herunter.

Bereit für den großen Download?

### Keyfacts: Das nimmst du mit

Bevor wir weitermachen, hier das Wichtigste auf einen Blick:

- **Single Source of Truth:** Composer verwaltet alle Bibliotheken und Abhängigkeiten zentral. Nie mehr manuelles Dateien-Schieben.
- **Security Check:** Bei der Installation prüfen wir immer den Hash der Datei, um keine manipulierte Software untergeschoben zu bekommen.
- **Global Access:** Durch das Verschieben nach `/usr/local/bin` ist der Befehl `composer` überall verfügbar.
- **No Sudo:** Führe Composer-Befehle immer als dein normaler User aus, um Rechte-Probleme mit dem Webserver zu vermeiden.