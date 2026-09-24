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

## Step 4 — AI search configuration

The admin page `?page=ai-search` saves planner defaults, search types, budget mode, limits and prompt instructions. Only administrators may save or restore prompts. Changes are recorded in `ai_prompt_versions` and the existing audit log. AI requests and supplier lookups are not yet implemented; enabling this setting alone does not create a working public AI search.

For an existing database, import `migrations/004_ai_settings.sql` once before using the AI settings page. A fresh `database.sql` import already contains this table. Keep AI API credentials in server-side configuration, outside the database and Git.
