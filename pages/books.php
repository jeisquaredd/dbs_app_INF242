<?php


require_once('../classes/database.php');
session_start();
$con = new database();


$allbooks = $con->viewbooks();

if(isset($_POST['update_books'])){
   
}

if(isset($_POST['delete_books'])){
  $book_id = $_POST['book_id'];
  $book_title = $_POST['book_title'];
  try {
    $con->deletebooks($book_id);
    $_SESSION['success_message'] = $book_title . " is deleted successfully!";

    header('Location: books.php');
    exit();
  } catch (PDOException $e) {
    $error_message = "Cannot delete this book. It may have active loans or copies in use.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Books — Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php
$navbarMode = 'admin';
$activePage = 'books.php';
include 'navbar.php';

if(isset($_SESSION['success_message'])): ?>
<div class="container py-3">
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> <?php echo $_SESSION['success_message']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if(isset($error_message)): ?>
<div class="container py-3">
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> <?php echo $error_message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php endif; ?>

<main class="container py-4">
  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card p-4">
        <h5 class="mb-1">Add Book</h5>
        <p class="small-muted mb-3">Creates a row in <b>Books</b>.</p>

        <!-- Later in PHP: action="../php/books/create.php" method="POST" -->
        <form action="#" method="POST">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="book_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" name="book_publication_year" type="number" min="1500" max="2100" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Edition</label>
            <input class="form-control" name="book_edition" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher" placeholder="optional">
          </div>
          <button class="btn btn-primary w-100" type="submit">Save Book</button>
        </form>
      </div>

      <div class="card p-4 mt-3">
        <h6 class="mb-2">Add Copy</h6>
        <p class="small-muted mb-3">Creates a row in <b>BookCopy</b>.</p>
        <!-- Later in PHP: action="../php/copies/create.php" method="POST" -->
        <form action="#" method="POST">
          <div class="mb-3">
            <label class="form-label">Book</label>
            <select class="form-select" name="book_id" required>
              <option value="">Select book</option>
              <option value="1">Noli Me Tangere</option>
              <option value="2">El Filibusterismo</option>
              <option value="3">Mga Ibong Mandaragit</option>
              <option value="4">Smaller and Smaller Circles</option>
              <option value="5">Dekada ’70</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
              <option value="AVAILABLE">AVAILABLE</option>
              <option value="ON_LOAN">ON_LOAN</option>
              <option value="LOST">LOST</option>
              <option value="DAMAGED">DAMAGED</option>
              <option value="REPAIR">REPAIR</option>
            </select>
          </div>
          <button class="btn btn-outline-primary w-100" type="submit">Add Copy</button>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-end mb-3">
          <div>
            <h5 class="mb-1">Books List</h5>
         
            <div class="small-muted">Placeholder rows. Replace with PHP + MySQL output.</div>
          </div>
          <div class="d-flex gap-2">
            <input class="form-control" style="max-width: 260px;" placeholder="Search title / ISBN...">
            <button class="btn btn-outline-secondary">Search</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>ISBN</th>
                <th>Year</th>
                <th>Publisher</th>
                <th>Copies</th>
                <th>Available</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($allbooks as $book) {
              echo '<tr>';

            

              echo'<td>' . $book['book_id'] . '</td>';
              echo'<td>' . $book['book_title'] . '</td>';
              echo'<td>' . $book['book_isbn'] . '</td>';
              echo'<td>' . $book['book_publication_year'] . '</td>';
              echo'<td>' . $book['book_publisher'] . '</td>';
              echo'<td class="text-center">' . $book['Copies'] . '</td>';
              echo'<td class="text-center"><span class="badge text-bg-success">' . $book['Available_Copies'] . '</span></td>';
              echo'<td class="text-end">';
              echo'<div class="btn-group" role="group">';

              echo'<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editBookModal"

              data-book-id="' . $book['book_id'] . '"
              data-book-title="' . $book['book_title'] . '"
              data-book-isbn="' . $book['book_isbn'] . '"
              data-book-publication-year="' . $book['book_publication_year'] . '"
              data-book-publisher="' . $book['book_publisher'] . '"

              >Edit</button>';

              echo'<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBookModal" data-book-id="' . $book['book_id'] . '" 
              data-book-title="' . $book['book_title'] . '"
              
              >Delete</button>';
              
              echo'</div>';
              echo'</td>';
              echo'</tr>';
              
              }?>
            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
  
</div>
          </tbody>
          </table>
          
        </div>
   
        <hr class="my-4">

        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Author to Book</h6>
              <p class="small-muted mb-3">Creates a row in <b>BookAuthors</b>.</p>
              <!-- Later in PHP: action="../php/bookauthors/create.php" method="POST" -->
              <form action="#" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <option value="1">Noli Me Tangere</option>
                    <option value="2">El Filibusterismo</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="author_id" required>
                    <option value="">Select author</option>
                    <option value="1">Jose Rizal</option>
                    <option value="2">Amado Hernandez</option>
                    <option value="3">F. H. Batacan</option>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-primary w-100" type="submit">Assign</button>
                </div>
              </form>
              <div class="small-muted mt-2">Unique constraint prevents duplicate (book_id, author_id).</div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Genre to Book</h6>
              <p class="small-muted mb-3">Creates a row in <b>BookGenre</b>.</p>
              <!-- Later in PHP: action="../php/bookgenre/create.php" method="POST" -->
              <form action="#" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <option value="1">Noli Me Tangere</option>
                    <option value="2">El Filibusterismo</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="genre_id" required>
                    <option value="">Select genre</option>
                    <option value="1">Classic</option>
                    <option value="5">Philippine Literature</option>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-outline-primary w-100" type="submit">Assign</button>
                </div>
              </form>
              <div class="small-muted mt-2">Unique constraint prevents duplicate (genre_id, book_id).</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<!-- Edit Book Modal (UI only) -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Later in PHP: load existing values -->
        <form action="#" method="POST">
         
        

          <div class="mb-3">
            <label class="form-label">Book ID</label>
            <input class="form-control" name="book_id" id="edit_book_id" readonly>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="book_title" id="edit_book_title">
          </div>
          
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn" id="edit_book_isbn">
          </div>

          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" type="number" min="1500" max="2100" name="book_publication_year" id="edit_book_year">
          </div>

          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher" id="edit_book_publisher">
          </div>
          <button class="btn btn-primary w-100" type="submit" name="update_books">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Book Modal (Confirmation) -->
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="delete_book_title"></strong>?</p>
        <p class="text-danger small">This action cannot be undone.</p>
        <form action="#" method="POST">
          <input type="hidden" name="book_id" id="delete_book_id">
          <input type="hidden" name="book_title" id="delete_book_titles">
          <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger" name="delete_books">Delete Book</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const editBookModal = document.getElementById('editBookModal');

  editBookModal.addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  if (!btn) return;

  document.getElementById('edit_book_id').value = btn.getAttribute('data-book-id') || '';

  document.getElementById('edit_book_title').value = btn.getAttribute('data-book-title') || '';

  document.getElementById('edit_book_isbn').value = btn.getAttribute('data-book-isbn') || '';

  document.getElementById('edit_book_year').value = btn.getAttribute('data-book-publication-year') || '';

  document.getElementById('edit_book_publisher').value = btn.getAttribute('data-book-publisher') || '';

  });

  const deleteBookModal = document.getElementById('deleteBookModal');

  deleteBookModal.addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  if (!btn) return;

  document.getElementById('delete_book_id').value = btn.getAttribute('data-book-id') || '';
  document.getElementById('delete_book_titles').value = btn.getAttribute('data-book-title') || '';

  document.getElementById('delete_book_title').textContent = btn.getAttribute('data-book-title') || '';
  });
  
</script>

</body>
</html>