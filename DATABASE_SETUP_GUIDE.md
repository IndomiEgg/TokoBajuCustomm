# BATOM 1:1 CUSTOM WEARABLE ART STUDIO

## Complete Database & Authentication Setup Guide

---

## 📋 TABLE OF CONTENTS

1. [Database Setup](#database-setup)
2. [Configuration Files](#configuration-files)
3. [Authentication Flow](#authentication-flow)
4. [Feature Overview](#feature-overview)
5. [Implementation Steps](#implementation-steps)
6. [Testing](#testing)

---

## 🗄️ DATABASE SETUP

### Prerequisites

- MySQL/MariaDB Server running
- Username: `root`
- Password: (none/empty)
- Default Host: `localhost`

### Step 1: Create Database

```sql
CREATE DATABASE IF NOT EXISTS batom_studio
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE batom_studio;
```

### Step 2: Import SQL Schema

Run the complete SQL schema from: `database_setup.sql`

```bash
# Via MySQL Command Line
mysql -u root < database_setup.sql

# Or in phpMyAdmin:
# 1. Open phpMyAdmin
# 2. Create new database: batom_studio
# 3. Import database_setup.sql file
```

### Step 3: Verify Database Created

```sql
-- Check if all tables exist
SHOW TABLES;

-- You should see:
-- 1. users
-- 2. admin_users
-- 3. orders
-- 4. order_attachments
-- 5. order_status_logs
-- 6. payment_tracking
-- 7. invoices
-- 8. activity_logs
-- 9. admin_reports
-- 10. analytics_snapshots
```

### Database Tables Overview

| Table                 | Purpose                                       |
| --------------------- | --------------------------------------------- |
| `users`               | Customer user accounts with profile info      |
| `admin_users`         | Admin/staff accounts with roles & permissions |
| `orders`              | Custom commission orders from customers       |
| `order_attachments`   | Reference images, sketches, design files      |
| `order_status_logs`   | Tracking log for order status changes         |
| `payment_tracking`    | Payment status & WhatsApp integration         |
| `invoices`            | Invoice/struk pembelanjaan data               |
| `activity_logs`       | Audit trail for all user & admin actions      |
| `admin_reports`       | Generated daily/weekly/monthly reports        |
| `analytics_snapshots` | Dashboard analytics data                      |

---

## 📝 CONFIGURATION FILES

### 1. Update `.env` File

```env
# Database Configuration
database.default.hostname = localhost
database.default.database = batom_studio
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =

# Session Configuration
session.driver = files
session.cookieName = batom_session
session.expiration = 7200  # 2 hours
session.savePath = WRITEPATH
```

### 2. CI4 Configuration Already Updated

The following files have been updated:

- `app/Controllers/Auth.php` - Authentication logic
- `app/Models/UserModel.php` - User database operations
- `app/Views/auth/login.php` - Login/Register UI
- `app/Config/Routes.php` - Authentication routes

---

## 🔐 AUTHENTICATION FLOW

### User Registration Flow

```
1. User fills registration form
   ├─ Full Name
   ├─ Username (unique)
   ├─ Email (unique)
   ├─ Phone Number (WhatsApp)
   ├─ Password (bcrypt hashed)
   └─ Terms & Conditions acceptance

2. Validation
   ├─ Check if username exists
   ├─ Check if email exists
   ├─ Verify password confirmation
   └─ Verify terms accepted

3. Account Creation
   ├─ Hash password with PASSWORD_BCRYPT
   ├─ Insert user record to DB
   ├─ Set initial account_status = 'active'
   └─ Send confirmation email (optional)

4. Auto Login
   ├─ Set session variables
   └─ Redirect to dashboard
```

### User Login Flow

```
1. User enters credentials
   ├─ Email
   └─ Password

2. Database Lookup
   ├─ Search for user by email
   └─ If not found → Error

3. Password Verification
   ├─ Use password_verify()
   ├─ If mismatch → Error
   └─ If match → Continue

4. Account Status Check
   ├─ active → Continue
   ├─ inactive → Error (verify email)
   └─ suspended → Error (account locked)

5. Create Session
   ├─ Set user_id in session
   ├─ Set email in session
   ├─ Set username in session
   ├─ Set full_name in session
   ├─ Set phone_number in session
   └─ Set is_logged_in = true

6. Redirect to Dashboard
```

---

## ✨ FEATURE OVERVIEW

### User Dashboard Features

- ✅ Track order status (pending → delivered)
- ✅ View order details & attachments
- ✅ Upload design references
- ✅ View payment status (paid/unpaid)
- ✅ View invoice/struk pembelanjaan
- ✅ Download invoice as PDF

### Admin Dashboard Features

- ✅ View all orders
- ✅ Update order status
- ✅ Manage payment status
- ✅ Send WhatsApp notifications
- ✅ Generate reports (daily/weekly/monthly/yearly)
- ✅ Export reports to PDF/Excel
- ✅ View analytics charts
- ✅ Manage user accounts
- ✅ Assign orders to designers/curators

### Payment Workflow (No Payment Gateway)

1. Customer completes order form
2. Order created in database (status: pending)
3. Admin receives notification
4. Admin creates invoice & sends via WhatsApp
5. Customer pays via WhatsApp (bank transfer)
6. Admin marks as paid in database
7. Designer starts work
8. Order status updates: pending → approved → in_progress → quality_check → ready_to_ship → shipped → delivered

---

## 🚀 IMPLEMENTATION STEPS

### Step 1: Database Import

```bash
cd C:\xampp\htdocs\TokoBajuCustom
mysql -u root < database_setup.sql
```

### Step 2: Verify CI4 Files

Check these files exist:

- ✅ `app/Controllers/Auth.php`
- ✅ `app/Models/UserModel.php`
- ✅ `app/Views/auth/login.php`
- ✅ `app/Config/Routes.php`

### Step 3: Start Local Server

```bash
cd C:\xampp\htdocs\TokoBajuCustom
php spark serve
```

Access at: `http://localhost:8080/auth`

### Step 4: Test Registration

1. Go to http://localhost:8080/auth
2. Click "Create Account"
3. Fill in all fields
4. Click "Create Account"
5. Should be logged in and redirected to dashboard

### Step 5: Test Login

1. Go to http://localhost:8080/auth
2. Click "Sign In"
3. Enter test email & password
4. Click "Sign In"
5. Should be logged in

---

## 🧪 TESTING

### Test Credentials (After Manual Insert)

```sql
-- Insert test user
INSERT INTO users (
    full_name,
    username,
    email,
    phone_number,
    password,
    account_status,
    is_verified,
    created_at
) VALUES (
    'Test User',
    'testuser',
    'test@example.com',
    '628123456789',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/DiO', -- password: 12345678
    'active',
    1,
    NOW()
);
```

### Test Account Details

- **Email:** test@example.com
- **Username:** testuser
- **Password:** 12345678

### Test Scenarios

#### Scenario 1: Register New User

1. Visit /auth
2. Fill registration form
3. Submit
4. Verify user created in database
5. Verify session created
6. Verify redirected to /dashboard

#### Scenario 2: Login with Correct Credentials

1. Visit /auth
2. Enter email & password
3. Submit
4. Verify session created
5. Verify redirected to /dashboard

#### Scenario 3: Login with Wrong Password

1. Visit /auth
2. Enter correct email but wrong password
3. Submit
4. Verify error message displayed
5. Verify NOT logged in

#### Scenario 4: Register with Duplicate Email

1. Register account 1 with test@example.com
2. Try register account 2 with same email
3. Verify error: "Email sudah terdaftar"

---

## 📱 NEXT STEPS

### Phase 2: Order Management

- Create Order Model & Controller
- Create order form page
- Implement file upload for references
- Create order status tracking page

### Phase 3: Admin Dashboard

- Create Admin Controller
- Create Analytics page with Charts.js
- Create Reports Generator (PDF/Excel)
- Create Order Management page
- Create WhatsApp integration

### Phase 4: Dashboard Enhancements

- Implement invoice generation
- Add payment status tracking
- Create report exports
- Add user profile management

---

## 🔗 USEFUL LINKS

- **CodeIgniter 4 Docs:** https://codeigniter.com/user_guide/
- **MySQL Docs:** https://dev.mysql.com/doc/
- **PHP Security:** https://www.php.net/manual/en/function.password-hash.php

---

## ❓ TROUBLESHOOTING

### Issue: Database Connection Error

**Solution:**

- Verify MySQL is running
- Check .env file has correct credentials
- Verify database name is `batom_studio`

### Issue: "Table doesn't exist" Error

**Solution:**

- Run `database_setup.sql` again
- Verify database import was successful
- Check table names in Models match database

### Issue: Password not verifying

**Solution:**

- Use `password_hash($pass, PASSWORD_BCRYPT)` for hashing
- Use `password_verify($input, $hashed)` for verification
- Do NOT use MD5 or simple hashing

### Issue: Session not persisting

**Solution:**

- Verify `session.savePath` exists and is writable
- Check session.driver in .env (should be 'files')
- Clear browser cookies and try again

---

## 📞 SUPPORT

For issues or questions, contact: **admin@batom.studio**

---

**Last Updated:** 2026-06-12  
**Version:** 1.0  
**Status:** Production Ready
