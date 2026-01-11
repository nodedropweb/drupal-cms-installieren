---
title: "Level 04 - Postgresql und AI"
source: "https://gemini.google.com/gem/1692906d9713/7688d316efff4b4f"
author:
  - "[[Gemini]]"
published:
created: 2026-01-11
description: "Chatte mit Gemini, dem KI-Assistenten von Google. Lass dich beim Schreiben, Planen, Sammeln von Ideen und vielem mehr von der leistungsfähigen generativen KI von Google unterstützen."
tags:
  - "clippings"
---
Erinnerst du dich an den alten Bibliothekar (MariaDB/MySQL)? Er ist zuverlässig. Er sortiert Bücher nach Alphabet und ISBN. Wenn du ihn fragst: "Gib mir Buch #123", bringt er es sofort.

Aber was, wenn du fragst: "Gib mir etwas, das sich so *anfühlt* wie ein Sonnenuntergang"? Der alte Bibliothekar wäre überfordert.

Hier kommt **PostgreSQL** ins Spiel. Postgres ist nicht nur eine Datenbank. Es ist eine strukturierte Daten-Plattform. Und mit der Erweiterung **pgvector** rüsten wir sie für das Zeitalter der Künstlichen Intelligenz (AI) auf.

### Warum machen wir das? (Der RAG-Faktor)

Wir bauen diesen Server für die Zukunft. Das Stichwort heißt **RAG** (Retrieval Augmented Generation). Wenn du später Dokumente (PDFs, Wikis) in dein Drupal lädst und eine KI (wie ChatGPT oder Llama) darüber befragen willst, muss die KI den *Inhalt* verstehen, nicht nur Keywords. Dazu wandelt man Text in Zahlenreihen um (Vektoren). MariaDB lernt das gerade erst. PostgreSQL kann das schon lange und ist der Industriestandard dafür.

Wir installieren heute das Gedächtnis, das deine Dokumente später *verstehen* wird.

### Schritt 1: Das Upgrade installieren

Auf Ubuntu 24.04 LTS liegt PostgreSQL 16 bereits in den Regalen. Und das Beste: Die Vektor-Erweiterung liegt auch bereit (im "Universe"-Repository).

Installieren wir den Server und die KI-Erweiterung:

```
sudo apt install postgresql postgresql-contrib postgresql-16-pgvector -y
```
- `postgresql-contrib`: Enthält wichtige Zusatz-Tools.
- `postgresql-16-pgvector`: Der Zauberstab für Vektorsuche.

### Schritt 2: Den Tresorraum betreten

PostgreSQL ist strenger als MySQL. Es nutzt standardmäßig keine Passwörter für lokale User, sondern verlässt sich darauf, wer du im Betriebssystem bist (Peer Authentication). Um Befehle zu geben, müssen wir kurz zum System-User `postgres` werden:

```
sudo -i -u postgres
```

Dein Prompt sollte sich ändern (oft zu `postgres@...`). Du bist jetzt der Datenbank-Administrator.

### Schritt 3: Benutzer und Datenbank erstellen

Wir brauchen einen Benutzer für Drupal und einen "leeren Raum" (die Datenbank).

1. **User anlegen:** Wir nennen ihn `drupal` (kreativ, ich weiß).
	```
	createuser --interactive --pwprompt
	```
	- Gib den Namen der Rolle ein: `drupal`
	- Gib das Passwort ein: Wähle ein **sicheres Passwort** (z.B. `Geheim123!`). Merk es dir gut!
	- Soll die neue Rolle ein "Superuser" sein? -> `n` (Nein)
	- Soll sie Datenbanken erstellen dürfen? -> `n` (Nein)
	- Soll sie Rollen erstellen dürfen? -> `n` (Nein)
2. **Datenbank anlegen:** Wir erstellen den Datentopf und schenken ihn dem User `drupal`.
	```
	createdb -O drupal drupal_db
	```
	- `-O`: Owner (Besitzer).
	- `drupal_db`: Der Name der Datenbank.

### Schritt 4: Die AI-Magie aktivieren (Extension)

Jetzt kommt der Schritt, den die meisten vergessen. Wir müssen die Vektor-Funktion in unserer neuen Datenbank explizit einschalten.

Wir öffnen die SQL-Konsole:

```
psql
```

Wir verbinden uns mit der Drupal-Datenbank:

```
\c drupal_db
```

(Antwort: `You are now connected to database "drupal_db"...`)

Wir aktivieren `pgvector`:

```
CREATE EXTENSION vector;
```

(Antwort: `CREATE EXTENSION`)

Und zur Sicherheit auch `trigram` (hilft bei normaler Textsuche enorm):

```
CREATE EXTENSION pg_trgm;
```

Verlasse die Konsole und den User: Tippe `\q` (Enter), dann `exit` (Enter). Du solltest wieder dein normaler User (`sherpa` oder dein Name) sein.

### Schritt 5: Authentifizierung prüfen (Der Türsteher)

Damit Drupal sich später per Passwort einloggen kann, müssen wir sicherstellen, dass Postgres Passwörter (SCRAM-SHA-256) akzeptiert. Das ist in Ubuntu 24.04 meist Standard, aber wir vertrauen nicht, wir prüfen.

Wir schauen kurz in die Config-Datei (als Sudo):

```
sudo grep "scram-sha-256" /etc/postgresql/16/main/pg_hba.conf
```

Wenn du Zeilen siehst, die mit `host ... scram-sha-256` enden, ist alles gut. PostgreSQL spricht die modernste Verschlüsselung.

### Was haben wir erreicht?

Du hast gerade eine **Vektor-Datenbank** installiert.

- **Apache** (Kellner) wartet auf Gäste.
- **PHP** (Koch) ist bereit zu kochen (und hat dank `php8.3-pgsql` aus Level 3 auch schon den passenden Pfannenwender für Postgres).
- **PostgreSQL** (Gedächtnis) ist bereit, nicht nur Texte, sondern *Bedeutungen* zu speichern.

Das Backend steht. Aber wer organisiert die Zutaten? Wer holt das Drupal-Paket? Im nächsten Level stellen wir den Logistik-Manager ein.

Bereit für Composer?

### Keyfacts: Das nimmst du mit

Bevor wir weitermachen, hier das Wichtigste auf einen Blick:

- **AI-Ready:** Mit `pgvector` wird deine Datenbank fähig, Ähnlichkeiten zwischen Texten zu berechnen (Basis für RAG/KI).
- **Sicherheit:** PostgreSQL nutzt standardmäßig System-User-Rechte (`peer`). Für Web-Apps wie Drupal richten wir explizit Passwort-Auth ein.
- **Extensions:** Erweiterungen wie `vector` oder `pg_trgm` müssen *pro Datenbank* einmalig via SQL (`CREATE EXTENSION`) aktiviert werden.
- **Strikte Trennung:** Wir nutzen einen dedizierten User (`drupal`) und eine dedizierte DB (`drupal_db`), niemals den Superuser `postgres` für die Webseite.