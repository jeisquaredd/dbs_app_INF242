<?php

require_once('../classes/database.php');

$con = new database();

$borrowers = [];
$borrowerLoadError = null;

try {
  $borrowers = $con->viewusers();
} catch (Exception $e) {
  $borrowerLoadError = 'Error loading borrowers: ' . $e->getMessage();
}

$checkoutStatus = null;
$checkoutMessage = '';

$borrower_id = '';
$processed_by_user_id = '1';
$copy_ids_input = '';
$due_date = '';
$condition_out = 'GOOD';

if (isset($_POST['create_loan'])) {
  $borrower_id = trim($_POST['borrower_id'] ?? '');
  $processed_by_user_id = trim($_POST['processed_by_user_id'] ?? '');
  $copy_ids_input = trim($_POST['copy_ids'] ?? '');
  $due_date = trim($_POST['li_duedate'] ?? '');
  $condition_out = trim($_POST['condition_out'] ?? 'GOOD');

  $errors = [];

  if (!ctype_digit($borrower_id)) {
    $errors[] = 'Borrower is required.';
  }

  if (!ctype_digit($processed_by_user_id)) {
    $errors[] = 'Processed by user ID is required.';
  }

  if ($due_date === '') {
    $errors[] = 'Due date is required.';
  }

  $copy_ids = preg_split('/\s*,\s*/', $copy_ids_input, -1, PREG_SPLIT_NO_EMPTY);
  $copy_ids = array_values(array_unique(array_filter(array_map('intval', $copy_ids), function ($id) {
    return $id > 0;
  })));

  if (empty($copy_ids)) {
    $errors[] = 'At least one valid copy ID is required.';
  }

  if (!empty($errors)) {
    $checkoutStatus = 'error';
    $checkoutMessage = implode(' ', $errors);
  } else {
    try {
      $loan_id = $con->createLoanWithItems($borrower_id, $processed_by_user_id, $copy_ids, $due_date, $condition_out);
      $checkoutStatus = 'success';
      $checkoutMessage = 'Loan created successfully. Loan ID: ' . $loan_id . '.';
      $copy_ids_input = '';
    } catch (Exception $e) {
      $checkoutStatus = 'error';
      $checkoutMessage = 'Error creating loan: ' . $e->getMessage();
    }
  }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout — Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.html">Library Admin</a>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary" href="admin-dashboard.html">Back</a>
      <a class="btn btn-sm btn-outline-secondary" href="login.html">Logout</a>
    </div>
  </div>
</nav>

<main class="container py-4">
  <?php if ($borrowerLoadError): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?php echo htmlspecialchars($borrowerLoadError); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($checkoutStatus === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($checkoutMessage); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($checkoutStatus === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($checkoutMessage); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="card p-4">
        <h5 class="mb-1">Process Checkout</h5>
        <p class="small-muted mb-4">Create a Loan + LoanItems. Processor is required.</p>

        <form action="" method="POST">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Borrower</label>
              <select class="form-select" name="borrower_id" required>
                <option value="">Select borrower</option>
                <?php foreach ($borrowers as $borrower): ?>
                  <option value="<?php echo htmlspecialchars($borrower['borrower_id']); ?>" <?php echo ($borrower_id == $borrower['borrower_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($borrower['borrower_firstname'] . ' ' . $borrower['borrower_lastname']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Processed By (User ID)</label>
              <input class="form-control" name="processed_by_user_id" type="number" value="<?php echo htmlspecialchars($processed_by_user_id); ?>" required>
              <div class="small-muted mt-1">Should be the logged-in ADMIN user_id.</div>
            </div>

            <div class="col-12">
              <label class="form-label">Copy IDs to Borrow (comma-separated)</label>
              <input class="form-control" name="copy_ids" placeholder="e.g., 102, 401" value="<?php echo htmlspecialchars($copy_ids_input); ?>" required>
              <div class="small-muted mt-1">Copies must be AVAILABLE and not already on an open loan.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Due Date</label>
              <input class="form-control" name="li_duedate" type="date" value="<?php echo htmlspecialchars($due_date); ?>" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Condition Out</label>
              <select class="form-select" name="condition_out" required>
                <option value="GOOD" <?php echo ($condition_out === 'GOOD') ? 'selected' : ''; ?>>GOOD</option>
                <option value="DAMAGED" <?php echo ($condition_out === 'DAMAGED') ? 'selected' : ''; ?>>DAMAGED</option>
              </select>
            </div>
          </div>

          <hr class="my-4">
          <button class="btn btn-primary" type="submit" name="create_loan">Create Loan</button>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card p-4">
        <h6 class="mb-2">Checkout Rules Reminder</h6>
        <ul class="small-muted mb-0">
          <li>Loan must have a borrower_id.</li>
          <li>Loan must have processed_by_user_id (ADMIN).</li>
          <li>Each copy can only be actively on loan once.</li>
          <li>Loan requires at least one LoanItem.</li>
        </ul>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
