(function ($) {
    $(document).ready(function () {
        var toggle_div = '<a class="xt_mobile_menu_toggle" href="#"><i class="fa fa-arrow-down"></i></a>';
        $('.xt-category-mobile-menu').closest('.main-header').prepend(toggle_div);
    }).on('click', '.xt_mobile_menu_toggle', function (e) {
        e.preventDefault();
        var el = $(this);
        el.find("i").toggleClass("fa-arrow-up fa-arrow-down");
        $('.xt-category-mobile-menu').toggleClass('shown');
    });
})(jQuery);