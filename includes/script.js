// Search
$(document).on('click', '#searchBtn', function (e) {
    e.preventDefault();
    let query = $('#search').val().trim();

    if (query === '') {
        $('#message').html('<p>Please enter a search term.</p>');
        return;
    }

    $.ajax({
        url: '', // same file
        method: 'GET',
        data: { search: query },
        success: function (response) {
            let newSection = $(response).find('section').html();
            $('section').html(newSection);
            history.pushState(null, '', '?search=' + encodeURIComponent(query));
        },
        error: function () {
            $('#message').html('<p style="color:red;">Error loading search results.</p>');
            alert('error');
        }
    });
});

// Insert or Update
$(document).on('submit', '#myForm', function (e) {
    e.preventDefault();
    let formData = $(this).serialize();
    let id = $(this).find('input[name="id"]').val();

    $.ajax({
        url: '',
        method: 'POST',
        data: formData + '&save=1',
        success: function (res) {
            $('#message').html('<p style="color:green;">' + res + '</p>');
            // Reload current page
            let currentPage = $('.pagination-link.active').attr('href') || window.location.href;

            $.ajax({
                url: currentPage,
                method: 'GET',
                success: function (data) {
                    $('section').html($(data).find('section').html());
                    alert('success');
                }
            });
        },
        error: function () {
            $('#message').html('<p style="color:red;">Something went wrong.</p>');
            alert('error');
        }
    });
});

// Edit (Load form into section)
$(document).on('click', '.edit', function (e) {
    e.preventDefault();
    let href = $(this).attr('href');

    $.ajax({
        url: href,
        method: 'GET',
        success: function (response) {
            $('section').html($(response).find('section').html());
        },
        error: function () {
            alert('Failed to load edit form.');
        }
    });
});

// Delete
$(document).on('click', '.delete', function (e) {
    e.preventDefault();
    let href = $(this).attr('href');

    $.ajax({
        url: href,
        method: 'GET',
        success: function () {
            let currentPage = $('.pagination-link.active').attr('href') || window.location.href;

            $.ajax({
                url: currentPage,
                method: 'GET',
                success: function (data) {
                    $('section').html($(data).find('section').html());
                },
                error: function () {
                    alert('Failed to reload content.');
                }
            });
        },
        error: function () {
            alert('Failed to delete item.');
        }
    });
});

// Pagination
$(document).on('click', '.pagination-link', function (e) {
    e.preventDefault();
    const href = $(this).attr('href');

    $.ajax({
        url: href,
        method: 'GET',
        success: function (response) {
            const section = $(response).find('section').html();
            if (section) {
                $('section').html(section);
            } else {
                $('#main-container').html($(response).find('#main-container').html());
            }
        },
        error: function () {
            alert('Failed to load page.');
        }
    });
});

