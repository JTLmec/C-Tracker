<?php
require_once __DIR__ . '/includes/functions.php';

$user = require_login();
$activities = get_all_activities((int) $user['id']);

$pageTitle = 'Activity History';
require __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">Saved records</p>
        <h1>Activity history</h1>
        <p class="lead compact-lead">
            Review your logged records and remove entries that should no longer count toward your totals.
        </p>
    </div>
    <a class="button" href="log_activity.php">Add Activity</a>
</section>

<section class="panel">
    <?php if (!$activities): ?>
        <p class="muted">No activities have been logged yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Activity</th>
                        <th>Quantity</th>
                        <th>Emissions</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?= e(format_short_date($activity['activity_date'])) ?></td>
                            <td><span class="category-pill"><?= e(ucfirst($activity['category'])) ?></span></td>
                            <td><strong><?= e($activity['name']) ?></strong></td>
                            <td><?= e($activity['quantity']) ?> <?= e($activity['unit']) ?></td>
                            <td><?= e(format_kg($activity['carbon_kg'])) ?></td>
                            <td><?= e($activity['notes'] ?: '-') ?></td>
                            <td>
                                <form method="post" action="delete_activity.php" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="activity_id" value="<?= e($activity['id']) ?>">
                                    <button class="link-button" type="submit" onclick="return confirm('Delete this activity?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
