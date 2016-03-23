(function ($) {
    $(document).ready(function () {
        if($('.sale_schedule').length) {
            $('.sale_schedule').click();
            $('.cancel_sale_schedule').hide();
            setTimeout(function () {
                $('#_sale_price_dates_to').val('Always').prop('disabled', true).closest('.sale_price_dates_fields').hide();
            }, 200);
        }
        if ($('#wcv-product-edit').length) {
            var selectors = [
                {
                    selector: 'post_title',
                    placeholder: 'Product Name',
                    text: 'The product name will also be title of your deal.',
                },
                {
                    selector: 'post_content',
                    placeholder: 'Product Description',
                    text: 'Product Description will be a detailed explanation of your product, this will be displayed in the large description tab below your product image.',
                },
                {
                    selector: 'post_excerpt',
                    placeholder: 'Product Short Description',
                    text: 'Product Short Description will be the highlights of your product, that will be displayed to the right of your image and above the add to cart button.',
                },
                {
                    selector: '_xt_product_options',
                    placeholder: 'Product Options',
                    text: 'What size, color, or customoization will your product be available in? This is where you will jot down the options your customer has to choose from.',
                },
                {
                    selector: '_regular_price',
                    placeholder: 'Regular Price ($)',
                    text: 'Retail Price is the price you\'re currently selling this product for on your website, etsy, etc. Do not inflate this price to increase the percentage off. Customers will find out and it will do more harm than good.',
                },
                {
                    selector: '_sale_price',
                    placeholder: 'Sale Price ($)',
                    text: 'Sale Price is the price you\'ll be selling your deal for. We recommend it be 40-50% off the retail price.',
                },
            ];
            for(var i in selectors) {
                var selector = 'label[for="' + selectors[i]['selector'] + '"]';
                var text = selectors[i]['text'];
                var el = $(selector);
                var left = el.outerWidth() + 5;

                el.after('<div class="xt_question_div">?</div>');

                var question_div = el.next('.xt_question_div');
                question_div.attr('title', text);
                question_div.css('margin-left', left).tooltip();

                //placeholder
                {
                    var placeholder = selectors[i]['placeholder'];
                    $('#' + selectors[i]['selector']).attr('placeholder', placeholder);
                }
            }
        }
    });
}) (jQuery);