<?php

require_once('../classes/database.php');
$con = new database();

$authorStatus = null;
$authorMessage = '';
$genreStatus = null;
$genreMessage = '';

if (isset($_POST['add_author'])) {
  $firstname = trim($_POST['author_firstname'] ?? '');
  $lastname = trim($_POST['author_lastname'] ?? '');
  $birthYearInput = trim($_POST['author_birth_year'] ?? '');
  $nationalityInput = trim($_POST['author_nationality'] ?? '');

  $birthYear = $birthYearInput === '' ? null : (int) $birthYearInput;
  $nationality = $nationalityInput === '' ? null : $nationalityInput;

  if ($firstname === '' || $lastname === '') {
    $authorStatus = 'error';
    $authorMessage = 'Author first name and last name are required.';
  } else {
    try {
      $con->insertAuthor($firstname, $lastname, $birthYear, $nationality);
      $authorStatus = 'success';
      $authorMessage = 'Author added successfully.';
    } catch (Exception $e) {
      $authorStatus = 'error';
      $authorMessage = 'Error adding author: ' . $e->getMessage();
    }
  }
}

if (isset($_POST['add_genre'])) {
  $genreName = trim($_POST['genre_name'] ?? '');

  if ($genreName === '') {
    $genreStatus = 'error';
    $genreMessage = 'Genre name is required.';
  } else {
    try {
      $con->insertGenre($genreName);
      $genreStatus = 'success';
      $genreMessage = 'Genre added successfully.';
    } catch (Exception $e) {
      $genreStatus = 'error';
      $genreMessage = 'Error adding genre. '.$genreName.' already exist.';
    }
  }
}

$allAuthors = $con->viewauthors();
$allGenres = $con->viewgenres();

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Authors and Genres - Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php
$navbarMode = 'admin';
$activePage = 'authors-genres.php';
include 'navbar.php';
?>

<main class="container py-4">
  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="card p-4 h-100">
        <h5 class="mb-1">Add Author</h5>
        <p class="small-muted mb-3">Creates a row in <b>Authors</b>.</p>

        <?php if ($authorStatus === 'success') { ?>
          <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($authorMessage); ?></div>
        <?php } elseif ($authorStatus === 'error') { ?>
          <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($authorMessage); ?></div>
        <?php } ?>

        <form action="" method="POST" class="row g-2">
          <div class="col-12 col-md-6">
            <label class="form-label">First Name</label>
            <input class="form-control" name="author_firstname" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Last Name</label>
            <input class="form-control" name="author_lastname" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Birth Year</label>
            <input class="form-control" name="author_birth_year" type="number" min="1" max="2100" placeholder="optional">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Nationality</label>
            <input class="form-control" name="author_nationality" placeholder="optional">
          </div>
          <div class="col-12">
            <button name="add_author" class="btn btn-primary w-100" type="submit">Save Author</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card p-4 h-100">
        <h5 class="mb-1">Add Genre</h5>
        <p class="small-muted mb-3">Creates a row in <b>Genres</b>.</p>

        <?php if ($genreStatus === 'success') { ?>
          <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($genreMessage); ?></div>
        <?php } elseif ($genreStatus === 'error') { ?>
          <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($genreMessage); ?></div>
        <?php } ?>

        <form action="" method="POST" class="row g-2">
          <div class="col-12">
            <label class="form-label">Genre Name</label>
            <input class="form-control" name="genre_name" required>
          </div>
          <div class="col-12">
            <button name="add_genre" class="btn btn-outline-primary w-100" type="submit">Save Genre</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Authors List</h5>
          <span class="small-muted"><?php echo count($allAuthors); ?> total</span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Author ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birth Year</th>
                <th>Nationality</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($allAuthors)) { ?>
                <tr>
                  <td colspan="5" class="text-center small-muted">No authors yet.</td>
                </tr>
              <?php } else {
                foreach ($allAuthors as $author) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($author['author_id']); ?></td>
                  <td><?php echo htmlspecialchars($author['author_firstname']); ?></td>
                  <td><?php echo htmlspecialchars($author['author_lastname']); ?></td>
                  <td><?php echo htmlspecialchars($author['author_birth_year'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($author['author_nationality'] ?? ''); ?></td>
                </tr>
              <?php }
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Genres List</h5>
          <span class="small-muted"><?php echo count($allGenres); ?> total</span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Genre ID</th>
                <th>Genre Name</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($allGenres)) { ?>
                <tr>
                  <td colspan="2" class="text-center small-muted">No genres yet.</td>
                </tr>
              <?php } else {
                foreach ($allGenres as $genre) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($genre['genre_id']); ?></td>
                  <td><?php echo htmlspecialchars($genre['genre_name']); ?></td>
                </tr>
              <?php }
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
