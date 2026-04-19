<?php
$pageTitle = $pageTitle ?? 'Carbon Footprint Tracker';
$user = $user ?? current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app-shell">
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php">
            <span class="brand-mark"></span>
            <span>
                <strong>Carbon Footprint Tracker</strong>
                <small>Daily impact monitor</small>
            </span>
        </a>
        <nav class="nav-links" aria-label="Main navigation">
            <?php if ($user): ?>
                <a class="<?= e(nav_active(['dashboard.php'])) ?>" href="dashboard.php">Dashboard</a>
                <a class="<?= e(nav_active(['log_activity.php'])) ?>" href="log_activity.php">Log Activity</a>
                <a class="<?= e(nav_active(['history.php'])) ?>" href="history.php">History</a>
                <a href="logout.php">Logout</a>
                <span class="user-chip"><?= e($user['full_name']) ?></span>
            <?php else: ?>
                <a class="<?= e(nav_active(['login.php'])) ?>" href="login.php">Login</a>
                <a class="button button-small" href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container page">
    <?php foreach (get_flashes() as $message): ?>
        <div class="alert alert-<?= e($message['type']) ?>">
            <?= e($message['message']) ?>
        </div>
    <?php endforeach; ?>
