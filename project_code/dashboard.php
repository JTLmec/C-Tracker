<?php
require_once __DIR__ . '/includes/functions.php';

$user = require_login();
$totals = get_dashboard_totals((int) $user['id']);
$breakdown = get_category_breakdown((int) $user['id']);
$recentActivities = get_recent_activities((int) $user['id']);
$recommendation = get_top_recommendation((int) $user['id']);
$dailyAverage = get_average_daily_footprint((int) $user['id']);

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">Welcome, <?= e($user['full_name']) ?></p>
        <h1>Your carbon summary</h1>
        <p class="lead compact-lead">
            Review your latest footprint, compare categories, and adjust the habits that matter most.
        </p>
    </div>
    <a class="button" href="log_activity.php">Log Activity</a>
</section>

<section class="hero-band">
    <div class="hero-copy">
        <span class="hero-label">Current snapshot</span>
        <strong><?= e(format_kg($totals['month_total'])) ?></strong>
        <p>This month’s recorded emissions based on your logged activities.</p>
    </div>
    <div class="hero-meta">
        <div>
            <span>Highest focus area</span>
            <strong><?= e($recommendation ? ucfirst($recommendation['category']) : 'No data yet') ?></strong>
        </div>
        <div>
            <span>Daily average</span>
            <strong><?= e(format_kg($dailyAverage)) ?></strong>
        </div>
    </div>
</section>

<section class="stats-grid" aria-label="Carbon footprint summary">
    <article class="stat-card">
        <span>Total Footprint</span>
        <strong><?= e(format_kg($totals['total'])) ?></strong>
        <small>All recorded activity</small>
    </article>
    <article class="stat-card">
        <span>Last 7 Days</span>
        <strong><?= e(format_kg($totals['week_total'])) ?></strong>
        <small>Recent short-term trend</small>
    </article>
    <article class="stat-card">
        <span>This Month</span>
        <strong><?= e(format_kg($totals['month_total'])) ?></strong>
        <small>Current monthly total</small>
    </article>
    <article class="stat-card">
        <span>Activities Logged</span>
        <strong><?= e($totals['activity_count']) ?></strong>
        <small>Saved entries</small>
    </article>
    <article class="stat-card">
        <span>Daily Average</span>
        <strong><?= e(format_kg($dailyAverage)) ?></strong>
        <small>Per active logging day</small>
    </article>
</section>

<section class="grid-two">
    <article class="panel">
        <div class="section-title">
            <h2>Breakdown by category</h2>
        </div>

        <?php if (!$breakdown): ?>
            <p class="muted">No activity data yet.</p>
        <?php else: ?>
            <div class="bar-list">
                <?php
                $highest = max(array_map(static fn ($row) => (float) $row['total'], $breakdown));
                foreach ($breakdown as $row):
                    $width = $highest > 0 ? ((float) $row['total'] / $highest) * 100 : 0;
                ?>
                    <div class="bar-item">
                        <div class="bar-label">
                            <span><?= e(ucfirst($row['category'])) ?></span>
                            <strong><?= e(format_kg($row['total'])) ?></strong>
                        </div>
                        <div class="bar-track">
                            <span style="width: <?= e(number_format($width, 2)) ?>%"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <article class="panel recommendation">
        <div class="section-title">
            <h2>Recommendation</h2>
        </div>

        <?php if ($recommendation): ?>
            <p class="category-pill"><?= e(ucfirst($recommendation['category'])) ?> focus</p>
            <p><?= e($recommendation['recommendation']) ?></p>
            <p class="muted">
                This is based on the activity category with the highest recorded emissions.
            </p>
        <?php else: ?>
            <p>Log your first activity to receive a recommendation.</p>
        <?php endif; ?>
    </article>
</section>

<section class="panel">
    <div class="section-title with-action">
        <h2>Recent activities</h2>
        <a href="history.php">View all</a>
    </div>

    <?php if (!$recentActivities): ?>
        <p class="muted">No activities recorded yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activity</th>
                        <th>Quantity</th>
                        <th>Emissions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentActivities as $activity): ?>
                        <tr>
                            <td><?= e(format_short_date($activity['activity_date'])) ?></td>
                            <td>
                                <strong><?= e($activity['name']) ?></strong>
                                <div class="table-subtle"><?= e(ucfirst($activity['category'])) ?></div>
                            </td>
                            <td><?= e($activity['quantity']) ?> <?= e($activity['unit']) ?></td>
                            <td><?= e(format_kg($activity['carbon_kg'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
