<?php

$navbarMode = $navbarMode ?? 'admin';
$activePage = $activePage ?? '';
$backHref = $backHref ?? 'admin-dashboard.php';
$backLabel = $backLabel ?? 'Back';

function navbarIsActive(string $href, string $activePage): string
{
  return $href === $activePage ? ' active' : '';
}

?>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
<?php if ($navbarMode === 'admin') { ?>
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navAdmin" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('admin-dashboard.php', $activePage); ?>" href="admin-dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('books.php', $activePage); ?>" href="books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('authors-genres.php', $activePage); ?>" href="authors-genres.php">Authors &amp; Genres</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('borrowers.php', $activePage); ?>" href="borrowers.php">Borrowers</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('checkout.php', $activePage); ?>" href="checkout.php">Checkout</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('return.php', $activePage); ?>" href="return.php">Return</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('catalog.php', $activePage); ?>" href="catalog.php">Catalog</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <span class="badge badge-soft">Role: ADMIN</span>
        <a class="btn btn-sm btn-outline-secondary" href="login.php">Logout</a>
      </div>
    </div>
<?php } elseif ($navbarMode === 'borrower') { ?>
    <a class="navbar-brand fw-semibold" href="borrower-dashboard.php">Library</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBorrower">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navBorrower" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('borrower-dashboard.php', $activePage); ?>" href="borrower-dashboard.php">My Dashboard</a></li>
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('catalog.php', $activePage); ?>" href="catalog.php">Catalog</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <span class="badge badge-soft">Role: BORROWER</span>
        <a class="btn btn-sm btn-outline-secondary" href="login.php">Logout</a>
      </div>
    </div>
<?php } elseif ($navbarMode === 'catalog') { ?>
    <a class="navbar-brand fw-semibold" href="catalog.php">Library Catalog</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navCatalog">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navCatalog" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link<?php echo navbarIsActive('catalog.php', $activePage); ?>" href="catalog.php">Catalog</a></li>
        <li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Admin</a></li>
        <li class="nav-item"><a class="nav-link" href="borrower-dashboard.php">Borrower</a></li>
      </ul>
      <a class="btn btn-sm btn-outline-secondary" href="login.php">Login</a>
    </div>
<?php } elseif ($navbarMode === 'admin-minimal') { ?>
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($backHref); ?>"><?php echo htmlspecialchars($backLabel); ?></a>
      <a class="btn btn-sm btn-outline-secondary" href="login.php">Logout</a>
    </div>
<?php } elseif ($navbarMode === 'guest-minimal') { ?>
    <a class="navbar-brand fw-semibold" href="login.php">Library</a>
    <div class="ms-auto">
      <a class="btn btn-sm btn-outline-secondary" href="login.php">Back to Login</a>
    </div>
<?php } elseif ($navbarMode === 'login') { ?>
    <a class="navbar-brand fw-semibold" href="login.php">Library System</a>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary" href="catalog.php">Catalog</a>
      <a class="btn btn-sm btn-outline-primary" href="register-borrower.php">Register</a>
    </div>
<?php } ?>
  </div>
</nav>
