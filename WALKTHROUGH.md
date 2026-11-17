# Library System — How It All Works (Team Walkthrough)

A guide for everyone in the group. It explains **what each folder holds**, **what the
files do**, and **how a single click travels through all the layers** — from the browser,
into the PHP code, down to the MySQL database, and back.

No prior knowledge of the whole system is assumed. If you only ever touched one folder,
this will show you how your part fits into the rest.

---

## 1. The big picture — the "layers"

Our app is a classic **PHP + MySQL** web app. Every action moves through the same layers:

```
   YOU (the browser)
        |   click a link / submit a form  (an HTTP request)
        v
   A PHP PAGE          e.g. books/create.php      <-- decides what to do
        |   uses shared helpers
        v
   INCLUDES / CONFIG   includes/*.php, config/database.php
        |   runs a safe, prepared SQL query through $pdo
        v
   MySQL DATABASE      library_db  (6 tables)
        |   returns rows / confirms a change
        v
   PHP builds the HTML page  (header + content + footer)
        |
        v
   YOU see the result in the browser
```

The important idea: **a PHP page is the "controller."** It reads the request, checks you
are logged in, talks to the database through `$pdo`, and then prints HTML. The same four
layers are involved whether you are adding a book or returning a loan.

---

## 2. The folder map

| Folder / file | What it holds | Layer |
|---|---|---|
| `index.php` (root) | Front door. Redirects to the dashboard (if logged in) or the login page. | Entry |
| `config/` | `database.php` — the **one** place that connects to MySQL and gives every page `$pdo`. | Config |
| `includes/` | Shared building blocks reused by every page (session, helpers, the template). | Shared core |
| `auth/` | Logging in, logging out, and the loading/splash screen. | Feature |
| `dashboard/` | The home screen after login (live date/time, stats + overdue/recent loans). | Feature |
| `books/` | Manage books — list, add, edit, delete. | Feature (CRUD) |
| `members/` | Manage library members. | Feature (CRUD) |
| `authors/` | Manage authors. | Feature (CRUD) |
| `categories/` | Manage book categories. | Feature (CRUD) |
| `loans/` | Issue a book, return a book, list loans. | Feature |
| `users/` | Manage **system logins** (admin-only). | Feature (CRUD) |
| `search/` | Global search across books, members, authors, categories. | Feature |
| `assets/` | `css/style.css` (all the styling) and `images/` (logos, login photos). | Front-end |
| `database/` | `library_db.sql` (the schema + data), `queries.sql`, `erd.png`. | Database |
| `docs/` | Reports / documentation (e.g. `report.pdf`). | Docs |

> **Rule of thumb:** each *feature folder* is self-contained and usually has the same four
> files — `index.php` (list), `create.php` (add), `edit.php` (change), `delete.php` (remove).

---

## 3. The shared core (`includes/` + `config/`) — read this first

These files are included by almost every page. Understand them once and every page makes
sense.

### `config/database.php` — the database connection
- Builds **one** PDO connection to MySQL and stores it in `$pdo`.
- Settings are XAMPP defaults: host `localhost`, database `library_db`, user `root`,
  empty password.
- Turns on three safety options: real errors (exceptions), rows as simple arrays, and
  **real prepared statements** (strong protection against SQL injection).
- Every page does `require_once __DIR__ . '/../config/database.php';` and can then use `$pdo`.

### `includes/functions.php` — the helpers + the session
- Starts the **session** (how the app remembers who is logged in between pages).
- `e($text)` — escapes text before printing it as HTML (stops broken layouts and XSS).
  You will see `<?= e($row['title']) ?>` everywhere — always print user data through `e()`.
- `set_message($text, $type)` — saves a one-time "flash" message for the **next** page.
- `show_message()` — prints that message. (We upgraded it to a **toast popup** that slides
  in and fades away.)

### `includes/auth_check.php` — the guard
- The first line of every logged-in page: `require_once __DIR__ . '/../includes/auth_check.php';`
- If there is no `user_id` in the session, it redirects to the login page and **stops**.
- This is why you cannot open `books/index.php` without logging in first.

### `includes/header.php`, `navbar.php`, `footer.php` — the template
Instead of repeating the `<html>`, menu, and footer on every page, we include them:

