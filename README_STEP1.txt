FB Automation - STEP 1 (Overlay Pack)

This zip is meant to be extracted on top of a fresh Laravel app.
What you still need to do:
1) Install packages: laravel/socialite
   composer require laravel/socialite

2) .env (SQLite dev)
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   DB_FOREIGN_KEYS=true
   APP_URL=http://localhost:8000

   FACEBOOK_CLIENT_ID=...
   FACEBOOK_CLIENT_SECRET=...
   FACEBOOK_REDIRECT_URI=https://<your-ngrok-domain>/auth/facebook/callback

3) Make the sqlite file:
   mkdir database
   type nul > database\database.sqlite   (Windows)

4) Migrate:
   php artisan migrate

5) Run:
   php artisan serve
   Open http://localhost:8000/auth/facebook/redirect

6) For Facebook OAuth + Webhook you need an HTTPS public URL (e.g., ngrok).
