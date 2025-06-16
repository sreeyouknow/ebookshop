<?php
include '../agent/include/header.php';
require '../Classes/view-total-controller.php';

$viewTotalCount = new veiwTotalController($conn);

// Total clients
$total_clients = $viewTotalCount->tclients()['total'];

// Pagination and search setup
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

// Get total book count (for pagination only)

$total_books = $pagesearch->countBooks($search)['total'];
$total_pages = ceil($total_books / $limit);

// Get paginated books based on search
$books_result = $pagesearch->getBooks($search, $start, $limit);
?>
<style>
.pagination {
    text-align: center;
    margin-top: 20px;
}
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
<!-- Agent Dashboard Content -->
<section>
    <h2 class="section-title">Agent Dashboard | <?=htmlspecialchars($c_user_name)?></h2>

    <!-- Quick Stats -->
    <h3 class="section-subtitle">📊 Quick Stats</h3>
    <div class="card-grid">
        <div class="card stat-card">
            <h4>Total Clients</h4>
            <p><?= $total_clients ?></p>
        </div>
        <div class="card stat-card">
            <h4>Total Books Uploaded</h4>
            <p><?= $total_books ?></p>
        </div>
    </div>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>

    <!-- Recent Book Uploads -->
    <h3 class="section-subtitle">📚 Recent Book Uploads</h3>
    <div class="card-grid">
        <?php if ($books_result->num_rows > 0): ?>
            <?php while ($book = $books_result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-body">
                        <p><strong>📖 Title:</strong> <?= htmlspecialchars($book['title']) ?></p>
                        <p><strong>👤 Uploaded By:</strong> <?= htmlspecialchars($book['uploaded_by']) ?></p>
                        <p><strong>🕒 Uploaded At:</strong> <?= $book['uploaded_at'] ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No books found.</p>
        <?php endif; ?>
    </div>

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
