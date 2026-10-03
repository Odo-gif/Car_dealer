<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <h4><?= htmlspecialchars(SITE_NAME) ?></h4>
                <p>Trusted quality used and new cars at honest prices. We help you find the right car and handle the paperwork so you don't have to.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cars.php">Browse Cars</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4>Get in Touch</h4>
                <ul>
                    <li>📞 <?= htmlspecialchars(SITE_PHONE) ?></li>
                    <li>✉️ <?= htmlspecialchars(SITE_EMAIL) ?></li>
                    <li>📍 <?= htmlspecialchars(SITE_ADDRESS) ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME) ?>. All rights reserved.
        </div>
    </div>
</footer>

<a href="https://wa.me/<?= htmlspecialchars(SITE_WHATSAPP) ?>?text=<?= urlencode('Hi, I would like to enquire about a car listed on your website.') ?>"
   target="_blank" rel="noopener"
   style="position:fixed;bottom:22px;right:22px;background:#25D366;color:#fff;width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 24px rgba(0,0,0,0.25);z-index:60;">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="#fff"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.78 14.12c-.24.68-1.19 1.25-1.95 1.41-.53.11-1.22.2-3.55-.76-2.98-1.23-4.9-4.24-5.05-4.44-.15-.2-1.21-1.61-1.21-3.07 0-1.46.76-2.17 1.03-2.47.27-.29.59-.36.79-.36.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.85 2.07.92 2.22.07.15.12.33.02.53-.1.2-.15.32-.3.5-.15.18-.31.4-.44.53-.15.15-.3.31-.13.6.17.29.76 1.25 1.63 2.03 1.12 1 2.06 1.31 2.35 1.46.29.15.46.13.63-.08.17-.2.72-.84.92-1.13.2-.29.39-.24.66-.14.27.1 1.72.81 2.02.96.29.15.49.22.56.34.07.13.07.75-.17 1.43z"></path></svg>
</a>

<script src="assets/js/script.js"></script>
</body>
</html>
