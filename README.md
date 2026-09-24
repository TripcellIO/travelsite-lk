# TravelSite.lk

PHP + MySQL application for TravelSite.lk, starting with the administration portal.

## Step 1 — Admin foundation

Included:
- Environment-based configuration
- PDO/MySQL connection
- Secure admin authentication
- CSRF protection
- Session regeneration after login
- Password hashing with PHP PASSWORD_DEFAULT
- Audit logging foundation
- Admin dashboard shell
- Destination/provider/trip/CMS schema foundation
- SiteGround-friendly Apache routing

## SiteGround setup

1. Create a MySQL database and database user in Site Tools.
2. Import `database.sql` into that database.
3. Copy `.env.example` to `.env` on the server and enter the database credentials.
4. Generate a long random value for `APP_KEY`.
5. Visit `setup_admin.php` once and create the first super administrator.
6. Delete `setup_admin.php` from production after the administrator is created.
7. Open `index.php?page=login`.

Never commit `.env`, database passwords, API secrets or private keys.
