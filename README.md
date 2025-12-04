<p align="center">
    <img width="1953" height="961" alt="Familio Dashboard" src="https://github.com/user-attachments/assets/2954bb7f-f6df-43b1-acfb-a81424ae4c49" style="display:block; margin: 0 auto; width: 1000px; height: auto;"/>
    <img width="1939" height="953" alt="Subscription Management" src="https://github.com/user-attachments/assets/59ef8fd4-182a-40b8-b784-c20d05961983" style="display:block; margin: 0 auto; width: 1000px; height: auto;"/>
    <img width="1954" height="962" alt="Payment Tracking" src="https://github.com/user-attachments/assets/08987ddd-599a-47b6-89f5-3b2242c90975" style="display:block; margin: 0 auto; width: 1000px; height: auto;"/>
</p>

# Familio

**Manage shared family subscriptions and track who owes what.**

Just a convenient thing I made to keep up with family subscriptions ^^


Note for trying to host this in cloud: if using turso its not easy to host because php ffi extension needs to be enabled for `turso/libsql-laravel` :(
If using other db provider, you might need to remove `turso/libsql-laravel` but maybe not, only one way to find out. I use locally only, with cloud db so let me know how it goes.

## What does it do?

- 📊 **Track Subscriptions**: Add all your family's shared services (streaming, music, storage, etc.)
- 👥 **Manage Members**: Keep track of who's on which subscription
- 💰 **Monitor Payments**: Record when members pay their share
- 📅 **Billing Cycles**: Track subscription charges and billing periods
- 🎯 **Stay Organized**: See at a glance who owes what and when

Perfect for families and friends splitting costs on Netflix, Spotify, Disney+, YouTube Premium, iCloud Storage, or any other shared subscription service.

## Tech Stack

- **Laravel 12** - PHP framework
- **Filament 4** - Admin panel
- **SQLite / Libsql (Turso)** - Database options
- **Vite** - Frontend build tool

## Prerequisites

- **PHP 8.2 or higher**
- **Composer**
- **Node.js and npm** (required for frontend assets)
- **SQLite** (or Turso/Libsql account)



## Quick Setup

### 1. Clone and Install

```bash
git clone https://github.com/TomasLiutvinas/familio.git
cd familio
composer install
npm install
```

### 2. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate your application key:

```bash
php artisan key:generate
```

### 3. Choose Your Database

I have turso so I didn't try other providers, but should work.

**Option A: SQLite (Easiest, local)**

Create the database file:

```bash
touch database/database.sqlite
```

Update your `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/familio/database/database.sqlite
DB_FOREIGN_KEYS=true
```

**Option B: Libsql/Turso (Cloud, but you need turso then)**

Enable FFI
/etc/php/php.ini
```
extension=ffi
```
and
```
ffi.enable=true
```

Update your `.env`:

```env
DB_CONNECTION=libsql
TURSO_DATABASE_URL=libsql://your-database.turso.io
TURSO_AUTH_TOKEN=your-auth-token
DB_FOREIGN_KEYS=true
```

### 4. Set Up Database

Run the migrations:

```bash
php artisan migrate
```

### 5. Create Admin User

```bash
php artisan make:filament-user
```

Follow the prompts to create your admin account.

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Start the Application

```bash
php artisan serve
```

Visit **http://localhost:8000** to see a nice page that nobody really cares about, but it looks nice.

Visit **http://localhost:8000/admin** and log in with your admin credentials.

## Development

```bash
php artisan serve
```

And change files. Break stuff.

## Using Familio

1. **Add People**: Create entries for family members
2. **Create Subscriptions**: Add your streaming services, music plans, etc.
3. **Assign Members**: Add people to subscriptions
4. **Track Charges**: Enter what it will cost or did cost for the year
5. **Log Payments**: Mark when members pay their share

The dashboard will show you a clear overview of all subscriptions, upcoming charges, and payment status.

## Contributing

Feel free to fork and adapt for your needs.

### Known Issues

- **Changing members mid-subscription**: If you modify subscription members after charges/payments exist, the historical data might show weird stuff. This hasn't been thoroughly (or at all) tested - recommend setting up subscriptions correctly from the start and avoiding member changes once you have payment history.

## Support

Provided as-is with no warranty. Found a bug? Open an issue. And then solve it:D Or maybe I will, but wouldn't hold my breath on that.

---

Built with chill music, some coding, some vibe sonnet 4.5 and Zed.
