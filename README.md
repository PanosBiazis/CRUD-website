<img src="CRUD/assets/icon.svg" width="80" height="80" alt="Bike Race icon" />

# Bike Race CRUD

A small PHP/MySQL learning project with a responsive, script-free interface.
Existing page URLs remain available. This is a local preview, not a production
account or personal-data service.

## Configuration

PHP 8.2+ with mysqli/mysqlnd and sessions is required. Provision an empty test
database separately; this repository does not run migrations or restore data.
Set `CRUD_DB_HOST` (defaults to loopback), `CRUD_DB_NAME`, `CRUD_DB_USER` and
`CRUD_DB_PASSWORD` in the server environment. Missing credentials fail closed.
Use a dedicated least-privilege database account, not a root account. No database
password is stored in source.

Set `CRUD_WRITER_EMAILS` to the comma-separated emails allowed to edit the shared
contestants table. Empty means no writes. Provision editor accounts offline in
the database, then sign in. Public registration rejects reserved editor emails;
registration alone grants no editor authority. Do not promote existing unverified
accounts by adding their email to this list. Editing sessions expire after one
hour and require signing in again. Public result browsing intentionally remains
available and must contain only data authorized for public display.

Expected tables: `contestants(id, name, country, time)` and
`accounts(email, password, username)`, with a unique email constraint and password
hash storage of at least 255 characters. Use utf8mb4. Existing stored password
hashes are verified; new passwords use PHP's current default hash and require
12–72 bytes. An existing duplicate email fails authentication rather than choosing
an arbitrary account.

## Security changes

- All user data uses bound SQL parameters; numeric IDs and pagination are validated.
- Every mutation requires an allowed authenticated editor and a session CSRF token.
- Delete-all is a confirmation form on GET and requires POST plus `DELETE ALL`.
- HTML output is escaped. Updated record data no longer appears in redirect URLs.
- Database errors and credentials are not shown to visitors. Sessions are HttpOnly,
  SameSite=Strict, and Secure on direct HTTPS; proxy TLS needs explicit deployment
  configuration and must not trust arbitrary forwarded headers.
- Local CSS/SVG; no third-party fonts, scripts or tracking. Results use bounded
  pagination; they no longer load the entire table to count rows.

## Verification and remaining limits

Run `php -n tests/security_test.php` and lint PHP files. The tests use synthetic
values and a recording database boundary; they do not contact a real database.
Local HTTP route tests cover methods, authorization, CSRF, redirects and escaping.
A real MySQL integration run, account rate limiting, recovery/MFA, deployment TLS,
DB backups and operational monitoring remain necessary before public deployment.
No live database, schema, user account or server was changed by this patch.
