<?php
include '../admin/include/header.php';

$message ='';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])){
    $request_id = intval($_POST['request_id']);

    if(isset($_POST['approve'])){
        if($pagesearch->approve($request_id)){
            $message = "Request approved successfully.";
        }
    }elseif(isset($_POST['reject'])){
        if($pagesearch->reject($request_id)){
            $message = "request rejected.";
        }
    }
}

// --- Pagination and Search ---
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

$total_books = $pagesearch->countBookRequests($search);
$total_pages = ceil($total_books / $limit);
$requests = $pagesearch->getBookRequests($search, $start, $limit);
?>
<div id="main-container">
<section>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search agents..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>

    <h1>📚 | Book Requests</h1>
    <?php if (!empty($message)): ?>
        <p style='color:green;'><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="card-grid">
        <?php if ($requests->num_rows > 0): ?>
            <?php while ($request = $requests->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <strong>👤 <?= htmlspecialchars($request['client_name'] ?? 'Unknown') ?></strong>
                    </div>
                    <div class="card-body">
                        <p><strong>📖 Book:</strong> <?= htmlspecialchars($request['title']) ?></p>
                        <p><strong>✍️ Author:</strong> <?= htmlspecialchars($request['author']) ?></p>
                        <p><strong>📝 Note:</strong> <?= htmlspecialchars($request['note']) ?></p>
                        <p><strong>⏰ Requested At:</strong> <?= htmlspecialchars($request['requested_at']) ?></p>
                        <p><strong>Status:</strong>
                            <?php if ($request['status'] === 'approved'): ?>
                                <span style="color: green;">Approved</span>
                            <?php elseif ($request['status'] === 'rejected'): ?>
                                <span style="color: red;">Rejected</span>
                            <?php else: ?>
                                <span style="color: orange;">Pending</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="card-actions">
                        <?php if ($request['status'] === 'pending'): ?>
                            <form method="POST" action="" class="approve-btn" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" name="approve" class="btn-approve">✅ Approve</button>
                            </form>
                            <form method="POST" action="" class="reect-btn" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" name="reject" class="btn-reject">❌ Reject</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No book requests found.</p>
        <?php endif; ?>
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
$(document).ready(function () {
    $(document).on("submit", ".approve-btn, .reject-btn", function (e) {
        e.preventDefault();

        const $form = $(this);
        const formData = new FormData(this);

        const isApprove = $form.find('button[name="approve"]').length > 0;
        const action = isApprove ? 'approve' : 'reject';
        formData.append(action, '1');

        $.ajax({
            url: window.location.href, // use current URL
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                const $card = $form.closest(".card");

                const $statusPara = $card.find("p").filter(function () {
                    return $(this).text().includes("Status:");
                });

                const $statusSpan = $statusPara.find("span");

                if ($statusSpan.length) {
                    if (action === "approve") {
                        $statusSpan.text("Approved").css("color", "green");
                    } else {
                        $statusSpan.text("Rejected").css("color", "red");
                    }
                }

                $card.find(".card-actions").html('');
            },
            error: function () {
                alert("Error processing the request.");
            }
        });
    });
});

</script>
<?php include '../includes/footer.php'; ?>
<script src="../includes/search_js.js"></script>

<?php include '../includes/footer.php'; ?>