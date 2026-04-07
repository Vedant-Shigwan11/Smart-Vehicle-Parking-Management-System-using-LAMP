# 🚗 Smart Parking Booking System (LAMP + AWS)

A full-stack Smart Parking Booking System built using **PHP, MySQL, Bootstrap, and JavaScript**, deployed on **AWS EC2 using LAMP stack (Linux, Apache, MySQL, PHP)**.

This system allows users to book parking slots in real-time and enables administrators to manage slots, bookings, and users efficiently.

---

# 🌐 Live Demo

* **User Login:**
  http://51.20.87.204/Smart-Vehicle-Parking-System-using-LAMP/user/login.php

* **Admin Login:**
  http://51.20.87.204/Smart-Vehicle-Parking-System-using-LAMP/admin/login.php

---

# 🔐 Credentials

## 👤 Admin Login

* Email: `admin@smartparking.local`
* Password: `admin123`

## 👤 Test User Login

* Email: `testuser@gmail.com`
* Password: `user123`

---

# 🚀 Features

## 🟢 User Features

* User registration with secure password hashing
* Login & logout system
* View parking slot availability
* Book slots with date & time selection
* Automatic price calculation (₹20/hour)
* Prevent double booking using overlap logic
* View booking history
* Cancel active bookings

---

## 🔴 Admin Features

* Admin login panel
* Dashboard with statistics
* Manage parking slots (Add/Edit/Delete)
* View all bookings
* Manage users

---

## ⚙️ Technical Features

* PHP + MySQL (MySQLi)
* Prepared statements (SQL Injection protection)
* Session-based authentication
* CSRF protection (if implemented)
* Bootstrap responsive UI
* JavaScript live slot updates

---

# 📁 Project Structure

```text
Smart-Vehicle-Parking-System-using-LAMP/
│
├── index.php              → Entry point (redirects based on role)
│
├── config/
│   ├── db.php             → Database connection
│   └── database.sql       → Database schema
│
├── includes/
│   ├── auth.php           → Authentication & authorization
│   ├── functions.php      → Helper functions (BASE_URL, pricing, etc.)
│   ├── header.php         → Common header UI
│   └── footer.php         → Common footer UI
│
├── admin/                 → Admin Panel
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── manage_slots.php
│   ├── bookings.php
│   └── manage_users.php
│
├── user/                  → User Panel
│   ├── register.php
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── book_slot.php
│   ├── history.php
│   ├── cancel.php
│   └── live_slots.php
│
├── assets/
│   ├── style.css          → Custom styles
│   └── script.js          → JS logic (price calc, live slots)
```

---

# 🛢️ Database Tables

* `users`
* `parking_slots`
* `bookings`

---

# 💰 Pricing Logic

* ₹20 per hour
* Price = Duration × Rate

---

# ⚠️ Booking Rule

The system prevents **double booking** using:

```sql
existing.start < new.end AND existing.end > new.start
```

---

# ☁️ AWS Deployment (LAMP)

## 🔹 Connect to EC2

```bash
ssh -i parking-key-new.pem ubuntu@51.20.87.204
```

---

## 🔹 Install LAMP

```bash
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql -y
```

---

## 🔹 Clone Project

```bash
cd /var/www/html/
sudo git clone https://github.com/Vedant-Shigwan11/Smart-Vehicle-Parking-System-using-LAMP.git
```

---

## 🔹 Set Permissions

```bash
sudo chown -R www-data:www-data Smart-Vehicle-Parking-System-using-LAMP
```

---

## 🔹 Setup Database

```bash
sudo mysql
```

```sql
CREATE DATABASE smart_parking;

CREATE USER 'parking_user'@'localhost' IDENTIFIED BY 'password123';

GRANT ALL PRIVILEGES ON smart_parking.* TO 'parking_user'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

---

## 🔹 Import Database

```bash
mysql -u parking_user -p smart_parking < config/database.sql
```

---

## 🔹 Configure DB

Edit:

```
config/db.php
```

```php
$conn = new mysqli("localhost", "parking_user", "password123", "smart_parking");
```

---

## 🔹 Set BASE_URL

```
includes/functions.php
```

```php
define('BASE_URL', '/Smart-Vehicle-Parking-System-using-LAMP/');
```

---

## 🔹 Restart Apache

```bash
sudo systemctl restart apache2
```

---

## 🌍 Access Project

```
http://51.20.87.204/Smart-Vehicle-Parking-System-using-LAMP/
```

---

# 🔧 Useful Commands

## Restart Server

```bash
sudo systemctl restart apache2
```

## Stop Server

```bash
sudo systemctl stop apache2
```

## Pull Latest Code

```bash
git pull origin main
```

## Check Files

```bash
ls /var/www/html/
```

---

# 🧠 Learning Outcomes

* LAMP stack deployment
* AWS EC2 hosting
* Full-stack PHP development
* Secure authentication system
* Real-world booking logic implementation

---

# 📌 Author

**Vedant Shigwan**
AI & Data Science Student
Pune, India 🇮🇳

---

# ⭐ Future Improvements

* Payment integration (Razorpay)
* Google Maps integration
* AI-based parking prediction
* Email/SMS notifications
* Mobile app version

---
