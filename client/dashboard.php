<?php include '../client/include/header.php'; ?>
<style>
    
    .dashboard-option {
        width:30%;
        background-color: #eaeef3;
        padding: 18px;
        border-radius: 10px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .dashboard-option:hover {
        background-color: #dceeff;
        transform: translateY(-3px);
    }

</style>
<div id="main-container">
<section>
    <div>
        <span><h1>Welcome |<span><?php echo $c_user_name; ?></span></h1></span>
        <span><a href="javascript:void(0)" class="sidebar-link" data-page="update_profile.php"> 👤</a></span>
    </div><br>
    <div class="dashboard-option">
        <a href="javascript:void(0)" class="sidebar-link" data-page="view-books.php" style="color:#1a2942;">Browse books</a>
    </div><br>
    <div class="dashboard-option">
        <a href= "javascript:void(0)" class="sidebar-link" data-page="book-request.php" style="color:#1a2942;">Request Book</a>
    </div><br>
    <div class="dashboard-option">
        <a href="javascript:void(0)" class="sidebar-link" data-page="messages.php" style="color:#1a2942;">Contact Agent</a>
    </div><br><br>
</section>
<script>$(document).on("click", ".sidebar-link", function (e) {
    e.preventDefault();

    const page = $(this).data("page");
    if (!page) return;

    $.ajax({
        url: page,
        method: "GET",
        success: function (response) {
            const tempDiv = $("<div>").html(response);
            const content = tempDiv.find("#main-container").html();

            if (content) {
                $("#main-container").html(content);
                window.scrollTo(0, 0);

                // ✅ Update browser URL
                history.pushState(null, '', page);
            } else {
                $("#main-container").html(response); // fallback
            }
        },
        error: function () {
            $("#main-container").html("<p style='color:red;'>Error loading page.</p>");
        }
    });
});</script>
<?php include '../includes/footer.php'; ?>