```php
$page_title = 'Books';
require_once __DIR__ . '/../includes/header.php';   // <html>, <head>, CSS, navbar, flash
//   ... the page's own content ...
require_once __DIR__ . '/../includes/footer.php';   // closes the tags, footer, scripts
```

- `header.php` loads the stylesheet, shows the **navbar** (only when logged in), and prints
  any flash message.
- `navbar.php` builds the menu, highlights the current section, shows the **global search
  box**, and hides the **Users** link unless your role is `admin`.
- `footer.php` closes the page and holds the small script that auto-dismisses toasts.

**One change here = every page changes.** That is the whole point of the template.

---

## 4. The database (`library_db`) — 6 tables

```
authors ---<  books  >--- categories
                 |
                 |  (a book can be on many loans)
                 v
               loans  >--- members
                 ^
                 |
users (separate: these are the people who LOG IN)
```

| Table | Key columns | Notes |
|---|---|---|
| `users` | `id`, `username`, `password`, `full_name`, `role` | Logins. `role` is `admin` or `librarian`. `password` is a **bcrypt hash**, never plain text. |
| `authors` | `id`, `name`, `country` | A book has one author. |
| `categories` | `id`, `name`, `description` | A book has one category. |
| `books` | `id`, `isbn`, `title`, `author_id`→authors, `category_id`→categories, `total_copies`, `available_copies` | `available_copies` goes down when issued, up when returned. |
| `members` | `id`, `member_number`, `full_name`, `email`, `phone`, `status` | People who **borrow** books. `status` is `active` or `suspended`. |
| `loans` | `id`, `member_id`→members, `book_id`→books, `date_issued`, `due_date`, `date_returned` | `date_returned` is `NULL` while a book is still out. |

**Two kinds of "people" — don't mix them up:**
- `users` = staff who **log into** the system (admin / librarian).
- `members` = library patrons who **borrow** books. They never log in.

**How loan status is worked out** (it is *not* stored as a column):
- `date_returned` is set → **returned**
- `date_returned` is NULL and `due_date` < today → **overdue**
- `date_returned` is NULL and `due_date` ≥ today → **out**

---

## 5. The repeating CRUD pattern (learn it once, applies to 4 folders)

`books/`, `members/`, `authors`, `categories` all follow the same shape:

| File | Job | Request type |
|---|---|---|
| `index.php` | **List** everything in a table, with Edit/Delete buttons. | GET |
| `create.php` | Show an **add** form; on submit, validate and `INSERT`. | GET shows form, POST saves |
| `edit.php` | Load one row, show a filled form; on submit, validate and `UPDATE`. | GET + POST |
| `delete.php` | **Remove** a row. POST-only so a stray link can't delete. | POST |

Every one of these:
1. `require` auth_check + database,
2. runs a **prepared statement** (the `?` placeholders keep user input out of the SQL),
3. sets a flash message, and
4. redirects back to `index.php`.

---

## 6. Flow walkthrough #1 — Logging in

Follow a login through every layer:

1. **Browser** → you open the site. Root `index.php` sees no session → redirects to
   `auth/login.php`.
2. `auth/login.php` shows the split-screen form. You type `admin` / `admin123` and submit
   (**POST**).
3. The same file runs the check:
   - Finds the user with a **prepared statement**:
     `SELECT id, password, full_name, role FROM users WHERE username = ?`
   - Verifies the password with `password_verify($typed, $stored_hash)` — the stored value
     is a **bcrypt hash**, so we never compare plain text.
4. If correct: it saves `user_id`, `full_name`, `role` into the **session**, sets a
   "Welcome" flash, and redirects to `auth/splash.php?next=dashboard` (the loading screen),
   which then forwards to the dashboard.
5. If wrong: it shows "Wrong username or password." right on the form (same message for
   bad username or bad password — safer).
6. Every page you visit afterwards runs `auth_check.php`, sees the `user_id`, and lets you
   through.

**Layers touched:** browser → login.php (controller) → functions.php (session/helpers) →
`$pdo` → `users` table → back to HTML.

---

## 7. Flow walkthrough #2 — Issuing a book (a *transaction*)

