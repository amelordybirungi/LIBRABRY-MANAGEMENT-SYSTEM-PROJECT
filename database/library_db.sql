-- =====================================================================
--  Library Management System - library_db.sql
--  This file creates the database, creates all 6 tables,
--  and inserts sample data.
--  How to use: phpMyAdmin -> Import -> choose this file -> Go
-- =====================================================================


-- ---------------------------------------------------------------------
-- STEP 1: Create the database (only if it does not exist yet)
--         and tell MySQL we want to work inside it.
-- ---------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS library_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE library_db;


-- ---------------------------------------------------------------------
-- STEP 2: Delete old tables (if any) so we can re-import cleanly.
--         Order matters: delete "child" tables (loans, books)
--         before the "parent" tables they point to.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS users;


-- =====================================================================
-- STEP 3: Create the tables
-- =====================================================================

-- ---------------------------------------------------------------------
-- Table 1: authors  (people who write books)
-- ---------------------------------------------------------------------
CREATE TABLE authors (
    author_id  INT AUTO_INCREMENT PRIMARY KEY,  -- unique number, MySQL fills it in (1, 2, 3...)
    name       VARCHAR(100) NOT NULL,           -- NOT NULL = this column cannot be left empty
    country    VARCHAR(60)                      -- may be empty
);

-- ---------------------------------------------------------------------
-- Table 2: categories  (types of books, e.g. Novel, Poetry)
-- ---------------------------------------------------------------------
CREATE TABLE categories (
    category_id  INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(60) NOT NULL UNIQUE,   -- UNIQUE = no two categories with the same name
    description  VARCHAR(255)
);

-- ---------------------------------------------------------------------
-- Table 3: books
--   author_id and category_id are FOREIGN KEYS: they point to a row
--   in the authors / categories tables instead of repeating the name.
-- ---------------------------------------------------------------------
CREATE TABLE books (
    book_id           INT AUTO_INCREMENT PRIMARY KEY,
    isbn              VARCHAR(20)  NOT NULL UNIQUE,   -- every book has a different ISBN
    title             VARCHAR(150) NOT NULL,
    author_id         INT NOT NULL,
    category_id       INT NOT NULL,
    publisher         VARCHAR(100),
    year_published    INT,
    total_copies      INT NOT NULL DEFAULT 1,         -- DEFAULT = value used if none is given
    available_copies  INT NOT NULL DEFAULT 1,

    -- CHECK = a rule MySQL enforces on every insert/update
    CHECK (total_copies >= 0),
    CHECK (available_copies >= 0 AND available_copies <= total_copies),

    -- Link to the parent tables.
    -- ON DELETE RESTRICT = MySQL refuses to delete an author/category
    -- that still has books (protects our data).
    FOREIGN KEY (author_id)   REFERENCES authors(author_id)       ON DELETE RESTRICT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)  ON DELETE RESTRICT
);

-- ---------------------------------------------------------------------
-- Table 4: members  (people who borrow books)
-- ---------------------------------------------------------------------
CREATE TABLE members (
    member_id      INT AUTO_INCREMENT PRIMARY KEY,
    member_number  VARCHAR(20)  NOT NULL UNIQUE,     -- e.g. MEM001, printed on the library card
    full_name      VARCHAR(100) NOT NULL,
    email          VARCHAR(100) NOT NULL UNIQUE,
    phone          VARCHAR(20),
    status         VARCHAR(10)  NOT NULL DEFAULT 'active',
    date_joined    DATE NOT NULL,

    -- status can only be one of these two words
    CHECK (status IN ('active', 'suspended'))
);

-- ---------------------------------------------------------------------
-- Table 5: loans  (which member borrowed which book, and when)
--   date_returned is NULL (empty) while the book is still out.
-- ---------------------------------------------------------------------
CREATE TABLE loans (
    loan_id        INT AUTO_INCREMENT PRIMARY KEY,
    member_id      INT  NOT NULL,
    book_id        INT  NOT NULL,
    date_issued    DATE NOT NULL,
    due_date       DATE NOT NULL,
    date_returned  DATE NULL DEFAULT NULL,

    CHECK (due_date >= date_issued),

    -- ON DELETE RESTRICT: a member or book that appears in loans
    -- cannot be deleted. Our PHP code will check this first and
    -- show a friendly message instead of an error.
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE RESTRICT,
    FOREIGN KEY (book_id)   REFERENCES books(book_id)     ON DELETE RESTRICT
);

