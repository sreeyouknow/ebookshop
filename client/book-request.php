<?php
include '../client/include/header.php';

$client_id = $_SESSION['user_id'] ?? 0;

// Submit Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_request_id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $note = trim($_POST['note']);

    $insertBookRequest = $user->insertBookRequest($book_request_id, $client_id, $title, $author, $note);
    $message = "Book request submitted successfully.";
}

$limit = 5;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

$total_books = $pagesearch->countBookRequests($search);
$total_pages = ceil($total_books / $limit);

// Fetch client requests
$result = $pagesearch->getBookRequestsByClient($client_id, $search, $start, $limit);
?>
<div id="main-container">
<section>
<h2>Request a Book</h2>

<?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="POST" id="myForm" style="display:block; border:1px solid grey; padding:20px 40px; width:40%;">
    <input type="hidden" name="id" value="<?= $insertBookRequest['id'] ?>">
    <label>Book Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Author:</label><br>
    <input type="text" name="author" required><br><br>

    <label>Note:</label><br>
    <textarea name="note" required style="width:100%; height:100px;"></textarea><br><br>

    <button type="submit">Request Book</button>
</form>
<br>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>
    <br>
<h3>Your Book Requests</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Title</th><th>Author</th><th>Note</th><th>Requested At</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td><?= $row['requested_at'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>
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
<?php include '../includes/footer.php'; ?>
