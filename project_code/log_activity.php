<?php
require_once __DIR__ . '/includes/functions.php';

$user = require_login();
$activityTypeGroups = get_activity_type_groups();
$activityTypes = get_activity_types();
$errors = [];
$activityDate = date('Y-m-d');
$activityTypeId = '';
$quantity = '';
$notes = '';

$activityTypeByName = [];
foreach ($activityTypes as $type) {
    $activityTypeByName[$type['name']] = $type;
}

$typicalPresets = [
    ['name' => 'Car travel', 'quantity' => 20],
    ['name' => 'Electric car travel', 'quantity' => 20],
    ['name' => 'Jeepney / shared minibus', 'quantity' => 20],
    ['name' => 'Train / rail', 'quantity' => 20],
    ['name' => 'Electricity use', 'quantity' => 8],
    ['name' => 'LPG / cooking gas use', 'quantity' => 0.3],
    ['name' => 'Meat-based meal', 'quantity' => 1],
    ['name' => 'Plant-based meal', 'quantity' => 1],
    ['name' => 'Beef', 'quantity' => 0.15],
    ['name' => 'Chicken', 'quantity' => 0.15],
    ['name' => 'Mixed household waste', 'quantity' => 1],
    ['name' => 'Food waste (landfilled)', 'quantity' => 0.5],
    ['name' => 'Hot water use (heated electrically)', 'quantity' => 30],
];

$typicalValues = [];
foreach ($typicalPresets as $preset) {
    $type = $activityTypeByName[$preset['name']] ?? null;

    if (!$type) {
        continue;
    }

    $quantityValue = (float) $preset['quantity'];
    $factorValue = (float) $type['emission_factor'];

    $typicalValues[] = [
        'category' => $type['category'],
        'name' => $type['name'],
        'quantity' => $quantityValue,
        'unit' => $type['unit'],
        'carbon_kg' => round($quantityValue * $factorValue, 2),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $activityDate = trim((string) ($_POST['activity_date'] ?? ''));
    $activityTypeId = (int) ($_POST['activity_type_id'] ?? 0);
    $quantity = trim((string) ($_POST['quantity'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));

    if ($activityDate === '' || !is_valid_date($activityDate)) {
        $errors[] = 'Choose a valid activity date.';
    } elseif ($activityDate > date('Y-m-d')) {
        $errors[] = 'Activity date cannot be in the future.';
    }

    if ($activityTypeId <= 0) {
        $errors[] = 'Choose an activity type.';
    }

    if (!is_numeric($quantity) || (float) $quantity <= 0) {
        $errors[] = 'Quantity must be greater than zero.';
    }

    $stmt = tracker_db()->prepare(
        'SELECT id, emission_factor FROM activity_types WHERE id = ? AND is_active = 1'
    );
    $stmt->execute([$activityTypeId]);
    $type = $stmt->fetch();

    if (!$type) {
        $errors[] = 'Selected activity type is not available.';
    }

    if (!$errors) {
        $quantityValue = round((float) $quantity, 2);
        $emissionFactor = (float) $type['emission_factor'];
        $carbonKg = round($quantityValue * $emissionFactor, 2);

        $stmt = tracker_db()->prepare(
            'INSERT INTO activities
                (user_id, activity_type_id, activity_date, quantity, emission_factor, carbon_kg, notes)
             VALUES
                (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $user['id'],
            $activityTypeId,
            $activityDate,
            $quantityValue,
            $emissionFactor,
            $carbonKg,
            $notes !== '' ? $notes : null,
        ]);

        flash('success', 'Activity logged successfully.');
        redirect('dashboard.php');
    }
}

$pageTitle = 'Log Activity';
require __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <div>
        <p class="eyebrow">Daily activity</p>
        <h1>Log carbon activity</h1>
        <p class="lead compact-lead">
            Add one activity at a time and preview the estimated emissions before saving the record.
        </p>
    </div>
</section>

<section class="grid-two">
    <form class="panel form" method="post" action="log_activity.php">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">New entry</p>
                <h2>Activity details</h2>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <label for="activity_date">Date</label>
        <input id="activity_date" name="activity_date" type="date" value="<?= e($activityDate) ?>" max="<?= e(date('Y-m-d')) ?>" required>

        <label for="activity_type_id">Activity type</label>
        <select id="activity_type_id" name="activity_type_id" required>
            <option value="">Select activity</option>
            <?php foreach ($activityTypeGroups as $category => $types): ?>
                <optgroup label="<?= e(ucfirst($category)) ?>">
                    <?php foreach ($types as $type): ?>
                        <option
                            value="<?= e($type['id']) ?>"
                            data-factor="<?= e($type['emission_factor']) ?>"
                            data-unit="<?= e($type['unit']) ?>"
                            <?= (string) $activityTypeId === (string) $type['id'] ? 'selected' : '' ?>
                        >
                            <?= e($type['name'] . ' (' . $type['unit'] . ', factor ' . $type['emission_factor'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Quantity</label>
        <input id="quantity" name="quantity" type="number" step="0.01" min="0.01" value="<?= e($quantity) ?>" required>

        <div class="calculation-preview" id="calculation-preview">
            Choose an activity and enter quantity to preview emissions.
        </div>

        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="4" placeholder="Optional"><?= e($notes) ?></textarea>

        <button class="button" type="submit">Save Activity</button>
    </form>

    <aside class="panel help-panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Calculation</p>
                <h2>How it works</h2>
            </div>
        </div>
        <p>
            Carbon footprint is estimated by multiplying the activity quantity by the emission factor stored in the database.
        </p>
        <div class="formula">quantity x emission factor = kg CO2e</div>
        <p class="muted">
            These values are simplified estimates for classroom use and are intended for comparison between habits.
        </p>
        <div class="help-list">
            <div>
                <span>Travel</span>
                <strong>Distance-based estimate</strong>
            </div>
            <div>
                <span>Electricity</span>
                <strong>kWh multiplied by factor</strong>
            </div>
            <div>
                <span>Food and waste</span>
                <strong>Quick habit comparison</strong>
            </div>
        </div>

        <?php if ($typicalValues): ?>
            <div class="typical-values">
                <h3>Typical values (quick guide)</h3>
                <p class="muted">
                    These sample quantities can help users decide what to enter for each activity.
                </p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Typical quantity</th>
                                <th>Est. CO2e</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($typicalValues as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($item['name']) ?></strong>
                                        <div class="table-subtle"><?= e(ucfirst($item['category'])) ?></div>
                                    </td>
                                    <td><?= e(rtrim(rtrim(number_format($item['quantity'], 2, '.', ''), '0'), '.')) ?> <?= e($item['unit']) ?></td>
                                    <td><?= e(format_kg($item['carbon_kg'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </aside>
</section>

<script>
const typeSelect = document.querySelector('#activity_type_id');
const quantityInput = document.querySelector('#quantity');
const preview = document.querySelector('#calculation-preview');

function updatePreview() {
    const selected = typeSelect.options[typeSelect.selectedIndex];
    const factor = Number(selected?.dataset.factor || 0);
    const unit = selected?.dataset.unit || 'unit';
    const quantity = Number(quantityInput.value || 0);

    if (!factor || !quantity) {
        preview.textContent = 'Choose an activity and enter quantity to preview emissions.';
        return;
    }

    const total = quantity * factor;
    preview.textContent = `${quantity.toFixed(2)} ${unit} x ${factor.toFixed(3)} = ${total.toFixed(2)} kg CO2e`;
}

typeSelect.addEventListener('change', updatePreview);
quantityInput.addEventListener('input', updatePreview);
updatePreview();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
