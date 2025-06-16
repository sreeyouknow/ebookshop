<?php
include '../client/include/header.php';

$client_id = $_SESSION['user_id'] ?? 0;

$limit = 3;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1): 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$searchParam = "%{$search}%";

$total_books = $pagesearch->countPurchase($client_id, $search);
$total_pages = ceil($total_books / $limit);

$orders = $pagesearch->getPurchase($client_id, $search, $start, $limit);
?>
<div id="main-container">
<section>
    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>
<h2>My Orders</h2>

<div class="card-grid">
    <?php while ($row = $orders->fetch_assoc()): ?>
        <div class="card-container">
            <div class="card">
                <p><strong>📘 Title:</strong> <?= htmlspecialchars($row['title']) ?></p>
                <p><strong>✍️ Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                <p><strong>💰 Price:</strong> ₹<?= $row['price'] ?></p>
                <p><strong>📅 Ordered At:</strong> <?= $row['purchase_date'] ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div> <br>
        <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
