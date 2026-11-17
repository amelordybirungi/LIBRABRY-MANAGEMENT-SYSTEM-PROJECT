<?php

require_once __DIR__ . '/../includes/auth_check.php';

$due = date('j M Y', strtotime('+14 days'));

$page_title = 'Checkout (template)';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- a small note so nobody thinks this button works yet -->
<div class="co-preview-note">
    Design preview &mdash; a static template for the group to review. Not connected to the database yet.
</div>

<div class="co-crumb">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    <span><b>Home</b> / Loans</span>
</div>

<div class="co-grid">
    <div class="co-left">
        <div class="co-steps">
            <div class="co-step active">
                <span class="co-check on">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                Borrower Information
            </div>
            <div class="co-step muted">
                <span class="co-check"></span>
                Confirm Loan
            </div>
        </div>

        <h2 class="co-h">Check Out These Books</h2>
        <p class="co-sub">Review the borrower and the books below before issuing the loan.</p>

        <div class="co-row">
            <div class="co-field focus">
                <span class="co-ico">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span class="co-field-text">
                    <span class="co-lab">First Name</span>
                    <span class="co-val">Amina</span>
                </span>
            </div>
            <div class="co-field">
                <span class="co-ico">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span class="co-field-text">
                    <span class="co-lab">Last Name</span>
                    <span class="co-val">Yusuf</span>
                </span>
            </div>
        </div>

        <div class="co-address">
            <span class="co-ico">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
            <span class="co-field-text">
                <span class="co-lab">Member &middot; MBR-1042</span>
                <span class="co-val">Al Helal 36, Noyasark, Sylhet 3100</span>
            </span>
            <button type="button" class="co-edit" aria-label="Edit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
            </button>
        </div>

        <h2 class="co-h co-h-sm">Checkout Method</h2>
        <p class="co-sub">Choose how the borrower will receive the books.</p>

        <label class="co-method">
            <span class="co-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            </span>
            <span class="co-method-label">Collect at the library desk</span>
            <span class="co-radio"></span>
        </label>

        <label class="co-method sel">
            <span class="co-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </span>
            <span class="co-method-label">Home delivery</span>
            <span class="co-radio on"></span>
        </label>

        <label class="co-method">
            <span class="co-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <span class="co-method-label">Reserve for pickup later</span>
            <span class="co-radio"></span>
        </label>
    </div>

    <aside class="co-summary">
        <h3 class="co-sum-title">Current Loan</h3>
        <p class="co-sum-sub">The books being issued to this borrower.</p>

        <div class="co-item">
            <span class="co-thumb">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </span>
            <div class="co-item-main">
                <div class="co-item-title">Things Fall Apart</div>
                <div class="co-item-code">TP399K</div>
                <div class="co-stepper">
                    <button type="button">&minus;</button>
                    <span>1</span>
                    <button type="button">+</button>
                </div>
            </div>
            <span class="co-chip">On shelf</span>
        </div>

        <div class="co-item">
            <span class="co-thumb">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </span>
            <div class="co-item-main">
                <div class="co-item-title">Half of a Yellow Sun</div>
                <div class="co-item-code">TP412Y</div>
                <div class="co-stepper">
                    <button type="button">&minus;</button>
                    <span>1</span>
                    <button type="button">+</button>
                </div>
            </div>
            <span class="co-chip">On shelf</span>
        </div>

        <div class="co-total-row">
            <span>Total books</span>
            <b>2</b>
        </div>

        <div class="co-lines">
            <div class="co-line"><span>Items</span><span>2</span></div>
            <div class="co-line"><span>Loan period</span><span>14 days</span></div>
            <div class="co-line"><span>Due date</span><span><?= e($due) ?></span></div>
            <div class="co-line"><span>Outstanding fines</span><span>None</span></div>
        </div>

        <button type="button" class="co-pay">Confirm Loan</button>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
