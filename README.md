**GitHub Repository:** [Library Management System](https://github.com/amelordybirungi/LIBRABRY-MANAGEMENT-SYSTEM-PROJECT)
# Library Management System Group 14

This  web-based Library Management System built with PHP and MySQL.The system is to bridge the gap between the librarian and administrator to manage library records through browsing the interface.
staff manage books, authors, categories, members and loans, with secure login,
it includes role-based access and a live statistics dashboard.

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
 **Logins to admin **Account**
- **Username:** `admin`
- **Password:** `admin123`

 **Logins to librarian Account**
 - **Username:** `librarian`
- **Password:** `librarian123`
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

1. Muwanguzi Gibroan Wejuli 262-141
2. Erika Lawrence 255-867
3. Okwri Philemon Samson 256-323
4. Namaganda Victoria	257-109
5. Birungi Amelordy	264-820
6. Mandela Robert Danty	257-891
7. Kakande Ismail	275-273
8. Oketayot George William	224-649
9. Abu Bakarr Samura	239-892
10. Loboe Mohammed	233-274


