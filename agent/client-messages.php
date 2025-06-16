<?php
include '../agent/include/header.php';
require '../Classes/delete-controller.php';
require '../Classes/edit-controller.php';
require '../Classes/agent-message-controller.php';

$edit = new edit($conn);
$delete = new delete($conn);

$agent_id = $_SESSION['user_id'] ?? null;

// --- Handle Actions ---

// End Conversation
if (isset($_GET['end_conversation'])) {
    $client_to_end = (int)$_GET['end_conversation'];
    $endConversation = $edit->endConversation($agent_id, $client_to_end);
    header("Location: client-messages.php");
    exit;
}

// Delete Message
if (isset($_GET['delete_message'])) {
    $id = (int)$_GET['delete_message'];
    $del_message = $delete->deleteMessages($id, $agent_id);
    header("Location: client-messages.php");
    exit;
}

// Delete Review
if (isset($_GET['delete_review'])) {
    $id = (int)$_GET['delete_review'];
    $del_reviews = $delete->deleteReviews($id, $agent_id);
    header("Location: client-messages.php");
    exit;
}

// Send Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = (int)$_POST['id'];
    $reply = trim($_POST['reply_text']);

    // Check if conversation is ended
    $status = $user->selectConversation($client_id, $agent_id);

    if (!$status || $status['conversation_status'] !== 'ended') {
        $insertCon = $user->insertConversation($agent_id, $client_id, $reply);
    }
    header("Location: client-messages.php");
    exit;
}

// --- Pagination & Search ---


if ($agent_id) {
    $messageManager = new MessageManager($conn);
    $messageManager->setAgentId($agent_id);

    $page = $_GET['page'] ?? 1;
    $search = $_GET['search'] ?? '';
    $limit = 3;

    $total_pages = $messageManager->getTotalClientPages($search, $limit);
    $clients = $messageManager->getClients($search, $page, $limit);
    $messages_by_client = $messageManager->getMessagesByClients($clients);
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
    <h2>💬 Client Conversations</h2>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search clients..." value="<?= htmlspecialchars($search) ?>">
             <button id="searchBtn" type="button">Search</button>
        </form>
    </div>

    <div class="container" style="display:flex; gap:25px;">
        <?php if (!empty($messages_by_client)): ?>
            <?php foreach ($messages_by_client as $client_id => $data): 
                $messages = $data['messages'];
                $last = end($messages);
                $conversation_ended = $last['conversation_status'] === 'ended';
            ?>
                <div style="border:2px solid #333; padding:15px;">
                    <h3>👤 <?= htmlspecialchars($data['name']) ?></h3>
                    
                    <a href="?end_conversation=<?= $client_id ?>" 
                        data-client-id="<?= $client_id ?>" 
                        class="end-conversation" 
                        style="color:red;">🛑 End Conversation</a>

                    <div style="margin-top:10px;">
                        <?php foreach ($messages as $msg): ?>
                            <div style="margin:10px 0; padding:10px; background:#f9f9f9; border:1px solid #ccc;">
                                <strong><?= $msg['sender'] === 'agent' ? '🟢 Agent' : '🔵 Client' ?>:</strong>
                                <?= htmlspecialchars($msg['message']) ?>
                                <div style="font-size:12px; color:gray;"><?= htmlspecialchars($msg['sent_at']) ?></div>
                                <a href="?delete_message=<?= $msg['id'] ?>" style="color:red;" onclick="return confirm('Delete this message?')" class="delete">🗑️ Delete</a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!$conversation_ended): ?>
                        <form method="POST" class="reply-form" data-client-id="<?= $client_id ?>" style="margin-top:15px;">
                            <input type="hidden" name="id" value="<?= $client_id ?>">
                            <textarea name="reply_text" rows="3" placeholder="Your reply..." required style="width:100%;"></textarea>
                            <button type="submit">Reply</button>
                        </form>

                    <?php else: ?>
                        <p style="color:gray;">🛑 This conversation is closed.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages found.</p>
        <?php endif; ?>
    </div>
<br>
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
    $(document).on('click', '.end-conversation', function (e) {
    e.preventDefault();
    if (!confirm('End conversation with this client?')) return;

    let clientId = $(this).data('client-id');

    $.ajax({
        url: 'client-messages.php?end_conversation=' + clientId,
        method: 'GET',
        success: function () {
            // Reload the section content
            let currentPage = $('.pagination-link.active').attr('href') || window.location.href;
            $.get(currentPage, function (data) {
                $('section').html($(data).find('section').html());
            });
        },
        error: function () {
            alert('Failed to end conversation.');
        }
    });
});
$(document).on('submit', '.reply-form', function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = form.serialize();

    $.ajax({
        url: 'client-messages.php',
        method: 'POST',
        data: formData,
        success: function () {
            // Reload the section content
            let currentPage = $('.pagination-link.active').attr('href') || window.location.href;
            $.get(currentPage, function (data) {
                $('section').html($(data).find('section').html());
            });
        },
        error: function () {
            alert('Failed to send reply.');
        }
    });
});
</script>
<?php include '../includes/footer.php'; ?>
