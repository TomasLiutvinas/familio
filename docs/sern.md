# Permanent Sern deployment

Familio runs as a PHP-FPM user service behind Sern's existing Caddy service.
Access: `http://192.168.1.188:8085/admin` from the home LAN. Caddy listens on
loopback and Sern's wired LAN address for this port. There is no public hostname,
TLS certificate request, or router port-forwarding change for this deployment.

Shared configuration and delivery tools live in the sibling Sern repository;
read `../sern/docs/familio-sern.md` for setup, runtime installation, readiness,
logs, automatic updates, pause and recovery. PHP runtime packages are managed by
pacman. Application releases use the dedicated `sern-php-<commit>` artifact adapter.

Build/test app changes here. The existing Turso database and owner-provisioned
accounts are reused; deployment never runs migrations, seeds or user creation.
The production User model explicitly allows existing accounts into the admin
panel, which has no public registration. Use file sessions/cache on Sern and
synchronous jobs; this app currently has no queued jobs or scheduled tasks.

The `.env`/APP_KEY and Turso credentials stay private on Sern. APP_DEBUG is false.
Shared local storage survives release switches. A read-only `/api/ready` endpoint
checks database connectivity and returns the immutable source commit.

`sern-release.yml` tests against SQLite in memory, builds the application from
locked dependencies, then publishes a checksummed release when main changes.
Production credentials are not available to CI and are not included in archives.
