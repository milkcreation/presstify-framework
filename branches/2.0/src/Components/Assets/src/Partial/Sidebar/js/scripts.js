jQuery(document).ready(function ($) {
    var tiFySidebar = {};

    $('body').addClass('tiFyPartial-SidebarBody');

    $(document)
        .on('click', '[aria-control="toggle_sidebar"]', function(e){
            e.preventDefault();

            let $Sidebar = $($(this).data('toggle'));

            if ($Sidebar.attr('aria-closed') == 'true') {
                $Sidebar.attr('aria-closed', 'false');
            } else {
                $Sidebar.attr('aria-closed', 'true');
            }
        })
        .on('click', function (e) {
            if (!$(e.target).closest('[aria-control="sidebar"][aria-closed="false"]').length && !$(e.target).closest('[aria-control="toggle_sidebar"]').length) {
                $('[aria-control="sidebar"][aria-closed="false"][aria-outside_close="true"]').each(function(){
                    $(this).attr('aria-closed', 'true');
                });
            }
            return true;
        });
});