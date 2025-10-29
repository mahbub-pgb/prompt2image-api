jQuery(document).ready(function ($) {

    // Get saved tab or default to 'users'
    var activeTab = localStorage.getItem('p2i_active_tab') || 'users';

    // Function to activate a tab
    function showTab(tab) {
        // Buttons
        $('.tab-button').removeClass('active');
        $('.tab-button[data-tab="' + tab + '"]').addClass('active');

        // Content
        $('.tab-content').removeClass('active').hide();
        $('.tab-' + tab).show().addClass('active');
    }

    // Show saved or default tab right away
    showTab(activeTab);

    // Initialize DataTable (for Users tab)
    if ($('#prompt2image-users-table').length) {
        $('#prompt2image-users-table').DataTable();
    }

    // Handle tab click
    $('.tab-button').on('click', function () {
        var tab = $(this).data('tab');
        localStorage.setItem('p2i_active_tab', tab);
        showTab(tab);
    });
});
