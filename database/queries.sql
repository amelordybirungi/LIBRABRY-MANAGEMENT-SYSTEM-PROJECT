USE library_db;

-- QUERY 1 (JOIN): All books with their author name and category name
SELECT  b.isbn,
        b.title,
        a.name  AS author,          -- AS = give the column a nicer name
        c.name  AS category,
        b.available_copies,
        b.total_copies
FROM books b                         -- "b" is a short nickname for books
JOIN authors    a ON b.author_id   = a.author_id     -- match each book to its author
JOIN categories c ON b.category_id = c.category_id   -- match each book to its category
ORDER BY b.title;                    -- sort A to Z

-- QUERY 2 (JOIN): Books currently on loan, with the member's name
SELECT  m.member_number,
        m.full_name  AS member,
        b.title      AS book,
        l.date_issued,
        l.due_date
FROM loans l
JOIN members m ON l.member_id = m.member_id
JOIN books   b ON l.book_id   = b.book_id
WHERE l.date_returned IS NULL        -- only loans not yet returned
ORDER BY l.due_date;                 -- the ones due soonest first

-- QUERY 3 (JOIN): Overdue loans and how many days late they are
SELECT  m.full_name  AS member,
        m.phone,
        b.title      AS book,
        l.due_date,
        DATEDIFF(CURDATE(), l.due_date) AS days_overdue
FROM loans l
JOIN members m ON l.member_id = m.member_id
JOIN books   b ON l.book_id   = b.book_id
WHERE l.date_returned IS NULL
  AND l.due_date < CURDATE()
ORDER BY days_overdue DESC;          -- most overdue first

-- QUERY 4 (AGGREGATE): Number of loans per category
SELECT  c.name           AS category,
        COUNT(l.loan_id) AS number_of_loans
FROM categories c
JOIN books b ON b.category_id = c.category_id
JOIN loans l ON l.book_id     = b.book_id
GROUP BY c.name
ORDER BY number_of_loans DESC;

-- QUERY 5 (AGGREGATE): Stock report per author
SELECT  a.name                   AS author,
        COUNT(b.book_id)         AS number_of_books,
        SUM(b.total_copies)      AS total_copies,
        SUM(b.available_copies)  AS copies_on_shelf
FROM authors a
JOIN books b ON b.author_id = a.author_id
GROUP BY a.name
ORDER BY a.name;