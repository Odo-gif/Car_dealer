<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/site_config.php';

$pageTitle = 'Contact Us';
$activeNav = 'contact';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/site_header.php';
?>

<section class="section" style="padding-top:36px;">
    <div class="container">
        <div class="section-title">
            <span class="eyebrow">Get In Touch</span>
            <h2>Contact Us</h2>
            <p>Have a question about a car, financing, or trade-in? Reach out — we're happy to help.</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" style="max-width:600px; margin:0 auto 24px;"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <div class="details-grid" style="max-width:1000px; margin:0 auto;">
            <div class="sticky-card" style="position:static;">
                <h3 style="margin-top:0;">Send us a message</h3>
                <form method="POST" action="inquiry_submit.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email (optional)</label>
                        <input type="email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" placeholder="How can we help?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-orange btn-block">Send Message</button>
                </form>
            </div>

            <div>
                <div class="feature-box" style="text-align:left; margin-bottom:16px;">
                    <h4>📞 Call Us</h4>
                    <p><?= htmlspecialchars(SITE_PHONE) ?></p>
                </div>
                <div class="feature-box" style="text-align:left; margin-bottom:16px;">
                    <h4>✉️ Email Us</h4>
                    <p><?= htmlspecialchars(SITE_EMAIL) ?></p>
                </div>
                <div class="feature-box" style="text-align:left; margin-bottom:16px;">
                    <h4>📍 Visit Us</h4>
                    <p><?= htmlspecialchars(SITE_ADDRESS) ?></p>
                </div>
                <a href="https://wa.me/<?= htmlspecialchars(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-block">💬 Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/site_footer.php'; ?>
