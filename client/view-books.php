<?php
include 'include/header.php';
require '../Classes/add-controller.php';
require '../Classes/show-table-controller.php';

$add = new add($conn);
$showTable = new showTable($conn);

 $client_id = $_SESSION['user_id'] ?? null;

    if (!$client_id) {
        echo "Please log in.";
        exit;
    }

// AJAX handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    $response = "Invalid request.";

    if (isset($_POST['wishlist'])) {
        $result = $add->addWishlist($client_id, $book_id);
        $response = $result ? "Added to wishlist!" : "Already in wishlist.";
    } elseif (isset($_POST['cart'])) {
        $result = $add->addCart($client_id, $book_id);
        $response = $result ? "Added to cart!" : "Already in cart.";
    }
    echo $response;
}

// --- Pagination Setup
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// --- Search
$search = $_GET['search'] ?? '';

// --- Total Count for Pagination
$total_books = $pagesearch->countBooks($search)['total'];
$total_pages = ceil($total_books / $limit);

// --- Fetch Books with Limit + Search
$books = $pagesearch->getBooks($search, $start, $limit);

// --- Fetch Wishlist
$wishlist = $showTable->wishlist($client_id);

// --- Fetch Cart
$cart = $showTable->cart($client_id);
?>

<style>
/* Your existing styles here */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px;
    background: #4CAF50;
    color: white;
    border-radius: 5px;
    display: none;
    z-index: 1000;
}
</style>

<div id="main-container">
<section>
<div class="container">
    <div class="notification" id="notification"></div>
    
    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>
    <div class="card-gird">
        <h2>Available Books</h2>
        <div class="card-container">
            <?php while ($book = $books->fetch_assoc()): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p><strong>Price:</strong> ₹<?= $book['price'] ?></p>
                    <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                    <form method="POST" class="ajax-form">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <button type="submit" name="wishlist" class="wishlist-btn">Wishlist</button>
                        <button type="submit" name="cart" class="cart-btn">Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
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
</section>

<script>
    function updateCartCount() {
    $.ajax({
        url: '', 
        method: 'GET',
        data: { get_cart_count: 1 },
        dataType: 'json',
        success: function(response) {
            $('#count').text(response.total_book);
        },
        error: function() {
            console.error("Failed to update cart count.");
        }
    });
}
$(document).ready(function() {
    let clickedButton = null;

    $('.ajax-form button').on('click', function () {
        clickedButton = $(this).attr('name');
    });

    $('.ajax-form').submit(function(e) {
        e.preventDefault();

        const form = $(this);
        const bookId = form.find('input[name="book_id"]').val();
        const formData = {
            book_id: bookId
        };
        formData[clickedButton] = true;

        $.ajax({
            type: 'POST',
            url: '', 
            data: formData,
            success: function(response) {
                form.find('button[name="' + clickedButton + '"]').prop('disabled', true);
                if (clickedButton === 'cart') {
                    updateCartCount();
                }
            },
            error: function() {
                $('#notification').text('Error occurred. Please try again.').fadeIn().delay(2000).fadeOut();
            }
        });
    });
});
</script>


<?php include '../includes/footer.php'; ?>