-- ---------------------------------------------------------------------
-- Table 6: users  (librarians who log in to the system)
--   We NEVER store the real password, only its hash
--   (made with PHP's password_hash()).
-- ---------------------------------------------------------------------
CREATE TABLE users (
    user_id        INT AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(50)  NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,
    full_name      VARCHAR(100) NOT NULL,
    role           VARCHAR(10)  NOT NULL DEFAULT 'librarian',

    CHECK (role IN ('admin', 'librarian'))
);


-- =====================================================================
-- STEP 4: Insert sample data
--   Parent tables first (authors, categories), then books,
--   because a book must point to an author and category that exist.
-- =====================================================================

-- 5 authors (they get author_id 1 to 5 in this order)
INSERT INTO authors (name, country) VALUES
('Chinua Achebe',            'Nigeria'),   -- 1
('Ngugi wa Thiong''o',       'Kenya'),     -- 2  ('' = how you write an apostrophe inside text)
('Chimamanda Ngozi Adichie', 'Nigeria'),   -- 3
('Okot p''Bitek',            'Uganda'),    -- 4
('George Orwell',            'United Kingdom'); -- 5

-- 5 categories (category_id 1 to 5)
INSERT INTO categories (name, description) VALUES
('Novel',              'Long fictional stories'),             -- 1
('Poetry',             'Poems and songs'),                    -- 2
('Historical Fiction', 'Stories set during real events'),     -- 3
('Dystopian',          'Stories about a bad imagined future'),-- 4
('Satire',             'Stories that mock society or power'); -- 5

-- 10 books (book_id 1 to 10). ISBNs are made-up sample values.
-- available_copies = total_copies minus the loans still out (see loans below)
INSERT INTO books (isbn, title, author_id, category_id, publisher, year_published, total_copies, available_copies) VALUES
('978-0-00-000001-0', 'Things Fall Apart',     1, 1, 'Heinemann',          1958, 5, 4), -- 1 (1 out)
('978-0-00-000002-0', 'Arrow of God',          1, 1, 'Heinemann',          1964, 3, 2), -- 2 (1 out)
('978-0-00-000003-0', 'Weep Not, Child',       2, 1, 'Heinemann',          1964, 4, 4), -- 3
('978-0-00-000004-0', 'Petals of Blood',       2, 1, 'Heinemann',          1977, 2, 1), -- 4 (1 out)
('978-0-00-000005-0', 'Purple Hibiscus',       3, 1, 'Fourth Estate',      2003, 1, 0), -- 5 (1 out -> 0 left, use it to test "cannot issue")
('978-0-00-000006-0', 'Half of a Yellow Sun',  3, 3, 'Fourth Estate',      2006, 3, 2), -- 6 (1 out)
('978-0-00-000007-0', 'Song of Lawino',        4, 2, 'East African Publishers', 1966, 4, 4), -- 7
('978-0-00-000008-0', 'Song of Ocol',          4, 2, 'East African Publishers', 1970, 2, 2), -- 8
('978-0-00-000009-0', 'Nineteen Eighty-Four',  5, 4, 'Secker & Warburg',   1949, 3, 2), -- 9 (1 out)
('978-0-00-000010-0', 'Animal Farm',           5, 5, 'Secker & Warburg',   1945, 2, 2); -- 10

-- 10 members (member_id 1 to 10). Member 9 is suspended (to test that rule).
INSERT INTO members (member_number, full_name, email, phone, status, date_joined) VALUES
('MEM001', 'Aisha Nakato',     'aisha.nakato@example.com',     '0700000001', 'active',    '2026-01-10'), -- 1
('MEM002', 'Brian Okello',     'brian.okello@example.com',     '0700000002', 'active',    '2026-01-15'), -- 2
('MEM003', 'Catherine Atim',   'catherine.atim@example.com',   '0700000003', 'active',    '2026-02-01'), -- 3
('MEM004', 'David Mugisha',    'david.mugisha@example.com',    '0700000004', 'active',    '2026-02-20'), -- 4
('MEM005', 'Esther Namubiru',  'esther.namubiru@example.com',  '0700000005', 'active',    '2026-03-05'), -- 5
('MEM006', 'Francis Ssempala', 'francis.ssempala@example.com', '0700000006', 'active',    '2026-03-18'), -- 6
('MEM007', 'Grace Achieng',    'grace.achieng@example.com',    '0700000007', 'active',    '2026-04-02'), -- 7
('MEM008', 'Henry Kato',       'henry.kato@example.com',       '0700000008', 'active',    '2026-05-11'), -- 8
('MEM009', 'Irene Nansubuga',  'irene.nansubuga@example.com',  '0700000009', 'suspended', '2026-06-07'), -- 9
('MEM010', 'James Opio',       'james.opio@example.com',       '0700000010', 'active',    '2026-07-21'); -- 10

-- 11 loans. Due date = issue date + 14 days.
--   5 returned (date_returned has a date)
--   6 still out (date_returned is NULL)
--   loans 6 and 7 are OVERDUE (due date has passed, not returned)
INSERT INTO loans (member_id, book_id, date_issued, due_date, date_returned) VALUES
(1,  1, '2026-09-01', '2026-09-15', '2026-09-14'),  -- 1  returned on time
(2,  3, '2026-09-05', '2026-09-19', '2026-09-18'),  -- 2  returned on time
(3,  6, '2026-09-10', '2026-09-24', '2026-09-30'),  -- 3  returned LATE
(4,  7, '2026-09-20', '2026-10-04', '2026-10-02'),  -- 4  returned on time
(5, 10, '2026-10-01', '2026-10-15', '2026-10-10'),  -- 5  returned on time
(1,  2, '2026-10-05', '2026-10-19', NULL),          -- 6  still out - OVERDUE
(6,  4, '2026-10-08', '2026-10-22', NULL),          -- 7  still out - OVERDUE
(7,  1, '2026-10-20', '2026-11-03', NULL),          -- 8  still out
(8,  6, '2026-10-25', '2026-11-08', NULL),          -- 9  still out
(10, 9, '2026-10-27', '2026-11-10', NULL),          -- 10 still out
(3,  5, '2026-10-28', '2026-11-11', NULL);          -- 11 still out (Purple Hibiscus now has 0 copies)

-- 2 test accounts for logging in. These are FAKE test passwords:
--   username: admin       password: admin123
--   username: librarian   password: librarian123
-- The long text starting with $2y$ is the hash made by password_hash().
INSERT INTO users (username, password_hash, full_name, role) VALUES
('admin',     '$2y$10$No1zDKCGNGNnkri/IbwpxeebE/aIoIrgUVTFvT0FgqTe7nln4wcpG', 'System Administrator', 'admin'),
('librarian', '$2y$10$X8Ei8SSREm.gaXABR1t7Mu.91nN1wMHpG4eK3TIjDYQy/ilXSrYDy', 'Test Librarian',       'librarian');