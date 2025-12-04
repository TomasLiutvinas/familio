

# Development Process

## Initial Laravel Installation

```bash
composer create-project laravel/laravel:"^12.0" family-subs
```

## Environment Configuration

Update the `.env` file:

```
APP_NAME=Familio
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/your/repos/family-subs/database/database.sqlite
DB_FOREIGN_KEYS=true
```

## Database Migration and Filament Installation

Run the following commands:

```bash
php artisan migrate
composer require filament/filamen:"^4.0" -W
php artisan filament:install
php artisan migrate
php artisan make:filament-panel admin
php artisan make:filament-user
```

Access the admin panel at:
`localhost:8000/admin`

## Model Creation

Generate models:

```bash
php artisan make:model Person -m
php artisan make:model Subscription -m
php artisan make:model SubscriptionMember -m
php artisan make:model SubscriptionCharge -m
php artisan make:model MemberPayment -m
```

Modify migration files in `database/Migrations/` and run:

```bash
php artisan migrate
```

Update model files in `app/Models/`.

## Filament Resource Generation

Generate Filament resources:

```bash
php artisan make:filament-resource Person --generate
php artisan make:filament-resource Subscription --generate
php artisan make:filament-resource SubscriptionMember --generate
php artisan make:filament-resource SubscriptionCharge --generate
php artisan make:filament-resource MemberPayment --generate
```

## Start the Server

Run the server:

```bash
php artisan serve
```

## Adding Libsql During Development

Install Libsql:

```bash
composer config minimum-stability dev
composer require turso/libsql-laravel
```

**Note**: Ensure that the FFI (Foreign Function Interface) extension is enabled in your PHP configuration. This is required for Libsql to function properly.

To enable the FFI extension:
1. Open your `php.ini` file. The location of this file depends on your system setup. Common locations include:
   - `/etc/php/8.x/cli/php.ini` (Linux)
   - `C:\xampp\php\php.ini` (Windows)
2. Search for the line containing `extension=ffi`.
3. If the line is commented out (starts with a `;`), remove the `;` to uncomment it.
4. Search for the `ffi.enable` directive in the same file. Ensure it is set to `true`:
   ```
   ffi.enable=true
   ```
5. Save the file and restart your web server or PHP service to apply the changes.

If the `ffi` extension is not present, ensure your PHP installation includes it, or recompile PHP with FFI support.

To enable the FFI extension:
1. Open your `php.ini` file. The location of this file depends on your system setup. Common locations include:
   - `/etc/php/8.x/cli/php.ini` (Linux)
   - `C:\xampp\php\php.ini` (Windows)
2. Search for the line containing `extension=ffi`.
3. If the line is commented out (starts with a `;`), remove the `;` to uncomment it.
4. Save the file and restart your web server or PHP service to apply the changes.

If the `ffi` extension is not present, ensure your PHP installation includes it, or recompile PHP with FFI support.

Update `config/database.php`:

```json
'libsql' => [
    'driver' => 'libsql',
    'url' => env('TURSO_DATABASE_URL'),
    'password' => env('TURSO_AUTH_TOKEN'),
    'prefix' => '',
],
```

Update `.env`:

```
DB_CONNECTION=libsql
TURSO_DATABASE_URL=libsql://<TURSO URL>
TURSO_AUTH_TOKEN=<TURSO TOKEN>
```
