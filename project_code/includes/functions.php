<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '');
}

function nav_active(array $pages): string
{
    return in_array(current_page(), $pages, true) ? 'is-active' : '';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function require_valid_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        flash('error', 'Your session expired. Please try again.');
        redirect('index.php');
    }
}

function is_valid_date(string $date): bool
{
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    $errors = DateTime::getLastErrors();

    return $parsed !== false
        && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
        && $parsed->format('Y-m-d') === $date;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, full_name, email, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function require_login(): array
{
    $user = current_user();

    if ($user === null) {
        flash('error', 'Please log in to continue.');
        redirect('login.php');
    }

    return $user;
}

function get_activity_types(): array
{
    $stmt = db()->query(
        'SELECT id, category, name, unit, emission_factor, recommendation
         FROM activity_types
         WHERE is_active = 1
         ORDER BY category, name'
    );

    return $stmt->fetchAll();
}

function get_activity_type_groups(): array
{
    $groups = [];

    foreach (get_activity_types() as $type) {
        $groups[$type['category']][] = $type;
    }

    return $groups;
}

function get_dashboard_totals(int $userId): array
{
    $stmt = db()->prepare(
        "SELECT
            COALESCE(SUM(carbon_kg), 0) AS total,
            COALESCE(SUM(CASE WHEN activity_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN carbon_kg ELSE 0 END), 0) AS week_total,
            COALESCE(SUM(CASE WHEN activity_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN carbon_kg ELSE 0 END), 0) AS month_total,
            COUNT(*) AS activity_count
         FROM activities
         WHERE user_id = ?"
    );
    $stmt->execute([$userId]);

    return $stmt->fetch() ?: [
        'total' => 0,
        'week_total' => 0,
        'month_total' => 0,
        'activity_count' => 0,
    ];
}

function get_average_daily_footprint(int $userId): float
{
    $stmt = db()->prepare(
        'SELECT
            COALESCE(SUM(carbon_kg), 0) AS total,
            COUNT(DISTINCT activity_date) AS active_days
         FROM activities
         WHERE user_id = ?'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    if (!$row || (int) $row['active_days'] === 0) {
        return 0.0;
    }

    return (float) $row['total'] / (int) $row['active_days'];
}

function get_category_breakdown(int $userId): array
{
    $stmt = db()->prepare(
        'SELECT at.category, COALESCE(SUM(a.carbon_kg), 0) AS total
         FROM activities a
         INNER JOIN activity_types at ON at.id = a.activity_type_id
         WHERE a.user_id = ?
         GROUP BY at.category
         ORDER BY total DESC'
    );
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

function get_recent_activities(int $userId, int $limit = 8): array
{
    $stmt = db()->prepare(
        'SELECT a.id, a.activity_date, a.quantity, a.carbon_kg, a.notes,
                at.category, at.name, at.unit
         FROM activities a
         INNER JOIN activity_types at ON at.id = a.activity_type_id
         WHERE a.user_id = ?
         ORDER BY a.activity_date DESC, a.created_at DESC
         LIMIT ' . $limit
    );
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

function get_all_activities(int $userId): array
{
    $stmt = db()->prepare(
        'SELECT a.id, a.activity_date, a.quantity, a.carbon_kg, a.notes,
                at.category, at.name, at.unit
         FROM activities a
         INNER JOIN activity_types at ON at.id = a.activity_type_id
         WHERE a.user_id = ?
         ORDER BY a.activity_date DESC, a.created_at DESC'
    );
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

function get_top_recommendation(int $userId): ?array
{
    $stmt = db()->prepare(
        'SELECT at.category, at.recommendation, SUM(a.carbon_kg) AS total
         FROM activities a
         INNER JOIN activity_types at ON at.id = a.activity_type_id
         WHERE a.user_id = ?
         GROUP BY at.category, at.recommendation
         ORDER BY total DESC
         LIMIT 1'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function format_kg(float|int|string $value): string
{
    return number_format((float) $value, 2) . ' kg CO2e';
}

function format_short_date(string $value): string
{
    $date = DateTime::createFromFormat('Y-m-d', $value);

    if ($date === false) {
        return $value;
    }

    return $date->format('M d, Y');
}
