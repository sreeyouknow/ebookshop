<?php
include '../client/include/header.php';
require '../Classes/delete-controller.php';

$delete = new delete($conn);
$client_id = $_SESSION['user_id'] ?? 0;
$message = "";

// --- Remove from Cart ---
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    $delete->deleteCarts($remove_id, $client_id);
    $message = "Book removed from cart.";
}

// --- Place Single Book Order via AJAX (no JSON)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order']) && isset($_POST['cart_id'])) {
    ob_start();
    $cart_id = intval($_POST['cart_id']);

    $stmt = $conn->prepare("SELECT cart.*, books.pdf_path 
        FROM cart 
        JOIN books ON cart.book_id = books.id 
        WHERE cart.id = ? AND cart.client_id = ?
    ");
    $stmt->bind_param("ii", $cart_id, $client_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $book_id = $row['book_id'];
        $pdf_path = $row['pdf_path'];

        if (empty($pdf_path)) {
            echo "<div class='ajax-response' data-success='false' data-message='ther is no file is here.'></div>";
            exit;
        }
        $inserted = $user->insertBookOrder($client_id, $book_id);

        if ($inserted) {
            $delete->deleteCarts($cart_id, $client_id);
            echo "<div class='ajax-response' data-success='true' data-message='Book ordered successfully!' data-url='{$pdf_path}'></div>";
        } else {
            echo "<div class='ajax-response' data-success='false' data-message='Error placing order. Try again.'></div>";
        }
    } else {
        echo "<div class='ajax-response' data-success='false' data-message='file is empty'></div>";
    }
    exit;
}

// --- Pagination + Search ---
$limit = 3;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$searchParam = "%{$search}%";

// Count total
$count_stmt = $conn->prepare("SELECT COUNT(*) AS total 
    FROM cart 
    JOIN books ON cart.book_id = books.id
    WHERE cart.client_id = ? AND books.title LIKE ?
");
$count_stmt->bind_param("is", $client_id, $searchParam);
$count_stmt->execute();
$total_books = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);

// Get paginated items
$stmt = $conn->prepare("SELECT cart.id, books.title, books.author, books.price, cart.quantity 
    FROM cart 
    JOIN books ON cart.book_id = books.id 
    WHERE cart.client_id = ? AND books.title LIKE ?
    LIMIT ? OFFSET ?
");
$stmt->bind_param("issi", $client_id, $searchParam, $limit, $start);
$stmt->execute();
$cart = $stmt->get_result();
?>

<div id="main-container">
<section>
    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="submit">Search</button>
        </form>
    </div>

    <div class="card-gird">
        <h2>🛒 My Cart</h2>
        <div class="card-container">
            <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

            <?php if ($cart->num_rows > 0): ?>
                <?php while ($row = $cart->fetch_assoc()): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                    <p><strong>Price:</strong> ₹<?= $row['price'] ?></p>

                    <label><strong>Quantity:</strong></label>
                    <select disabled>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option <?= $i == $row['quantity'] ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <div>
                        <a href="?search=<?= urlencode($search) ?>&page=<?= $page ?>&remove=<?= $row['id'] ?>" onclick="return confirm('Remove this book from cart?')" class="delete">🗑️ Remove</a>
                    </div>

                    <form method="POST" class="ajax-form">
                        <input type="hidden" name="cart_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="place_order" onclick="return confirm('Place order for this book?')">✅ Place Order</button>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
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

<script>
$(document).ready(function () {
    $('.ajax-form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const cartId = form.find('input[name="cart_id"]').val();

        $.ajax({
            type: 'POST',
            url: '',
            data: {
                place_order: true,
                cart_id: cartId
            },
            success: function (html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const response = doc.querySelector('.ajax-response');

                if (response) {
                    const success = response.dataset.success === 'true';
                    const message = response.dataset.message;
                    const downloadUrl = response.dataset.url;

                    if (success) {
                        form.closest('.card').fadeOut(300, function () {
                            $(this).remove();
                            if ($('.card').length === 0) {
                                $('.card-container').html("<p>Your cart is empty.</p>");
                            }
                        });

                        if ($('#global-message').length === 0) {
                            $('.card-gird').prepend("<p id='global-message' style='color:green;'>" + message + "</p>");
                        } else {
                            $('#global-message').text(message).show();
                        }

                        if ($('#download-now').length === 0 && downloadUrl) {
                            const downloadBtn = `
                                <div id="download-now-wrapper" style="margin-top: 10px;">
                                    <button id="download-now" style="padding: 8px 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                        📥 Download Now
                                    </button>
                                </div>`;
                            $('.card-gird').prepend(downloadBtn);
                        }

                        $('#download-now').off('click').on('click', function () {
                            let pdfUrl = downloadUrl; // Already correct from above
                            let fileName = pdfUrl.split('/').pop();

                            // Ensure it ends in .pdf
                            if (!fileName.endsWith('.pdf')) {
                                fileName += '.pdf';
                            }

                            const link = document.createElement('a');
                            link.href = pdfUrl;
                            link.download = fileName;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });

                    } else {
                        alert(message);
                    }
                } else {
                    alert("Unexpected error.");
                }
            },
            error: function () {
                alert("Error placing order. Try again.");
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
