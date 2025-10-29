jQuery(document).ready(function ($) {
    // Initialize DataTable
    $('#prompt2image-users-table').DataTable();

    // Handle tab switching
    $('.tab-button').on('click', function () {
        var tab = $(this).data('tab');

        $('.tab-button').removeClass('active');
        $(this).addClass('active');

        $('.tab-content').hide().removeClass('active');
        $('.tab-' + tab).show().addClass('active');
    });
});
