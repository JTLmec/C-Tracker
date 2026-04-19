<?php
require_once __DIR__ . '/includes/functions.php';

if (current_user()) {
    redirect('dashboard.php');
}

$errors = [];
$fullName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        try {
            $stmt = db()->prepare(
                'INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([
                $fullName,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
            ]);

            $_SESSION['user_id'] = (int) db()->lastInsertId();
            flash('success', 'Account created successfully.');
            redirect('dashboard.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'That email is already registered.';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
?>

<section class="auth-layout">
    <div class="auth-copy">
        <p class="eyebrow">Start tracking</p>
        <h1>Create your account</h1>
        <p class="lead">
            Record travel, electricity, food, waste, and water activities to estimate your daily carbon footprint.
        </p>
        <div class="auth-notes">
            <div>
                <span>Setup time</span>
                <strong>Less than a minute</strong>
            </div>
            <div>
                <span>Goal</span>
                <strong>Spot patterns and improve habits</strong>
            </div>
        </div>
    </div>

    <form class="panel form auth-panel" method="post" action="register.php">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">New account</p>
                <h2>Register</h2>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <label for="full_name">Full name</label>
        <input id="full_name" name="full_name" type="text" value="<?= e($fullName) ?>" required>

        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" minlength="6" required>

        <label for="confirm_password">Confirm password</label>
        <input id="confirm_password" name="confirm_password" type="password" minlength="6" required>

        <button class="button" type="submit">Create Account</button>
        <p class="form-note">Already registered? <a href="login.php">Log in</a>.</p>
    </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
