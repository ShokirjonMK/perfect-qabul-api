$(document).ready(function () {
    // Price input num only
    $(document).on('change paste keyup', '[product-form-price-input="price"]', function (e) {
        return (/^[0-9]*\.?[0-9]*$/).test($(this).val() + e.key);
    });

    // Discount input num only
    $(document).on('keypress', '[product-form-discount-input]', function (e) {
        return (/^[0-9]*\.?[0-9]*$/).test($(this).val() + e.key);
    });

    // Price input bind
    $(document).on('change paste keyup', '[product-form-price-input="price"]', function (e) {
        var value = parseFloat($(this).val());
        var $discount_input = $('[product-form-discount-input]');
        var $discount_value = $('[product-form-discount-input="value"]');

        if (!isNaN(value) && value > 0) {
            $discount_input.removeAttr('readonly');
        } else {
            $discount_value.val('');
            $discount_input.attr('readonly', 'readonly');
        }

        productPriceTotalBind();
    });

    // Currency select bind
    $(document).on('change', '[product-form-price-input="currency"]', function () {
        productPriceTotalBind();
    });

    // Discount type select bind
    $(document).on('change', '[product-form-discount-input="type"]', function () {
        var type = $(this).val();

        $('.product-discount-input').removeClass('active');

        if (type != undefined && type == 'price') {
            $('[product-form-discount-input="price"]').addClass('active');
        } else {
            $('[product-form-discount-input="percentage"]').addClass('active');
        }
    });

    // Discount bind
    $(document).on('change keyup', '[product-form-discount-input]', function () {
        productPriceDiscountBind();
    });

    $('[product-form-price-input]').trigger('change');
    $('[product-form-discount-input]').trigger('change');

    // Currency select bind
    $(document).on('change keyup', '[data-unit-input="quantity"]', function () {
        productUnitPriceBind();
    });
});

function productUnitPriceBind() {
    var text = '';
    var pr_price = 0;

    var $total_price = $('#product_total_price_input');
    var $currency_type = $('[product-form-price-input="currency"]');
    var $quantity_input = $('[data-unit-input="quantity"]');

    var price = parseFloat($total_price.val());
    var currency = $currency_type.val();
    var quantity = parseFloat($quantity_input.val());

    if (!isNaN(price) && price > 0) {
        pr_price = price;
    }

    if (!isNaN(quantity) && quantity > 0 && pr_price > 0) {
        var total = (pr_price / quantity);
        text = numeral(total).format('0,0.00') + ' ';

        if (currency != undefined && currency != '') {
            text += currency;
        }
    }

    $('#product_unit_price_span').html(text);
}

function productPriceTotalBind() {
    var pr_price = 0;
    var discount_price = 0;
    var discount_percentage = 0;

    var $price_input = $('[product-form-price-input="price"]');
    var $currency_type = $('[product-form-price-input="currency"]');
    var $total_price_input = $('#product_total_price_input');

    var $discount_price_input = $('[product-form-discount-input="price"]');
    var $discount_percentage_input = $('[product-form-discount-input="percentage"]');
    var $discount_type = $('[product-form-discount-input="type"]');

    var price = parseFloat($price_input.val());
    var currency = $currency_type.val();
    var discount_type = $discount_type.val();

    if (!isNaN(price) && price > 0) {
        pr_price = price;
    }

    if (discount_type != undefined && discount_type != '') {
        var _discount_price = parseFloat($discount_price_input.val());
        var _discount_percentage = parseFloat($discount_percentage_input.val());

        if (discount_type == 'percentage' && !isNaN(_discount_percentage) && _discount_percentage > 0) {
            discount_percentage = _discount_percentage;
        } else if (discount_type == 'price' && !isNaN(_discount_price) && _discount_price) {
            discount_price = _discount_price;
        }
    }

    var text = numeral(pr_price).format('0,0.00') + ' ';
    $total_price_input.val(pr_price);

    if (discount_price > 0 && pr_price > 0) {
        text = '<s>' + numeral(pr_price).format('0,0.00') + '</s> ' + numeral(discount_price).format('0,0.00') + ' ';
        $total_price_input.val(discount_price);
    }

    if (discount_percentage > 0 && pr_price > 0) {
        var _sale_count = price * (discount_percentage / 100);
        var _sale_price = (price - _sale_count);
        text = '<s>' + numeral(pr_price).format('0,0.00') + '</s> ' + numeral(_sale_price).format('0,0.00') + ' ';
        $total_price_input.val(_sale_price);
    }

    if (currency != undefined && currency != '') {
        text += currency;
    }

    $('#product_sale_price_span').html(text);

    productUnitPriceBind();
}

function productPriceDiscountBind() {
    var $price_input = $('[product-form-price-input="price"]');
    var $discount_type_input = $('[product-form-discount-input="type"]');
    var $discount_price_input = $('[product-form-discount-input="price"]');
    var $discount_percentage_input = $('[product-form-discount-input="percentage"]');

    var type = $discount_type_input.val();
    var price = parseFloat($price_input.val());

    if (type != undefined && type != '' && !isNaN(price) && price > 0) {
        var discount_price = parseFloat($discount_price_input.val());
        var discount_percentage = parseFloat($discount_percentage_input.val());

        if (type == 'percentage') {
            if (isNaN(discount_percentage) && discount_percentage < 1) {
                $discount_percentage_input.val('0');
            } else if (discount_percentage > 99) {
                $discount_percentage_input.val('99');
            }
        }

        if (type == 'price') {
            if (isNaN(discount_price) && discount_price < 1) {
                $discount_price_input.val('0.00');
            } else if (discount_price >= price) {
                $discount_price_input.val((price - 1).toFixed(2));
            }
        }
    }

    productPriceTotalBind();
}