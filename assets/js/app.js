(function () {
    'use strict';

    // ---- Flash toasts: slide out and remove themselves after a few seconds
    document.querySelectorAll('.toast').forEach(function (toast) {
        setTimeout(function () {
            toast.classList.add('toast-hide');
            setTimeout(function () { toast.remove(); }, 300);
        }, 3500);
    });

    // ---- Close button (X) on a toast removes it immediately
    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('.toast-close') : null;
        if (btn) {
            var toast = btn.closest('.toast');
            if (toast) toast.remove();
        }
    });

    // ---- Tables: wrap each one so a wide table scrolls sideways on small
    //      screens instead of stretching the whole page.
    document.querySelectorAll('table').forEach(function (table) {
        if (table.parentElement && table.parentElement.classList.contains('table-wrap')) return;
        var wrap = document.createElement('div');
        wrap.className = 'table-wrap';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    });

    // ---- Dashboard stat numbers: count up from 0 to the real value on load
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('.stat-h-num').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-target'), 10) || 0;
        if (reduceMotion) { el.textContent = target; return; }
        var duration = 1000;
        var start    = performance.now();
        function tick(now) {
            var progress = Math.min((now - start) / duration, 1);
            // ease-out: fast at first, then slows as it reaches the target
            var eased = 1 - (1 - progress) * (1 - progress);
            el.textContent = Math.round(eased * target);
            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = target;   // land exactly on the final number
            }
        }
        requestAnimationFrame(tick);
    });

    // ---- Splash screen: redirect to the whitelisted destination after a pause.
    //      The destination is set server-side in a data attribute (never from
    //      user input), with a <meta refresh> fallback if JavaScript is off.
    var splash = document.querySelector('[data-splash-redirect]');
    if (splash) {
        var dest = splash.getAttribute('data-splash-redirect');
        setTimeout(function () { window.location.href = dest; }, 1600);
    }

    // ---- Navbar: the hamburger button opens/closes the menu on phones
    var navToggle = document.querySelector('.navbar-toggle');
    if (navToggle) {
        navToggle.addEventListener('click', function () {
            var nav = document.querySelector('.navbar');
            if (!nav) return;
            var open = nav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
})();

// ---- Login: show/hide the password and swap the eye / eye-off icons.
//      Global so the button's onclick="togglePassword(this)" can reach it.
function togglePassword(btn) {
    var input  = document.getElementById('password');
    var eye    = btn.querySelector('.icon-eye');
    var eyeOff = btn.querySelector('.icon-eye-off');
    var show   = input.type === 'password';   // are we about to show it?

    input.type = show ? 'text' : 'password';
    eye.style.display    = show ? 'none' : 'block';
    eyeOff.style.display = show ? 'block' : 'none';
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
}
