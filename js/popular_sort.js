(function ($) {
    $(document).ready(function () {
        $('.menu-item-' + menu_item_id).click(function(e) {
            e.preventDefault();
            window.location.href = all_deals_cat_link + '?orderby=popularity';
        });
    });
})(jQuery);