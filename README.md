# Library Management System

A web-based Library Management System built with PHP and MySQL. It lets library
staff manage books, authors, categories, members and loans, with secure login,
role-based access and a live statistics dashboard.

This project was developed as a group coursework project.

## Features

- Secure login with hashed passwords (bcrypt) and session-based authentication
- Role-based access: **admin** and **librarian**
- Super admin (username `admin`) who can add, edit and delete system users
- Books, Authors and Categories management (create, read, update, delete)
- Members management with profile photo upload
- Loans: issue books, record returns and track overdue items
- Search across the catalogue
- Dashboard with live statistics (total books, members, books out, overdue)
- Responsive layout that works on desktop and mobile

## Tech Stack

- **Backend:** PHP (procedural, using PDO prepared statements)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML, CSS, JavaScript
- **Server:** Apache (via XAMPP)

## Requirements

- [XAMPP](https://www.apachefriends.org/) (includes Apache, PHP and MySQL/MariaDB)
- A modern web browser

## Setup and Installation

1. **Install and start XAMPP.** Open the XAMPP Control Panel and start **Apache**
   and **MySQL**.

2. **Place the project in htdocs.** Clone or copy this project into your XAMPP
   `htdocs` folder so the path is:

   ```
   C:\xampp\htdocs\library-system
   ```

3. **Create the database.** Open phpMyAdmin at
   `http://localhost/phpmyadmin`, then:
   - Create a new database named `library_db`, **or**
   - Use the **Import** tab to import `database/library_db.sql`
     (this creates the tables and loads sample data).

4. **Check the database settings.** The connection details are in
   `config/database.php`. The defaults match a standard XAMPP install:

   ```
   Host:     localhost
   Database: library_db
   User:     root
   Password: (empty)
   ```

5. **Open the app** in your browser:

   ```
   http://localhost/library-system
   ```

## Default Login

Use the following account to sign in for the first time:

- **Username:** `PLACEHOLDER_USERNAME`
- **Password:** `PLACEHOLDER_PASSWORD`

> The `admin` account is the **super admin** and is the only user that can add,
> edit or delete other system users.

## Project Structure

```
library-system/
├── assets/          CSS, JavaScript, images and member photo uploads
├── auth/            Login, logout and splash screen
├── authors/         Author management
├── books/           Book management
├── categories/      Category management
├── config/          Database connection settings
├── dashboard/       Dashboard with statistics
├── database/        SQL schema, sample data and ERD
├── docs/            Project report and screenshots
├── includes/        Shared header, footer, navbar and helper functions
├── loans/           Issue and return books
├── members/         Member management (with photo upload)
├── search/          Catalogue search
├── users/           System user management (super admin only)
└── index.php        Entry point
```

## Screenshots

Screenshots of the system are available in the `docs/screenshots/` folder.

## Documentation

- **Project report:** `docs/Library Management system Project Report.docx`
- **Entity Relationship Diagram (ERD):** see the project report / `database/`

## Team

This project was developed by:

1. 
2. PLACEHOLDER_NAME_2
3. PLACEHOLDER_NAME_3
4. PLACEHOLDER_NAME_4
5. PLACEHOLDER_NAME_5
6. PLACEHOLDER_NAME_6
7. PLACEHOLDER_NAME_7
8. PLACEHOLDER_NAME_8
9. PLACEHOLDER_NAME_9
10. PLACEHOLDER_NAME_10

## Disclaimer

This project was built for educational purposes as part of a coursework
assignment.
