<?php
include '../admin/include/header.php';
require '../Classes/edit-controller.php';
require '../Classes/delete-controller.php';

$delete = new delete($conn);
$edit = new edit($conn);

$errors = [];
$success = "";

// === Handle Create/Update ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $role === '') {
        $errors[] = "All fields except password (for update) are required.";
    } else {
        if ($id === '') {
            if ($password === '') {
                $errors[] = "Password is required for new user.";
            } else {
                 $register = $user->register($name, $email, $password, $role);
                $success = "User added successfully!";
            }
        } else {
            if ($password !== '') {
                $editUsers = $edit->editUsersr($id, $name, $email, $password, $role);
            } else {
                $editUserr = $edit->editUserr($id, $name, $email, $password, $role);
            }
            $success = "User updated successfully!";
        }
    }
}

// === Handle Delete ===
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $deleteUser = $delete->deleteUser($delete_id);
    $success = "User deleted.";
}

// === Handle Edit ===
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_user = $user->agents($edit_id);
}

// === Pagination & Search ===
$limit = 3;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');

// Count total users
$total_users = $pagesearch->countUsers($search);
$total_pages = ceil($total_users / $limit);

// Fetch paginated users
$users = $pagesearch->getUsers($search, $start, $limit);
?>
<div id="main-container">
<section>
    <h1>User Management</h1>

    <?php foreach ($errors as $err) echo "<p style='color:red;'>$err</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <h2><?php echo $edit_user ? "Edit User" : "Add New User"; ?></h2>
    <form method="POST" id="myForm">
        <input type="hidden" name="id" value="<?php echo $edit_user['id'] ?? ''; ?>">
        <input type="text" name="name" placeholder="Full Name" value="<?php echo $edit_user['name'] ?? ''; ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo $edit_user['email'] ?? ''; ?>" required>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="client" <?php if (($edit_user['role'] ?? '') == 'client') echo 'selected'; ?>>Client</option>
            <option value="agent" <?php if (($edit_user['role'] ?? '') == 'agent') echo 'selected'; ?>>Agent</option>
            <option value="admin" <?php if (($edit_user['role'] ?? '') == 'admin') echo 'selected'; ?>>Admin</option>
        </select>
        <input type="password" name="password" placeholder="<?php echo $edit_user ? 'Leave blank to keep current password' : 'Password'; ?>">
        <button type="submit"><?php echo $edit_user ? 'Update User' : 'Add User'; ?></button>
    </form>

    <hr>

    <h2 class="section-title">👥 All Users</h2>

    <div class="search-box">
        <form method="GET" id="searchForm">
            <input type="text" name="search" id="search" placeholder="Search agents..." value="<?= htmlspecialchars($search) ?>">
            <button id="searchBtn" type="button">Search</button>
        </form>
    </div>

    <div class="card-grid">
        <?php if ($users->num_rows > 0): ?>
            <?php while ($row = $users->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($row['name']) ?>
                    </div>
                    <div class="card-body" style="text-align:left;">
                        <p><strong>ID:</strong> <?= $row['id'] ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>Role:</strong> <?= $row['role'] ?></p>
                    </div>
                    <div class="card-actions">
                        <a href="?edit=<?= $row['id'] ?>" class="edit">✏️ Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this agent?')">🗑️ Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No users found.</p>
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
