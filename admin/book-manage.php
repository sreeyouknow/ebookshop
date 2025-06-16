<?php
include '../admin/include/header.php';
require '../Classes/edit-controller.php';
require '../Classes/delete-controller.php';

$delete = new delete($conn);
$edit = new edit($conn);

$edit_mode = false;
$edit_data = [
    'id' => '',
    'title' => '',
    'author' => '',
    'description' => '',
    'price' => '',
    'uploaded_by' => '',
];

// Fetch users for dropdown
$users = $conn->query("SELECT id, name FROM users");

// Delete Book
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // 1. Delete from cart
    $delete_cart = $delete->deleteCard($id);
    // 2. Delete from purchases
    $delete_Purchases = $delete->deletePurchases($id);
    // 3. Finally, delete from books
    $delete_book = $delete->deleteBook($id);

    echo "<script>alert('Book deleted successfully.'); 
    window.location.href='book-manage.php';</script>";
    exit;
}

$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

// Get total number of books for pagination
$count_result = $pagesearch->countBooks($search);
$total_books = $count_result['total'];
$total_pages = ceil($total_books / $limit); 


$books = $pagesearch->getBooks($search, $start, $limit);

        
// Edit Book - Fetch data
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result= $user->Books($edit_id);
    if ($result->num_rows === 1) {
        $edit_mode = true;
        $edit_data = $result->fetch_assoc();
    }
}

// Handle Upload or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $price = intval($_POST['price']);
    $uploaded_by = intval($_POST['uploaded_by']);
    $uploaded_at = date("Y-m-d H:i:s");

    if (!empty($_POST['id'])) {
        // Update
        $id = intval($_POST['id']);
        $editBooks = $edit->editBooks($title, $author, $description, $price, $uploaded_by, $id);
        echo "<script>alert('Book updated successfully.'); window.location.href='book-manage.php';</script>";
        exit;
    } else {
        // Insert
        $insertBook = $user->insertBooks($id, $title, $author, $description, $price, $uploaded_by, $uploaded_at);
        echo "<script>alert('Book uploaded successfully.'); window.location.href='book-manage.php';</script>";
        exit;
    }
}
?>
<div id="main-container">
<section>
    <h2><?= $edit_mode ? "Edit Book" : "Upload Book" ?></h2>
    <form method="POST" action="" id="myForm">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <input type="text" name="title" placeholder="Book Title" value="<?= htmlspecialchars($edit_data['title']) ?>" required><br>
        <input type="text" name="author" placeholder="Author" value="<?= htmlspecialchars($edit_data['author']) ?>" required><br>
        <textarea name="description" placeholder="Description" required><?= htmlspecialchars($edit_data['description']) ?></textarea><br>
        <input type="number" name="price" placeholder="Price" value="<?= htmlspecialchars($edit_data['price']) ?>" required min="0"><br>

        <label>Uploaded By:</label>
        <select name="uploaded_by" required>
            <option value="">-- Select Uploader --</option>
            <?php
            $users->data_seek(0);
            while ($u = $users->fetch_assoc()):
                $selected = $u['id'] == $edit_data['uploaded_by'] ? 'selected' : '';
            ?>
                <option value="<?= $u['id'] ?>" <?= $selected ?>><?= htmlspecialchars($u['name']) ?></option>
            <?php endwhile; ?>
        </select><br>

        <button type="submit"><?= $edit_mode ? "Update Book" : "Upload Book" ?></button>
    </form>

    <h3 class="section-title">📚 All Books</h3>
    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search agents..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>
    <div class="card-grid">
        <?php while ($row = $books->fetch_assoc()):?>
        <div class="card">
            <div class="card-header">
                <strong><?= htmlspecialchars($row['title']) ?></strong>
            </div>
            <div class="card-body" style="text-align:left;">
                <p><strong>📖 Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                <p><strong>👤 Uploaded By:</strong> <?= htmlspecialchars($row['uploader']) ?></p>
                <p><strong>💰 Price:</strong> ₹<?= htmlspecialchars($row['price']) ?></p>
                <p><strong>🕒 Uploaded At:</strong> <?= htmlspecialchars($row['uploaded_at']) ?></p>
            </div>
            <div class="card-actions">
                <a href="?edit=<?= $row['id'] ?>" class="edit">✏️ Edit</a>
                <a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this book?')">🗑️ Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
 <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>" class="pagination-link">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="pagination-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>" class="pagination-link">Next &raquo;</a>
        <?php endif; ?>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
