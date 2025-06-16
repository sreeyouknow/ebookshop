<?php 
include '../agent/include/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $uploaded_by = $_SESSION['user_id'];
    $uploaded_at = date("Y-m-d H:i:s");

    // PDF Upload
    $pdf_path = '';
    if (isset($_FILES['book_pdf']) && $_FILES['book_pdf']['error'] === 0) {
        $filename = basename($_FILES['book_pdf']['name']);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($file_ext === 'pdf') {
            // Use unique filename
            $new_filename = time() . '_' . $filename;
            $target_path = '../uploads/' . $new_filename;

            if (move_uploaded_file($_FILES['book_pdf']['tmp_name'], $target_path)) {
                $pdf_path = $target_path;

              
                $stmt = $conn->prepare("INSERT INTO books (title, author, description, price, uploaded_by, uploaded_at, pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssiss", $title, $author, $description, $price, $uploaded_by, $uploaded_at, $pdf_path);
                $stmt->execute();
            } else {
                echo "<p style='color:red;'>❌ Failed to upload PDF.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Only PDF files are allowed.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Please select a PDF to upload.</p>";
    }
}
?>


<div id="main-container">
<section>
    <h2>Upload Book (PDF Only)</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Book Title" required>
        <input type="text" name="author" placeholder="Book Author" required>
        <textarea name="description" placeholder="Book Description" required></textarea>
        <input type="number" name="price" placeholder="Price" required>
        <input type="file" name="book_pdf" accept="application/pdf" required>
        <button type="submit">Upload Book PDF</button>
    </form>
</section>
</div>

<?php include '../includes/footer.php'; ?>
