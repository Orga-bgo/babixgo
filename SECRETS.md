# Required GitHub Secrets for Deployment

This document lists the environment variables/secrets required for deploying the babixgo application.

## Database Configuration Secrets

The application requires the following environment variables to be set for database connectivity:

### Required Secrets

| Secret Name | Description | Example Value |
|-------------|-------------|---------------|
| `DB_HOST` | Database server hostname | `localhost` or `db.example.com` |
| `DB_NAME` | Database name | `babixgo_db` |
| `DB_USER` | Database username | `babixgo_user` |
| `DB_PASS` | Database password | `your_secure_password` |
| `DB_CHARSET` | Database character set (optional) | `utf8mb4` (default) |

## Setting Secrets in GitHub

### For GitHub Actions

1. Go to your repository settings
2. Navigate to **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add each secret with its corresponding value

### For Production Deployment

Set these as environment variables on your hosting server. The exact method depends on your hosting provider:

**For Apache with mod_env:**
```apache
SetEnv DB_HOST "your_database_host"
SetEnv DB_NAME "babixgo_db"
SetEnv DB_USER "babixgo_user"
SetEnv DB_PASS "your_secure_password"
```

**For PHP-FPM:**
Add to your pool configuration:
```ini
env[DB_HOST] = your_database_host
env[DB_NAME] = babixgo_db
env[DB_USER] = babixgo_user
env[DB_PASS] = your_secure_password
```

**For command line/shell:**
```bash
export DB_HOST="your_database_host"
export DB_NAME="babixgo_db"
export DB_USER="babixgo_user"
export DB_PASS="your_secure_password"
```

## Local Development

For local development, if no environment variables are set, the application will use default values:

- **DB_HOST**: `localhost`
- **DB_NAME**: `babixgo`
- **DB_USER**: `root`
- **DB_PASS**: (empty string)
- **DB_CHARSET**: `utf8mb4`

You can override any of these by setting the corresponding environment variable.

## Security Best Practices

1. **Never commit secrets to the repository**
2. **Use strong, unique passwords** for production databases
3. **Rotate secrets regularly**
4. **Use different credentials** for development, staging, and production environments
5. **Limit database user permissions** to only what's needed
6. **Enable SSL/TLS** for database connections in production

## Verifying Configuration

To verify that your secrets are properly configured, you can check the application logs or use a test script (ensure this is done securely and never exposes actual password values).

The `shared/config/database.php` file will automatically:
- Read from environment variables if they exist
- Fall back to defaults if environment variables are not set
