<?php
/**
 * RSS Generator für die Drupal Infrastructure Academy
 * Erstellt eine valide feed.xml aus Markdown-Dateien.
 */

$baseUrl = "https://github.com/nodedropweb/drupal-cms-installieren/blob/main/";
$rawUrl = "https://raw.githubusercontent.com/nodedropweb/drupal-cms-installieren/main/";
$feedFile = 'feed.xml';

// Alle Markdown-Dateien im Verzeichnis finden
$files = glob("*.md");

// Sortierung: Wir wollen Level 00 bis Level 09 in der richtigen Reihenfolge
sort($files);

$items = [];

foreach ($files as $file) {
    if ($file === 'README.md') continue; // README ignorieren

    $content = file_get_contents($file);
    
    // Einfaches Parsing für den Titel aus dem YAML-Frontmatter oder Dateinamen
    $title = $file;
    if (preg_match('/title:\s*"(.*)"/', $content, $matches)) {
        $title = $matches[1];
    } elseif (preg_match('/title:\s*(.*)/', $content, $matches)) {
        $title = trim($matches[1]);
    }

    // Zeitstempel der Datei
    $date = date(DATE_RSS, filemtime($file));
    $link = $baseUrl . urlencode($file);
    $guid = md5($file);
    
    // Content für den Feed (Markdown-Inhalt)
    $description = htmlspecialchars($content);

    $items[] = "
        <item>
            <title><![CDATA[$title]]></title>
            <link>$link</link>
            <guid isPermaLink=\"false\">$guid</guid>
            <pubDate>$date</pubDate>
            <description><![CDATA[$description]]></description>
        </item>";
}

// RSS Header & Zusammenbau
$rssContent = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
$rssContent .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">' . PHP_EOL;
$rssContent .= '<channel>' . PHP_EOL;
$rssContent .= '    <title>Drupal CMS Infrastruktur Guide</title>' . PHP_EOL;
$rssContent .= '    <link>https://github.com/nodedropweb/drupal-cms-installieren</link>' . PHP_EOL;
$rssContent .= '    <description>Vom leeren Server zur AI-Ready Drupal Enterprise Plattform.</description>' . PHP_EOL;
$rssContent .= '    <language>de-de</language>' . PHP_EOL;
$rssContent .= implode(PHP_EOL, $items) . PHP_EOL;
$rssContent .= '</channel>' . PHP_EOL;
$rssContent .= '</rss>';

// Datei schreiben
file_put_contents($feedFile, $rssContent);
echo "Feed erfolgreich generiert: $feedFile\n";