This is the most important flow to understand because it changes **two tables at once**.

`loans/issue.php` does two things that must both succeed or both fail:
1. **Add** a row to `loans`.
2. **Subtract 1** from that book's `available_copies`.

If step 1 worked but step 2 crashed, our numbers would be wrong forever. So we wrap them in
a **transaction**:

```php
$pdo->beginTransaction();
  // 1. INSERT INTO loans (...)
  // 2. UPDATE books SET available_copies = available_copies - 1 WHERE id = ?
$pdo->commit();      // make BOTH permanent
// ...on any error:
$pdo->rollBack();    // undo EVERYTHING
```

Before saving, the page also **re-checks on the server** that the member is still `active`
and the book still has a copy free — even though the dropdowns only show valid options.
**Never trust the browser; always re-check on the server.**

**Returning** (`loans/return.php`) is the mirror image, also a transaction:
1. set `date_returned = today`, and
2. **add 1 back** to `available_copies`.
It first checks the loan isn't already returned, so a copy can't be added back twice.

---

## 8. Flow walkthrough #3 — Deleting safely

`books/delete.php` shows how we protect the data:
- It is **POST-only** (a plain link or web crawler can't trigger a delete).
- Before deleting a book, it checks: *is this book in any loan?*
  `SELECT COUNT(*) FROM loans WHERE book_id = ?`
- If yes → it refuses with a friendly message instead of crashing on a foreign-key error.
- If no → it deletes.

Members work the same way (a member with loans can't be deleted).

---

## 9. Flow walkthrough #4 — Search & filters

- **Global search** (`search/index.php`): the navbar box submits `?q=...`. The page runs a
  `LIKE %term%` prepared query against books, members, authors and categories, then shows
  grouped results with an "Open" link to each record.
- **Filters** (`members/index.php`, `loans/index.php`): a status dropdown adds a `WHERE`
  condition and reloads the list via `?status=...`. Because it's in the URL (GET), a
  filtered view can be bookmarked or shared.

Both build the SQL **with placeholders**, so filtering is injection-safe.

---

## 10. Security — the four habits in this codebase

1. **Prepared statements everywhere.** User input goes in through `?` placeholders, never
   glued into the SQL string. Stops SQL injection.
2. **Hashed passwords.** `password_hash()` to store, `password_verify()` to check. We can
   never read a password back.
3. **`auth_check.php` on every page.** No session → no access.
4. **`e()` on every piece of output.** Escapes HTML so data can't break the page or inject
   scripts.

Plus **roles**: only `admin` sees the **Users** menu and can open the `users/` pages.

---

## 11. Front-end — where the "look" lives

- **All** styling is in one file: `assets/css/style.css`. Change a color or spacing there
  and it updates across the whole app.
- The brand teal is `#0e7490`. We use **inline SVG icons** (not emojis) for things like the
  password eye, the search magnifier, and the dashboard cards.
- `assets/images/` holds the two logos (standalone owl + wordmark) and the login photos.

---

## 12. Working as a group (git)

- Everyone **pulls** before starting and **pushes** small, finished changes.
- Try to work in **different files/folders** from your teammates to avoid merge conflicts
  (the folders are split by feature on purpose).
- The feature folders are independent, so two people can work on `books/` and `members/`
  at the same time with little overlap.

---

## 13. Quick glossary

| Term | Plain meaning |
|---|---|
| **PDO** | PHP's safe way to talk to the database. We keep one connection in `$pdo`. |
| **Prepared statement** | A query with `?` placeholders; the values are sent separately so they can't be treated as code. |
| **Session** | Server-side memory that remembers you're logged in across pages. |
| **Flash message** | A one-time message shown on the next page (our toast popup). |
| **Transaction** | A group of changes that all succeed or all get undone together. |
| **Foreign key (FK)** | A column pointing at another table's `id` (e.g. `loans.book_id` → `books.id`). |
| **CRUD** | Create, Read, Update, Delete — the four things we do to each table. |
| **Hash** | A one-way scramble of a password; you can check it but not reverse it. |

---

*Tip: open a file next to this guide and read its top comment block — every file starts with
a short explanation of what it does.*
