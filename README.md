# AutoDeal — Car Sales / Dealership Website

A complete PHP + MySQL car sales website: a public-facing site where buyers
browse listings, view full car descriptions/specs/photos, and contact you —
plus an admin dashboard where you add cars, upload photos, set prices, and
manage buyer inquiries.

## Features

**Public website**
- Homepage with hero search bar and featured listings
- Browse/search page with filters (make, body type, price range, keyword, sort)
- Car details page: photo gallery, full specs, description, price
- "Chat on WhatsApp" button pre-filled with the car name, click-to-call, and an inquiry form
- Contact page with a general inquiry form
- Fully responsive, custom-styled dark/orange automotive theme (no external CDN needed)

**Admin dashboard** (`/admin`)
- Secure login (bcrypt-hashed passwords)
- Dashboard — total listings, available/sold counts, stock value, new leads
- Full car CRUD: title, make, model, year, price, mileage, transmission, fuel
  type, body type, color, location, status (available/reserved/sold), featured flag, description
- Multi-photo upload per car (first photo = main image), with the ability to
  delete individual photos later
- Inquiries/leads inbox — view buyer messages, mark as contacted/closed, delete

## Requirements
XAMPP (Apache + MySQL + PHP 8+)

## Setup (XAMPP)
1. Copy the `car_dealer` folder into `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
4. Click **Import**, choose `database/car_dealer.sql`, and click **Go**.
   (Creates the `car_dealer` database, all tables, and 6 demo listings with placeholder photos.)
5. Public site: `http://localhost/car_dealer/`
   Admin panel: `http://localhost/car_dealer/admin/`
6. Log in to the admin panel with:
   - **Username:** `admin`
   - **Password:** `admin123`
7. Change the admin password after your first login (update the `users` table
   in phpMyAdmin with a new `password_hash()` value).

## Before you go live — edit these
`config/site_config.php` — set your real phone number, WhatsApp number, email and address:
```php
define('SITE_PHONE', '+254 797 381 808');
define('SITE_WHATSAPP', '+254797381 808'); // digits only, no + or leading 0
define('SITE_EMAIL', 'sales@autodeal.co.ke');
define('SITE_ADDRESS', 'Ngong Road, Nairobi, Kenya');
```

`config/db.php` — if your MySQL username/password differs from the XAMPP default:
```php
$DB_USER = 'root';
$DB_PASS = '';
```

Delete the 6 demo cars from **Admin → Manage Cars** once you've added your real stock
(their placeholder photos live in `assets/uploads/cars/demo-*.jpg` and can be removed too).

## Folder structure
```
car_dealer/
├── config/                 # db.php, site_config.php
├── database/car_dealer.sql # Full schema + demo data
├── includes/                # public site header/footer/car-card partials
├── assets/css/style.css     # Public site styling
├── assets/css/admin.css     # Admin dashboard styling
├── assets/js/script.js      # Public site JS (gallery, mobile nav)
├── assets/js/admin.js       # Admin JS (delete confirm, image preview)
├── assets/uploads/cars/     # Uploaded car photos (.htaccess blocks script execution)
├── index.php / cars.php / car_details.php / contact.php / inquiry_submit.php
└── admin/
    ├── includes/             # admin header/sidebar/footer/auth guard
    ├── index.php (login) / logout.php / dashboard.php
    ├── cars_list.php / cars_add.php / cars_edit.php / cars_delete.php
    └── inquiries.php / inquiries_delete.php
```

## Notes
- All database queries use PDO prepared statements to prevent SQL injection.
- Uploaded images are validated by MIME type and size (max 5MB, JPG/PNG/WEBP only).
- The `assets/uploads/cars/.htaccess` file blocks any uploaded file from being
  executed as a script — keep it in place.
- Deleting a car also deletes its photo files from disk and cascades to its images/inquiries.
