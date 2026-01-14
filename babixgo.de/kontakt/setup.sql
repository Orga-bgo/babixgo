-- ==========================================
-- Datenbank Setup für babixGO Kontaktformular
-- ==========================================

-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS babixgo_contacts 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE babixgo_contacts;

-- Contacts Tabelle
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(50) DEFAULT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Users Tabelle
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standard Admin User erstellen
-- Username: admin
-- Passwort: admin123 (BITTE ÄNDERN!)
INSERT INTO admin_users (username, password_hash, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@babixgo.de');

-- Beispiel-Kontakt (zum Testen)
INSERT INTO contacts (name, email, whatsapp, message, ip_address, user_agent, created_at, status)
VALUES 
('Max Mustermann', 'max@example.com', '+49 123 456789', 'Test-Nachricht für das System.', '127.0.0.1', 'Mozilla/5.0', NOW(), 'new');