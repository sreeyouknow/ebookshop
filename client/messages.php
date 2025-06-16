<?php
include 'include/header.php';
require '../Classes/show-table-controller.php';

$showTable = new showTable($conn);

$client_id = $_SESSION['user_id'];
$agent_id = 2; // or fetch dynamically assigned agent

// Send new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $insertCConversation = $user->insertclientConversation($agent_id, $client_id, $reply);
}

// Fetch conversation
$result = $showTable->messages($client_id, $agent_id);

$status = $user->selectConversation($client_id, $agent_id)['conversation_status'] ?? 'open';
?>
<div id="main-container">
<section style="height:80vh;"><h2>Conversation with Agent</h2>
    <div>
        <?php while ($row = $result->fetch_assoc()): ?>
            <p><strong><?= ucfirst($row['sender']) ?>:</strong> <?= htmlspecialchars($row['message']) ?> <em>(<?= $row['sent_at'] ?>)</em></p>
        <?php endwhile; ?>
    </div>

    <?php if ($status === 'open'): ?>
        <form method="POST">
            <textarea name="message" required></textarea><br>
            <button type="submit">Send</button>
        </form>
    <?php else: ?>
        <p><strong>This conversation has been ended by the agent.</strong></p>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
