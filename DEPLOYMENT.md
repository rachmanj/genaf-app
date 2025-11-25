# Production Deployment Guide for /gamma Subdirectory

## File Structure on Production Server

```
/htdocs/gamma/              (or your web root)
├── .htaccess              (root .htaccess - see below)
├── server.php             (handles routing - already updated)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── .htaccess         (already configured)
│   ├── index.php
│   └── ...
├── routes/
├── storage/
├── vendor/
└── .env                   (production .env)
```

## Step 1: Root .htaccess Configuration (with server.php)

Create/update `.htaccess` in the root of `/gamma` folder:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    RewriteEngine On

    # Handle existing files and directories
    RewriteCond %{REQUEST_FILENAME} -d [OR]
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ ^$1 [N]

    # Handle files with extensions - redirect to public folder
    RewriteCond %{REQUEST_URI} (\.\w+$) [NC]
    RewriteRule ^(.*)$ public/$1 [L]

    # All other requests go to server.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ server.php [L]
</IfModule>
```

**Note:** This configuration uses `server.php` which allows you to access the app at `http://domain/gamma` instead of `http://domain/gamma/public`.

## Step 2: server.php Configuration

The `server.php` file has been updated to automatically detect and handle the subdirectory. It uses `$_SERVER['SCRIPT_NAME']` to determine the subdirectory path, so it works automatically without hardcoding `/gamma`.

**Important:** Make sure `server.php` is in the root of `/gamma` folder on your production server.

## Step 3: Production .env Configuration

Ensure your production `.env` has:

```env
APP_URL=http://192.168.32.13/gamma
APP_ENV=production
APP_DEBUG=false

SESSION_DRIVER=database
SESSION_PATH=/gamma
SESSION_TABLE=gamma_sessions
SESSION_COOKIE=gamma_session
```

## Step 4: Verify public/.htaccess

The `public/.htaccess` should NOT have `RewriteBase` directive. It should be:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Step 5: Test the Configuration

1. Access: `http://192.168.32.13/gamma/`
2. Should redirect to login or dashboard
3. Check browser console for any 404 errors on assets

## Troubleshooting

### If you still get 404:

1. **Check if mod_rewrite is enabled:**

    ```apache
    # In httpd.conf or .htaccess, ensure:
    LoadModule rewrite_module modules/mod_rewrite.so
    ```

2. **Check file permissions:**

    - `.htaccess` files should be readable (644)
    - `storage/` and `bootstrap/cache/` should be writable (775)

3. **Check Apache AllowOverride:**

    ```apache
    # In httpd.conf or virtual host:
    <Directory "/path/to/gamma">
        AllowOverride All
    </Directory>
    ```

4. **Test if server.php is accessible:**

    - Try: `http://192.168.32.13/gamma/server.php`
    - If this works, the root .htaccess routing is the issue
    - Try: `http://192.168.32.13/gamma/public/`
    - If this works but `/gamma/` doesn't, check the root .htaccess

5. **Check Laravel logs:**
    - `storage/logs/laravel.log`

## Alternative: Simpler Root .htaccess

If the above doesn't work, try this even simpler version:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```
