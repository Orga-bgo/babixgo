# GitHub Actions Workflows

## SFTP Deployment Workflow

### Datei: `sftp-deploy.yml`

Diese GitHub Action ermöglicht das manuelle Deployment des gesamten Repositories (inkl. Secrets und Variablen) auf einen Webserver via SFTP.

### Funktionen

- ✅ **Nur manuelle Auslösung**: Der Workflow kann nur manuell über die GitHub Actions UI gestartet werden
- ✅ **Sicherheitsbestätigung**: Erfordert die Eingabe von "deploy" zur Bestätigung
- ✅ **Vollständiges Repository**: Lädt das komplette Repository hoch
- ✅ **Secrets & Variablen**: Erstellt automatisch eine `.env` Datei mit allen konfigurierten Secrets
- ✅ **SFTP Upload**: Sichere Übertragung via SFTP

### Benötigte Secrets

Die folgenden Secrets müssen in den GitHub Repository Settings konfiguriert werden:

#### SFTP Verbindung
- `SFTP_SERVER` - Hostname oder IP-Adresse des Webservers
- `SFTP_USERNAME` - SFTP Benutzername
- `SFTP_PASSWORD` - SFTP Passwort
- `SFTP_PORT` - SFTP Port (optional, Standard: 22)
- `SFTP_REMOTE_DIR` - Zielverzeichnis auf dem Server

#### Datenbank Konfiguration
- `DB_HOST` - Datenbank Host
- `DB_NAME` - Datenbank Name
- `DB_USER` - Datenbank Benutzer
- `DB_PASSWORD` - Datenbank Passwort

#### SMTP Konfiguration
- `SMTP_HOST` - SMTP Server
- `SMTP_PORT` - SMTP Port
- `SMTP_USER` - SMTP Benutzername
- `SMTP_KEY` - SMTP Passwort/API Key

#### Site Konfiguration
- `SITE_URL` - URL der Website

### Verwendung

1. Gehe zu **Actions** im GitHub Repository
2. Wähle **"Manual SFTP Deployment"** aus der Liste der Workflows
3. Klicke auf **"Run workflow"**
4. Gebe **"deploy"** in das Bestätigungsfeld ein
5. Klicke auf **"Run workflow"** um das Deployment zu starten

### Sicherheitshinweise

⚠️ **Wichtig**: 
- Die `.env` Datei mit Secrets wird während des Deployments erstellt
- Stelle sicher, dass `.env` in der `.gitignore` des Zielservers enthalten ist
- Überprüfe die SFTP-Verbindungsdaten vor dem ersten Deployment
- Der Workflow löscht keine Dateien auf dem Server (`delete_remote_files: false`)

### Troubleshooting

**Problem**: Workflow schlägt mit "Deployment cancelled" fehl
- **Lösung**: Stelle sicher, dass du exakt "deploy" (ohne Anführungszeichen) eingibst

**Problem**: SFTP Verbindung schlägt fehl
- **Lösung**: Überprüfe die SFTP Secrets (Server, Username, Password, Port)

**Problem**: Dateien werden nicht hochgeladen
- **Lösung**: Überprüfe `SFTP_REMOTE_DIR` - das Verzeichnis muss auf dem Server existieren
