<?php $activePage = $activePage ?? ''; ?>
<aside class="sidebar">
    <div class="brand">
        <div class="icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h14M5 17a2 2 0 0 1-2-2v-2l2-5a2 2 0 0 1 2-1h6a2 2 0 0 1 2 1l2 5v2a2 2 0 0 1-2 2M5 17a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2M17 17a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2"></path><circle cx="7.5" cy="14.5" r=".6" fill="#fff"></circle><circle cx="16.5" cy="14.5" r=".6" fill="#fff"></circle></svg>
        </div>
        <span>AutoDeal</span>
    </div>

    <nav>
        <div class="group-label">Main</div>
        <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
            Dashboard
        </a>

        <div class="group-label">Listings</div>
        <a href="cars_list.php" class="<?= $activePage === 'cars' ? 'active' : '' ?>">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h14M5 17a2 2 0 0 1-2-2v-2l2-5a2 2 0 0 1 2-1h6a2 2 0 0 1 2 1l2 5v2a2 2 0 0 1-2 2"></path></svg>
            Manage Cars
        </a>
        <a href="cars_add.php" class="<?= $activePage === 'cars_add' ? 'active' : '' ?>">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Car
        </a>

        <div class="group-label">Leads</div>
        <a href="inquiries.php" class="<?= $activePage === 'inquiries' ? 'active' : '' ?>">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Inquiries
        </a>

        <div class="group-label">Site</div>
        <a href="../index.php" target="_blank">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
            View Public Site
        </a>
    </nav>

    <div class="user-box">
        <div class="avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
        <div>
            <div class="name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
            <div class="role"><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></div>
        </div>
    </div>
    <a href="logout.php" class="logout-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        Sign out
    </a>
</aside>
