-- Security Logs Table Migration
-- Adds comprehensive security logging capabilities to track security-related events

-- Drop table if exists to ensure clean recreation
DROP TABLE IF EXISTS security_logs;

-- Create security_logs table for enhanced security event tracking
CREATE TABLE security_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(255),
    request_method VARCHAR(10),
    request_uri TEXT,
    severity ENUM('info', 'warning', 'critical') DEFAULT 'info',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_ip_address (ip_address),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint to users table (if it exists)
-- This will be skipped if users table doesn't exist yet
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'users'
        ),
        'ALTER TABLE security_logs ADD CONSTRAINT fk_security_logs_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE;',
        'SELECT "Users table does not exist, skipping foreign key constraint" as message;'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Insert migration record (skip if table doesn't exist)
-- SET @migration_sql = (
--     SELECT IF(
--         EXISTS(
--             SELECT 1
--             FROM information_schema.TABLES
--             WHERE TABLE_SCHEMA = DATABASE()
--             AND TABLE_NAME = 'database_migrations'
--         ),
--         'INSERT INTO database_migrations (migration_name, executed_at, description) VALUES (''add_security_logs_table'', NOW(), ''Added security_logs table for comprehensive security event logging with enhanced metadata'') ON DUPLICATE KEY UPDATE executed_at = NOW();',
--         'SELECT "database_migrations table does not exist, skipping migration record" as message;'
--     )
-- );
-- PREPARE stmt_migration FROM @migration_sql;
-- EXECUTE stmt_migration;
-- DEALLOCATE PREPARE stmt_migration;

-- Optional: Create a view for easier security log analysis
CREATE OR REPLACE VIEW security_logs_summary AS
SELECT
    sl.id,
    sl.action,
    sl.severity,
    sl.ip_address,
    sl.created_at,
    u.username,
    CASE
        WHEN sl.severity = 'critical' THEN '🔴 Critical'
        WHEN sl.severity = 'warning' THEN '🟡 Warning'
        ELSE '🔵 Info'
    END as severity_icon,
    LEFT(sl.details, 100) as details_preview
FROM security_logs sl
LEFT JOIN users u ON sl.user_id = u.id
ORDER BY sl.created_at DESC;

-- Optional: Create indexes for common security queries
-- These help with performance when querying security logs frequently
CREATE INDEX idx_security_logs_recent ON security_logs (created_at DESC, severity);
CREATE INDEX idx_security_logs_ip_action ON security_logs (ip_address, action, created_at DESC);
CREATE INDEX idx_security_logs_user_action ON security_logs (user_id, action, created_at DESC);

-- Comments for documentation
/*
Security Logs Table Structure:
- id: Auto-incrementing primary key
- user_id: Reference to users table (nullable for anonymous events)
- action: Security event type (e.g., 'login_failed', 'rate_limit_exceeded', 'admin_user_created')
- details: Detailed information about the security event
- ip_address: Client IP address (supports IPv4 and IPv6)
- user_agent: Browser/client user agent string
- session_id: PHP session identifier
- request_method: HTTP method (GET, POST, etc.)
- request_uri: Full request URI
- severity: Event severity level (info, warning, critical)
- created_at: Timestamp of the security event

Common Security Events Logged:
- Authentication: login_failed, account_locked, user_login, user_logout
- Rate Limiting: rate_limit_exceeded
- Admin Actions: admin_user_created, admin_password_changed, admin_user_status_changed
- File Operations: file_access, file_upload_failed
- Form Operations: Various form-related security events

Usage Examples:
- Monitor failed login attempts: SELECT * FROM security_logs WHERE action = 'login_failed'
- Track admin actions: SELECT * FROM security_logs WHERE action LIKE 'admin_%'
- Analyze suspicious IPs: SELECT ip_address, COUNT(*) FROM security_logs WHERE severity IN ('warning', 'critical') GROUP BY ip_address ORDER BY COUNT(*) DESC
- Recent security events: SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 50
*/
