<?php
include 'include/header.php';
require '../Classes/delete-controller.php';
require '../Classes/show-table-controller.php';

$showTable = new showTable($conn);
$delete = new delete($conn);

$client_id = $_SESSION['user_id'];
$review_data = null;

// Handle New Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'submit_review') {
        $agent_id = $_POST['id'] ;
        $rating = $_POST['rating'];
        $message = $_POST['review_message'];
        $insertReviews = $user->insertReviews($client_id, $agent_id, $message, $rating);
        echo "Review submitted successfully!";
        exit;
    }

    if ($action === 'edit_review') {
        $id = $_POST['id'];
        $message = $_POST['edit_message'];
        $rating = $_POST['edit_rating'];
        $stmt = $conn->prepare("UPDATE reviews SET message = ?, rating = ? WHERE id = ? AND client_id = ?");
        $stmt->bind_param("siii", $message, $rating, $id, $client_id);
        $stmt->execute();
        echo "Review updated successfully!";
        exit;
    }
}


// Handle Delete or Edit Review
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    if ($action == 'delete_review') {
        $delete->deleteReviewsC($id, $client_id);
        echo "<p style='color:red;'>Review deleted successfully!</p>";
    } elseif ($action == 'edit_review') {
        $review_data = $showTable->reviews($id, $client_id);
    }
}

// Pagination and Search
$limit = 2;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$searchParam = "%{$search}%";

$count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reviews WHERE client_id = ? AND message LIKE ?");
$count_stmt->bind_param("is", $client_id, $searchParam);
$count_stmt->execute();
$total_reviews = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_reviews / $limit);

$review_stmt = $conn->prepare("SELECT * FROM reviews WHERE client_id = ? AND message LIKE ? ORDER BY sent_at DESC LIMIT ? OFFSET ?");
$review_stmt->bind_param("isii", $client_id, $searchParam, $limit, $start);
$review_stmt->execute();
$reviews_result = $review_stmt->get_result();
?>

<div id="main-container">
<section>
    <h3>Leave New Review</h3>
    <form method="POST" class="reviews" style="display:block; border:1px solid grey; padding:20px 40px; width:40%;">
        <input type="hidden" name="action" value="submit_review">
        <input type="hidden" name="id" value="2" required>
        <textarea name="review_message" placeholder="Your review" required></textarea>
        <input type="number" name="rating" min="1" max="5" placeholder="Rating (1-5)" required>
        <button type="submit" name="submit_review">Submit</button>
    </form>

    <!-- Edit Review Form -->
    <?php if ($review_data): ?>
        <hr>
        <h3>Edit Review</h3>
        <form method="POST" class="reviews">
            <input type="hidden" name="action" value="edit_review">
            <input type="hidden" name="id" value="<?= htmlspecialchars($review_data['id']); ?>">
            <textarea name="edit_message" required><?= htmlspecialchars($review_data['message']); ?></textarea><br>
            <input type="number" name="edit_rating" value="<?= htmlspecialchars($review_data['rating']); ?>" min="1" max="5" required><br>
            <button type="submit" name="edit_review">Update Review</button>
        </form>
    <?php endif; ?>

    <hr>
    <h2>Your Reviews</h2>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search reviews..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="submit">Search</button>
        </form>
    </div>
    <br>
    <div class="card-container">
        <?php if ($reviews_result->num_rows > 0): ?>
            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                <div class="card">
                    <p><strong>Review for Agent <?= $review['agent_id']; ?>:</strong> <?= htmlspecialchars($review['message']); ?></p>
                    <p><strong>Rating:</strong> <?= htmlspecialchars($review['rating']); ?> ⭐</p>
                    <p><em>Sent at: <?= $review['sent_at']; ?></em></p>
                    <?php if (!empty($review['reply'])): ?>
                        <p style="color:green;"><strong>Agent Reply:</strong> <?= htmlspecialchars($review['reply']); ?></p>
                    <?php endif; ?>
                    <a href="?action=edit_review&id=<?= $review['id']; ?>" class="edit">Edit</a>
                    <a href="?action=delete_review&id=<?= $review['id']; ?>" class="delete" onclick="return confirm('Are you sure?')" style="color:red;">🗑️ Delete</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews found.</p>
        <?php endif; ?>
    </div>

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
<script>
$(document).on('submit', '.reviews', function (e) {
    e.preventDefault();
    let form = $(this);
    let formData = form.serialize();

    $.ajax({
        url: '',
        method: 'POST',
        data: formData,
        success: function (res) {
            $('#message').html('<p style="color:green;">' + res + '</p>');
            // Refresh the section content
            $.get(window.location.href, function (data) {
                const newSection = $(data).find('section').html();
                $('section').html(newSection);
            });
        },
        error: function () {
            $('#message').html('<p style="color:red;">Something went wrong.</p>');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
