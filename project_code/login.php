<?php
require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    redirect('dashboard.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $userRow = $stmt->fetch();

    if ($userRow && password_verify($password, $userRow['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $userRow['id'];
        flash('success', 'Welcome back.');
        redirect('dashboard.php');
    }

    $error = 'Invalid email or password.';
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>

<section class="auth-layout">
    <div class="auth-copy">
        <p class="eyebrow">Carbon Footprint Tracker</p>
        <h1>Measure daily impact with less friction</h1>
        <p class="lead">
            Log common activities and see simple recommendations for reducing emissions over time.
        </p>
        <div class="auth-notes">
            <div>
                <span>Daily tracking</span>
                <strong>Travel, power, food, waste, water</strong>
            </div>
            <div>
                <span>Outputs</span>
                <strong>Footprint totals, history, suggestions</strong>
            </div>
        </div>
    </div>

    <form class="panel form auth-panel" method="post" action="login.php">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Welcome back</p>
                <h2>Log in</h2>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button class="button" type="submit">Log In</button>
        <p class="form-note">New user? <a href="register.php">Create an account</a>.</p>
    </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
