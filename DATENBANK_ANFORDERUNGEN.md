# Datenbank-Anforderungen: Downloads und Categories

## Zusammenfassung (Antwort auf die Frage)

**Frage:** Welche Einträge sollte downloads in der Datenbank haben? Categories? Was muss alles laut Code drin sein?

**Antwort:** Die folgenden Tabellen und Felder MÜSSEN in der Datenbank vorhanden sein:

---

## 1. CATEGORIES Tabelle (war komplett fehlend!)

### Pflichtfelder:
```
✓ id              - Primärschlüssel (AUTO_INCREMENT/SERIAL)
✓ name            - Name der Kategorie (z.B. "Android Apps")
✓ slug            - URL-freundlicher Name (z.B. "android-apps")
✓ description     - Beschreibung der Kategorie
✓ icon            - Icon-Pfad oder Emoji (z.B. "📱")
✓ sort_order      - Sortierreihenfolge für Anzeige
✓ created_at      - Erstellungsdatum
```

### Beispieldaten:
- Android Apps (slug: android-apps)
- Windows Tools (slug: windows-tools)
- Scripts (slug: scripts)

---

## 2. DOWNLOADS Tabelle

### Vorhandene Felder (bereits im Schema):
```
✓ id              - Primärschlüssel
✓ filename        - Dateiname (z.B. "app.apk")
✓ filepath        - Dateipfad (z.B. "apk/app_123456.apk")
✓ filetype        - Dateityp: 'apk', 'scripts', oder 'exe'
✓ filesize        - Dateigröße in Bytes
✓ version         - Versionsnummer (z.B. "1.0.0")
✓ description     - Beschreibung
✓ download_count  - Anzahl Downloads
✓ active          - Aktiv/Inaktiv (Boolean)
✓ created_at      - Erstellungsdatum
✓ updated_at      - Aktualisierungsdatum
```

### FEHLENDE Felder (wurden jetzt hinzugefügt):
```
+ name              - Anzeigename (unterschiedlich von filename)
+ file_size         - Lesbare Dateigröße (z.B. "2.5 MB")
+ file_type         - Anzeigetyp (z.B. "Android APK")
+ download_link     - Haupt-Download-URL
+ alternative_link  - Alternativer Download-Link
+ category_id       - Fremdschlüssel zu categories Tabelle
+ created_by        - Fremdschlüssel zu users Tabelle (wer hat es hochgeladen)
```

### Indizes:
```
✓ idx_filetype     - Index auf filetype
✓ idx_category_id  - Index auf category_id
✓ idx_created_by   - Index auf created_by
✓ idx_active       - Index auf active
```

---

## 3. COMMENTS Tabelle

### Vorhandene Felder:
```
✓ id           - Primärschlüssel
✓ user_id      - Fremdschlüssel zu users
✓ domain       - Domain-Name (jetzt NULL erlaubt)
✓ content_id   - Content-ID (jetzt NULL erlaubt)
✓ comment      - Kommentartext
✓ status       - Status: 'approved', 'pending', 'spam'
✓ created_at   - Erstellungsdatum
```

### FEHLENDE Felder (wurden jetzt hinzugefügt):
```
+ download_id   - Direkte Referenz zu downloads Tabelle
+ comment_text  - Alternativer Feldname (wird vom files-Bereich verwendet)
```

**Wichtiger Hinweis:** Die comments-Tabelle hat sowohl `comment` als auch `comment_text` Felder für Rückwärtskompatibilität:
- Der Admin-Bereich nutzt das Feld `comment`
- Der Files-Bereich (`/babixgo.de/files/`) nutzt das Feld `comment_text`
- Beide Felder sollten im Anwendungscode mit dem gleichen Wert befüllt werden
- Die Felder `domain` und `content_id` werden für allgemeine Kommentare verwendet (z.B. auf Seiten)
- Das Feld `download_id` wird speziell für Kommentare zu Downloads verwendet

### Indizes:
```
✓ idx_user_id         - Index auf user_id
✓ idx_domain_content  - Index auf domain, content_id
✓ idx_download_id     - Index auf download_id (NEU)
✓ idx_status          - Index auf status
```

---

## 4. USERS Tabelle

### Vorhandene Felder:
```
✓ id                    - Primärschlüssel
✓ username              - Benutzername
✓ email                 - E-Mail
✓ password_hash         - Passwort-Hash
✓ description           - Benutzerbeschreibung
✓ friendship_link       - Freundschafts-Link Code
✓ is_verified           - Verifiziert (Boolean)
✓ verification_token    - Verifizierungs-Token
✓ reset_token           - Passwort-Reset-Token
✓ reset_token_expires   - Token-Ablaufdatum
✓ role                  - Rolle: 'user' oder 'admin'
✓ created_at            - Erstellungsdatum
✓ updated_at            - Aktualisierungsdatum
```

### FEHLENDE Felder (wurden jetzt hinzugefügt):
```
+ comment_count   - Anzahl der Kommentare des Benutzers
+ email_verified  - E-Mail-Verifizierungsstatus (Boolean)
```

---

## Wichtige Beziehungen (Foreign Keys)

```
downloads.category_id    → categories.id (ON DELETE SET NULL)
downloads.created_by     → users.id (ON DELETE SET NULL)
comments.user_id         → users.id (ON DELETE CASCADE)
comments.download_id     → downloads.id (ON DELETE CASCADE)
```

---

## Wo wird was verwendet?

### Admin-Bereich (`/admin/downloads.php`):
Verwendet: `filename`, `filepath`, `filetype`, `filesize`, `version`, `description`, `active`, `download_count`

### Files-Bereich (`/babixgo.de/files/`):
Verwendet: `name`, `description`, `file_size`, `file_type`, `download_link`, `alternative_link`, `category_id`, `created_by`

### Beide Bereiche nutzen jetzt ALLE Felder:
- Alte Felder bleiben erhalten (Admin-Funktionalität)
- Neue Felder ermöglichen Files-Funktionalität
- Vollständige Kompatibilität mit bestehendem Code

---

## Status

✅ **ERLEDIGT:**
- Categories-Tabelle erstellt mit allen benötigten Feldern
- Downloads-Tabelle erweitert mit fehlenden Feldern
- Comments-Tabelle erweitert mit download_id und comment_text
- Users-Tabelle erweitert mit comment_count und email_verified
- Beispiel-Kategorien eingefügt
- MySQL und PostgreSQL Schemas aktualisiert
- Foreign Keys und Indizes hinzugefügt
- Migrationsanleitungen erstellt

---

## Dateien geändert/erstellt:

1. `/babixgo.de/shared/create-tables.sql` - MySQL Schema (aktualisiert)
2. `/babixgo.de/shared/create-tables-postgres.sql` - PostgreSQL Schema (aktualisiert)
3. `DATABASE_SCHEMA_REQUIREMENTS.md` - Detaillierte Anforderungsdokumentation (neu)
4. `DATABASE_MIGRATION_GUIDE.md` - Migrationsanleitung für bestehende DBs (neu)
5. `DATENBANK_ANFORDERUNGEN.md` - Diese Zusammenfassung auf Deutsch (neu)

---

## Nächste Schritte

1. ✅ Schema-Dateien sind aktualisiert
2. 🔄 Für neue Installation: `create-tables.sql` oder `create-tables-postgres.sql` verwenden
3. 🔄 Für bestehende Datenbank: `DATABASE_MIGRATION_GUIDE.md` befolgen
4. 🔄 Datenbank neu erstellen oder migrieren
5. 🔄 Kategorien und Downloads über Admin-Panel hinzufügen
