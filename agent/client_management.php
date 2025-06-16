<?php
include '../agent/include/header.php';
require '../Classes/delete-controller.php';
require '../Classes/edit-controller.php';

$edit = new edit($conn);
$delete = new delete($conn);

$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

// --- Total Count for Pagination ---
$total_clients = $pagesearch->countClient($search);

$total_pages = ceil($total_clients / $limit);

// --- Fetch Clients ---
$client_result = $pagesearch->getClient($search, $start, $limit);

// --- Handle delete client ---
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $deleteUser = $delete->deleteUser($delete_id);
    exit;
}

// --- Handle edit client ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $editUser = $edit->editUser($name, $email, $client_id);
    exit;
}
?>
<style>
    .pagination a {
    padding: 6px 12px;
    margin: 2px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #1a2942;
}
.pagination a.active {
    font-weight: bold;
    background-color: #c7a100;
    color: #fff;
}
</style>
<div id="main-container">
<section>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search clients..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>

    <h2>👥 Client Management</h2>

    <!-- Client Cards -->
    <div class="card-grid">
        <?php while ($client = $client_result->fetch_assoc()): ?>
            <div class="card">
                <div class="card-body">
                    <p><strong>👤 Name:</strong> <?= htmlspecialchars($client['name']) ?></p>
                    <p><strong>📧 Email:</strong> <?= htmlspecialchars($client['email']) ?></p>
                    <div class="card-actions">
                        <a href="client_management.php?edit_id=<?= $client['id'] ?>" class="edit">Edit</a>
                        <a href="client_management.php?delete_id=<?= $client['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete?')" style="color:red;">Delete</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if (isset($_GET['edit_id'])):
        $edit_id = (int)$_GET['edit_id'];
        $client_to_edit = $conn->query("SELECT * FROM users WHERE id = $edit_id AND role = 'client'")->fetch_assoc();
    ?>
        <h3>✏️ Edit Client</h3>
        <form method="POST" class="edit-form" id="myForm">
            <input type="hidden" name="id" value="<?= $client_to_edit['id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($client_to_edit['name']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($client_to_edit['email']) ?>" required>
            <button type="submit">Update Client</button>
        </form>
    <?php endif; ?>

    <!-- Pagination -->
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
