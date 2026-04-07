# Smart Parking Booking System

Smart Parking Booking System is a beginner-friendly full-stack PHP project built for XAMPP using Apache, MySQL, PHP, Bootstrap, HTML, CSS, and JavaScript.

It allows users to register, log in, view parking slots, book a slot for a selected date and time, check booking history, and cancel bookings. It also provides an admin panel to manage parking slots, bookings, and users.

## Features

### User Features
- User registration with secure password hashing
- User login and logout
- View parking slot availability
- Book parking slots using date and time
- Automatic price calculation at `Rs. 20/hour`
- Prevent double booking with overlap checking
- View booking history
- Cancel active bookings

### Admin Features
- Admin login and logout
- Dashboard with parking and booking statistics
- Add, edit, and delete parking slots
- View all bookings
- Manage registered users

### Technical Features
- Built with PHP and MySQL
- Uses MySQLi prepared statements
- Session-based authentication
- Bootstrap responsive UI
- Reusable header and footer components
- JavaScript-based live slot updates

## Project Structure

```text
smart-parking/
│
├── index.php
│
├── config/
│   ├── db.php
│   └── database.sql
│
├── includes/
│   ├── auth.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── manage_slots.php
│   ├── bookings.php
│   └── manage_users.php
│
├── user/
│   ├── register.php
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── book_slot.php
│   ├── history.php
│   ├── cancel.php
│   └── live_slots.php
│
└── assets/
    ├── style.css
    └── script.js
```

## Database Tables

The project uses these main tables:

- `users`
- `parking_slots`
- `bookings`

## Default Admin Login

After importing the SQL file, you can log in as admin using:

- Email: `admin@smartparking.local`
- Password: `admin123`

## User Login
- Email: `usertest@gmail.com`
- password: `user123`

## How to Run the Project in XAMPP

1. Copy the project folder into `C:\xampp\htdocs\`
2. Rename the project folder to `smart-parking` if needed
3. Start `Apache` and `MySQL` from the XAMPP Control Panel
4. Open `phpMyAdmin`
5. Import the file `config/database.sql`
6. Check the database connection settings in `config/db.php`
7. Open the project in your browser:

```text
http://localhost/Smart-Vehicle-Parking-System-using-LAMP/user/login.php

default
admin login
http://localhost/Smart-Vehicle-Parking-System-using-LAMP/admin/login.php
```

## Main Workflow

### User Side
- Register an account
- Login to the system
- View available parking slots
- Book a slot with start and end time
- View and cancel bookings

### Admin Side
- Login as admin
- View system statistics
- Manage slots
- View all bookings
- Manage users

## Security Used

- Passwords are stored using `password_hash()`
- Login verification uses `password_verify()`
- Prepared statements help prevent SQL injection
- Sessions are used for authentication

## Price Logic

- Parking price is calculated at `Rs. 20 per hour`
- Total price depends on booking duration

## Booking Rule

The system prevents double booking by checking whether a selected slot already has another active booking during the same time range.

## Developed For

- LAMP stack learning
- XAMPP local server on Windows
- Beginner to intermediate PHP/MySQL practice

