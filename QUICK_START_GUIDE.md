# BATOM 1:1 AUTHENTICATION SYSTEM - QUICK START GUIDE

## ⚡ 5-MINUTE SETUP

Follow these steps to get authentication working:

---

## STEP 1: Import Database (2 minutes)

### Option A: Command Line (Recommended)

```bash
# Open Command Prompt or PowerShell
# Navigate to TokoBajuCustom folder
cd C:\xampp\htdocs\TokoBajuCustom

# Import database
mysql -u root < database_setup.sql

# If you see "Query OK" messages, database is created ✅
```

### Option B: phpMyAdmin (GUI)

1. Open http://localhost/phpmyadmin
2. Click "New" to create new database
3. Database name: `batom_studio`
4. Charset: `utf8mb4_unicode_ci`
5. Click "Create"
6. Select `batom_studio` database
7. Click "Import" tab
8. Choose `database_setup.sql` file
9. Click "Go"

---

## STEP 2: Verify Database (1 minute)

After import, verify all tables exist:

```bash
# Login to MySQL
mysql -u root

# Select database
USE batom_studio;

# Show all tables
SHOW TABLES;

# You should see 10 tables:
# 1. users
# 2. admin_users
# 3. orders
# 4. order_attachments
# 5. order_status_logs
# 6. payment_tracking
# 7. invoices
# 8. activity_logs
# 9. admin_reports
# 10. analytics_snapshots

# Exit MySQL
EXIT;
```

---

## STEP 3: Start Development Server (1 minute)

```bash
# Navigate to project
cd C:\xampp\htdocs\TokoBajuCustom

# Start CodeIgniter development server
php spark serve

# You should see:
# Starting CodeIgniter development server with PHP 7.4.33
# Server started on http://localhost:8080
```

---

## STEP 4: Test Registration (1 minute)

1. Open browser: http://localhost:8080/auth
2. Click **"Create Account"** tab
3. Fill form with test data:
   ```
   Full Name: .................... John Doe
   Username: ..................... johndoe2024
   Email: ........................ john@example.com
   Phone Number: ................. 628123456789
   Password: ..................... password123
   Confirm Password: ............. password123
   ✓ Agree with Terms & Conditions
   ```
4. Click **"Create Account"** button
5. You should be logged in and see message:
   ```
   ✅ Akun berhasil dibuat! Selamat datang, John Doe!
   ```

---

## STEP 5: Test Login (Optional)

1. Logout or open new incognito window
2. Go to http://localhost:8080/auth
3. Click **"Sign In"** tab
4. Enter credentials:
   ```
   Email: ........................ john@example.com
   Password: ..................... password123
   ```
5. Click **"Sign In"** button
6. You should see:
   ```
   ✅ Selamat datang, John Doe!
   ```

---

## ✅ SUCCESS!

If you completed all 5 steps without errors, authentication system is working! 🎉

---

## 📚 NEXT STEPS

After basic setup works, you can:

1. **Create User Dashboard**
   - File: `app/Controllers/Dashboard.php`
   - Shows user's orders
   - Track order status

2. **Create Admin Panel**
   - File: `app/Controllers/Admin/Dashboard.php`
   - Manage orders
   - View analytics
   - Generate reports

3. **Setup WhatsApp Notifications**
   - Send order details to admin
   - Send status updates to customer

4. **Implement Order System**
   - Create order form
   - File upload for references
   - Order status tracking

---

## 🆘 TROUBLESHOOTING

### Problem: "Table doesn't exist"

**Solution:**

```bash
# Check if database imported correctly
mysql -u root -e "USE batom_studio; SHOW TABLES;"

# If no tables shown, re-import:
mysql -u root < database_setup.sql
```

### Problem: "Can't connect to database"

**Solution:**

1. Make sure MySQL is running (XAMPP Control Panel)
2. Edit `.env` file
3. Verify these lines:
   ```env
   database.default.hostname = localhost
   database.default.database = batom_studio
   database.default.username = root
   database.default.password =
   ```

### Problem: Server won't start

**Solution:**

```bash
# Make sure you're in correct directory
cd C:\xampp\htdocs\TokoBajuCustom

# If port 8080 is busy, use different port:
php spark serve --port 9000

# Then visit http://localhost:9000/auth
```

### Problem: Form submission doesn't work

**Solution:**

1. Check browser console (F12) for errors
2. Make sure JavaScript is enabled
3. Clear browser cache
4. Try in different browser

---

## 📝 DEFAULT TEST ACCOUNTS

### Customer Account (created during registration)

```
Email: test@example.com
Password: 12345678
```

### Admin Account (in database)

```
Email: admin@batom.studio
Password: admin123 (hashed in database)
Status: active
Role: super_admin
```

To login as admin, go to http://localhost:8080/admin/login (when admin UI is ready)

---

## 🔐 SECURITY NOTES

- ✅ Passwords are hashed with bcrypt (never stored plaintext)
- ✅ All forms have CSRF protection
- ✅ Session timeout: 2 hours
- ✅ Account status: active/inactive/suspended
- ✅ WhatsApp integration with phone verification

---

## 📊 DATABASE INFO

| Info          | Value        |
| ------------- | ------------ |
| Database Name | batom_studio |
| Hostname      | localhost    |
| Username      | root         |
| Password      | (empty)      |
| Charset       | utf8mb4      |
| Tables        | 10           |
| Total Fields  | 89           |

---

## 🎯 FEATURES READY

- ✅ User registration with validation
- ✅ User login with password verification
- ✅ Session management
- ✅ Logout functionality
- ✅ Form validation
- ✅ Error messages
- ✅ Success messages
- ✅ Password eye-toggle
- ✅ Tab-based UI
- ✅ Responsive design

---

## 📞 NEED HELP?

Check documentation files:

1. `DATABASE_SETUP_GUIDE.md` - Detailed setup guide
2. `AUTHENTICATION_IMPLEMENTATION_STATUS.md` - Current status
3. `IMPLEMENTATION_SUMMARY.md` - Complete overview

---

**Quick Start Guide v1.0**  
**Last Updated:** 2024-12-17
