<?php
include '../client/include/header.php';
require '../Classes/add-controller.php';
require '../Classes/show-table-controller.php';
require '../Classes/delete-controller.php';

$add = new add($conn);

$showTable = new showTable($conn);

$delete = new delete($conn);

$client_id = $_SESSION['user_id'] ?? 0;


// --- Add to Wishlist ---
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $book_id = intval($_GET['add']);
    $addWishlist = $add->wishlist($client_id, $book_id);
}

// --- Remove from Wishlist ---
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    $deleteWishlist = $delete->deleteWishlist($remove_id, $client_id);
    $message = "Book removed from wishlist.";
}

// --- Add to Cart ---
if (isset($_GET['cart']) && is_numeric($_GET['cart'])) {
    $wishlist_id = intval($_GET['cart']);

    // Get book_id from wishlist
    $result = $user->getBookFWishlist($wishlist_id, $client_id);
    if ($result && $row = $result->fetch_assoc()) {
        $book_id = $row['book_id'];
        $addCart = $add->addCart($client_id, $book_id);
        $message = "Book added to cart.";
    }
}



// --- Pagination + Search ---
$limit = 3;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

$total_books = $pagesearch->countWishlist($client_id, $search);
$total_pages = ceil($total_books / $limit);

// --- Fetch Wishlist
$wishlist = $pagesearch->getWishlist($client_id, $search, $start, $limit);
?>

<div id="main-container">
<section>
    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>
    <div class="card-gird">
        <h2>My Wishlist</h2>
        <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
        <div class="card-container">
            <?php if ($wishlist->num_rows > 0): ?>
                <?php while ($row = $wishlist->fetch_assoc()): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p>Author: <?= htmlspecialchars($row['author']) ?></p>
                        <p>Price: ₹<?= $row['price'] ?></p>
                        <a href="?remove=<?= $row['id'] ?>" onclick="return confirm('Remove this book from wishlist?')" class="delete">Remove</a>
                        <button class="add-to-cart" data-id="<?= $row['id'] ?>">Add to Cart</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No books found in wishlist.</p>
            <?php endif; ?>
        </div>
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
<!-- JavaScript should be after the card list -->
<script>
$(document).ready(function() {
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();

        let wishlistId = $(this).data('id');

        $.ajax({
            url: '?cart=' + wishlistId,
            method: 'GET',
            success: function(response) {
                const newSection = $(response).find('section').html();
                $('section').html(newSection);
            },
            error: function() {
                alert('Failed to add to cart.');
            }
        });
    });
});
</script>

</section>

<?php include '../includes/footer.php'; ?>
