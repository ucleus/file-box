-- Logo Delivery Portal Database Schema
-- SQLite compatible (can be adapted for MySQL)

-- Users table (single admin user)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- OTP codes for authentication
CREATE TABLE IF NOT EXISTS otp_codes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Deliveries table
CREATE TABLE IF NOT EXISTS deliveries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    token VARCHAR(64) NOT NULL UNIQUE,
    client_name VARCHAR(255) NOT NULL,
    client_email VARCHAR(255),
    project_name VARCHAR(255) NOT NULL,
    project_version VARCHAR(100),
    notes TEXT,
    brand_notes TEXT,
    status VARCHAR(20) DEFAULT 'active', -- active, paused, expired
    passphrase VARCHAR(255), -- optional
    expires_at DATETIME, -- optional expiry date
    max_downloads INTEGER, -- optional download limit
    download_count INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Assets table (files for each delivery)
CREATE TABLE IF NOT EXISTS assets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    delivery_id INTEGER NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    file_type VARCHAR(50) NOT NULL, -- PNG, JPG, SVG, PDF, AI, EPS, etc.
    asset_tag VARCHAR(50), -- primary, alt, favicon, social, etc.
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE
);

-- Activity log for analytics and audit
CREATE TABLE IF NOT EXISTS activity_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    delivery_id INTEGER,
    event_type VARCHAR(50) NOT NULL, -- page_view, file_download, zip_download, tweak_request
    file_name VARCHAR(255),
    ip_hash VARCHAR(64), -- hashed IP for privacy
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE SET NULL
);

-- Rate limiting table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    identifier VARCHAR(64) NOT NULL, -- IP hash or token
    action VARCHAR(50) NOT NULL, -- download, otp_request, etc.
    attempts INTEGER DEFAULT 1,
    window_start DATETIME DEFAULT CURRENT_TIMESTAMP,
    blocked_until DATETIME
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_deliveries_token ON deliveries(token);
CREATE INDEX IF NOT EXISTS idx_deliveries_status ON deliveries(status);
CREATE INDEX IF NOT EXISTS idx_assets_delivery ON assets(delivery_id);
CREATE INDEX IF NOT EXISTS idx_activity_delivery ON activity_log(delivery_id);
CREATE INDEX IF NOT EXISTS idx_activity_created ON activity_log(created_at);
CREATE INDEX IF NOT EXISTS idx_otp_user ON otp_codes(user_id);
CREATE INDEX IF NOT EXISTS idx_otp_code ON otp_codes(code);
CREATE INDEX IF NOT EXISTS idx_rate_limits_identifier ON rate_limits(identifier, action);
