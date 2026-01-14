<?php
/**
 * Icon Helper für H2-Überschriften mit Material Symbols
 */

$icon_map = [
    'Unsere Services' => ['icon' => 'star', 'class' => 'icon-service'],
    'Kontakt' => ['icon' => 'mail', 'class' => 'icon-service'],
    
    'Preis' => ['icon' => 'euro-symbol', 'class' => 'icon-price'],
    'Monopoly Go Partnerevent Hauptpreis' => ['icon' => 'trophy', 'class' => 'icon-game'],
    'Häufig gestellte Fragen (FAQ)' => ['icon' => 'help-center', 'class' => 'icon-help'],
    'Jetzt Partner buchen & Würfel sparen!' => ['icon' => 'casino', 'class' => 'icon-cta'],
    
    'Preisliste' => ['icon' => 'euro-symbol', 'class' => 'icon-price'],
    
    'Voraussetzungen' => ['icon' => 'checklist', 'class' => 'icon-service'],
    'Ablauf' => ['icon' => 'timeline', 'class' => 'icon-service'],
    'Durchführung' => ['icon' => 'play-circle', 'class' => 'icon-game'],
    'Direkter Vergleich' => ['icon' => 'compare-arrows', 'class' => 'icon-info'],
    'Sicherheit' => ['icon' => 'shield-lock', 'class' => 'icon-security'],
    'Jetzt Würfel sichern!' => ['icon' => 'casino', 'class' => 'icon-cta'],
    
    'Beispiel-Accounts' => ['icon' => 'account-circle', 'class' => 'icon-service'],
    'Häufig gestellte Fragen' => ['icon' => 'help-center', 'class' => 'icon-help'],
    
    'Details & Garantie' => ['icon' => 'info', 'class' => 'icon-info'],
    
    'Verfügbare Anleitungen' => ['icon' => 'menu-book', 'class' => 'icon-service'],
    
    'Warum den Freundschaftsbalken füllen?' => ['icon' => 'favorite', 'class' => 'icon-game'],
    'Was du brauchst' => ['icon' => 'checklist', 'class' => 'icon-service'],
    'Schritt-für-Schritt Anleitung' => ['icon' => 'timeline', 'class' => 'icon-service'],
    'Tipps & Troubleshooting' => ['icon' => 'help-center', 'class' => 'icon-help'],
    'Keine Zeit oder Probleme?' => ['icon' => 'schedule', 'class' => 'icon-help'],
    
    'Verfügbare Downloads' => ['icon' => 'download', 'class' => 'icon-info'],
    'Alle Downloads fertig?' => ['icon' => 'done-all', 'class' => 'icon-service'],
    
    'Kontaktformular' => ['icon' => 'mail', 'class' => 'icon-service'],
    
    'Betreiber der Website' => ['icon' => 'account-circle', 'class' => 'icon-service'],
    
    '1. Verantwortlicher' => ['icon' => 'account-circle', 'class' => 'icon-service'],
    '2. Erhebung und Speicherung personenbezogener Daten' => ['icon' => 'shield-lock', 'class' => 'icon-security'],
];

/**
 * Rendert H2 mit Icon
 */
function h2_with_icon($title, $icon_name = null, $icon_class = 'icon-service') {
    global $icon_map;
    
    if (isset($icon_map[$title])) {
        $icon_name = $icon_map[$title]['icon'];
        $icon_class = $icon_map[$title]['class'];
    }
    
    if (!$icon_name) {
        return '<h2>' . htmlspecialchars($title) . '</h2>';
    }
    
    $icon_path = '/assets/material-symbols/' . htmlspecialchars($icon_name) . '.svg';
    
    return '<h2>' .
           '<img src="' . $icon_path . '" alt="" class="icon ' . htmlspecialchars($icon_class) . '" loading="lazy">' .
           htmlspecialchars($title) .
           '</h2>';
}
?>
