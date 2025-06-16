<?php
include '../admin/include/header.php';
require '../Classes/edit-controller.php';
require '../Classes/delete-controller.php';

$delete = new delete($conn);
$edit = new edit($conn);

// Handle AJAX Save (Insert or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password'] ?? '');
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // UPDATE agent
        if (!empty($password)) {
            $result = $edit->editUsers($id, $name, $email, $password);
        } else {
            $result = $edit->editUser($id, $name, $email);
        }
        echo "✅ Agent updated successfully!";
    } else {
        // INSERT agent
        $role = "agent";
        $result = $user->register($name, $email, $password, $role);
        echo "✅ Agent added successfully!";
    }
}

// Fetch for Edit
$edit_agent = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_agent = $user->agents($edit_id);
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $agent_del = $delete->deleteUser($id);
    exit;
}

// Pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$userCount = $pagesearch->countAgent($search);
$total_pages = ceil($userCount / $limit);

$agents = $pagesearch->getAgent($search, $start, $limit);
?>
<div id="main-container">
<section>
    <h2>Agent Management</h2>
    <form method="POST" action="" id="myForm">
        <input type="hidden" name="id" value="<?= $edit_agent['id'] ?? '' ?>">
        <input type="text" name="name" placeholder="Agent Name" required value="<?= $edit_agent['name'] ?? '' ?>">
        <input type="email" name="email" placeholder="Agent Email" required value="<?= $edit_agent['email'] ?? '' ?>">

        <?php if (!$edit_agent): ?>
            <input type="password" name="password" placeholder="Password" required>
        <?php else: ?>
            <small>Must to change password if you update changes</small><br>
            <input type="password" name="password" placeholder="New Password">
        <?php endif; ?>

        <button type="submit">Save Agent</button>
    </form>

    <h3 class="section-title">🧑‍💼 All Agents</h3>
        <div class="search-box">
            <form method="GET" id="searchForm">
                <input type="text" name="search" id="search" placeholder="Search agents..." value="<?= htmlspecialchars($search) ?>">
                <button id="searchBtn" type="button">Search</button>
            </form>
        </div>
    <div class="card-grid" style="text-align:left;">
        <?php if ($agents && $agents->num_rows > 0): ?>
            <?php while ($row = $agents->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <strong>👤 <?= htmlspecialchars($row['name']) ?></strong>
                    </div>
                    <div class="card-body">
                        <p><strong>📧 Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>🆔 ID:</strong> <?= $row['id'] ?></p>
                    </div>
                    <div class="card-actions">
                        <a href="?edit=<?= $row['id'] ?>" class="edit">✏️ Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this agent?')">🗑️ Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No agents found.</p>
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
<?php include '../includes/footer.php'; ?>
