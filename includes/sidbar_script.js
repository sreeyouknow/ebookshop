// Submenu toggle
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".submenu-toggle").forEach(toggle => {
        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
        });
    });
});


// AJAX Page Loader
// Sidebar AJAX Loader — only apply to top-level sidebar items
$(document).on("click", ".nav-list > li > .sidebar-link, .nav-list .submenu .sidebar-link", function (e) {
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
});

//back and forward navigate pages
window.addEventListener('popstate', function () {
    // Reload the current URL content via AJAX
    $.ajax({
        url: window.location.href,
        method: 'GET',
        success: function (response) {
            const tempDiv = $("<div>").html(response);
            const content = tempDiv.find("#main-container").html();

            if (content) {
                $("#main-container").html(content);
            } else {
                $("#main-container").html(response);
            }
        },
        error: function () {
            $("#main-container").html("<p style='color:red;'>Failed to reload page.</p>");
        }
    });
});

