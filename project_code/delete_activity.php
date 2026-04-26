<?php
require_once __DIR__ . '/includes/functions.php';

$user = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('history.php');
}

require_valid_csrf();

$activityId = (int) ($_POST['activity_id'] ?? 0);

if ($activityId > 0) {
    $stmt = tracker_db()->prepare('DELETE FROM activities WHERE id = ? AND user_id = ?');
    $stmt->execute([$activityId, $user['id']]);
    flash('success', 'Activity deleted.');
}

redirect('history.php');
