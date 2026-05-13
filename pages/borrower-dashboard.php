<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Borrower Dashboard — Library</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php
$navbarMode = 'borrower';
$activePage = 'borrower-dashboard.php';
include 'navbar.php';
?>

<main class="container py-4">
  <div class="row g-3">
    <div class="col-12 col-lg-5">
      <div class="card p-4">
        <h5 class="mb-1">My Profile</h5>
        <p class="small-muted mb-3">Example borrower: Juan Dela Cruz</p>

        <div class="mb-2"><b>Email:</b> juan.delacruz@samplemail.com</div>
        <div class="mb-2"><b>Mobile:</b> 0917 123 4567</div>
        <div class="mb-2"><b>Status:</b> <span class="badge text-bg-success">Active</span></div>

        <hr class="my-3">
        <div class="small-muted">
          Your borrower record is linked to your user account via <b>BorrowerUser</b>.
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-7">
      <div class="card p-4">
        <h5 class="mb-2">My Loans</h5>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Loan ID</th>
                <th>Status</th>
                <th>Loan Date</th>
                <th>Items</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1001</td>
                <td><span class="badge text-bg-success">CLOSED</span></td>
                <td>2025-10-03</td>
                <td>2</td>
              </tr>
              <tr>
                <td class="small-muted" colspan="4">Your current open loans will appear here.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <hr class="my-3">
        <a class="btn btn-outline-primary" href="catalog.php">Browse Catalog</a>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>