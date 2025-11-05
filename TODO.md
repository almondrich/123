# Security Hardening Implementation Plan

## Missing Security Features to Implement

### 1. Limit on number of entries to prevent huge inserts
- **Current Status**: Only injuries are limited to 100 in TONYANG_save.php
- **Action**: Add general entry limits for batch operations (e.g., max 50 forms per day per user)

### 2. Use HTTPS, Secure cookie flags, SameSite=Strict or Lax for session cookies
- **Current Status**: HttpOnly and SameSite=Strict are set, but Secure flag missing
- **Action**: Add secure flag to session config (requires HTTPS detection)

### 3. Add server-side rate limits and CAPTCHA if public
- **Current Status**: Per-session rate limiting exists, no CAPTCHA
- **Action**: Add CAPTCHA to login form for additional protection

### 4. Unit test form parsing with weird/malformed input arrays to ensure robustness
- **Current Status**: No unit tests exist
- **Action**: Create basic unit test file for form validation functions

### 5. Consider throttling or queueing if bulk inserts become large
- **Current Status**: No throttling/queueing for bulk operations
- **Action**: Add batch size limits and processing queues for large inserts

## Implementation Steps

1. Update session configuration in `includes/config.php`
2. Add CAPTCHA to `public/login.php`
3. Enhance rate limiting in `includes/functions.php`
4. Create unit tests in `tests/` directory
5. Add throttling logic to `api/TONYANG_save.php`
6. Test all changes thoroughly
