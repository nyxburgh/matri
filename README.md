# Matrimony Website — Setup Guide
## LAMPP / XAMPP Installation

### Folder Structure (after extraction)
```
/opt/lampp/htdocs/matrimony/
├── app/
│   ├── core/               ← Router, DB, Session, Controller, Logger, Autoloader
│   └── modules/
│       └── Admin/
│           ├── Controllers/
│           ├── Routes/
│           └── Views/
├── config/
│   └── config.php          ← App constants (reads .env)
├── public/
│   ├── index.php           ← Front Controller (entry point)
│   └── .htaccess
├── storage/
│   ├── photos/             ← User uploaded photos
│   ├── logs/               ← Application logs
│   └── cache/              ← Cache files
├── .env                    ← Environment config (edit this)
├── .htaccess               ← Root Apache rules
└── README.md
```

### Step 1 — Import Database
```bash
# Open phpMyAdmin → Create DB: matrimony_db
# Import: matrimony_db.sql
```

### Step 2 — Edit .env
```
APP_URL=http://localhost/matrimony
DB_HOST=localhost
DB_NAME=matrimony_db
DB_USER=root
DB_PASS=          ← your LAMPP MySQL password (blank by default)
```

### Step 3 — Enable Apache mod_rewrite (LAMPP)
```bash
sudo /opt/lampp/bin/apachectl -M | grep rewrite
# If not listed, edit /opt/lampp/etc/httpd.conf
# Uncomment: LoadModule rewrite_module modules/mod_rewrite.so
```

### Step 4 — Allow .htaccess overrides
Edit `/opt/lampp/etc/httpd.conf`, find the section for htdocs and set:
```apache
<Directory "/opt/lampp/htdocs">
    AllowOverride All
    ...
</Directory>
```

### Step 5 — Set storage permissions
```bash
chmod -R 775 /opt/lampp/htdocs/matrimony/storage/
```

### Step 6 — Access Admin Panel
```
URL:      http://localhost/matrimony/admin/login
Email:    admin@mymatrimony.com
Password: Admin@123
```

### URL Structure
- Admin Login:    http://localhost/matrimony/admin/login
- Admin Dashboard:http://localhost/matrimony/admin/dashboard
- Admin Users:    http://localhost/matrimony/admin/users
- Admin Profiles: http://localhost/matrimony/admin/profiles

### Security Notes
- Change admin password after first login
- Edit .env → set APP_ENV=production in live server
- Photos are served through PHP proxy (never direct URL)
- All forms have CSRF token protection
- Session auto-expires after 30 minutes of inactivity
