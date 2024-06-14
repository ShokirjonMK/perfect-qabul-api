$(document).ready(function () {
    // Init sortable js
    $(document).on('initBlock', '.stg-gallery--block', function () {
        var $items = $(this).children('.stg-gallery--in');
        var $input_data = $(this).children('[stg-gallery--elements="all"]');

        if ($items != undefined && $items.length > 0) {
            var sortable_init = true;
            var sortable_attr = 'stg-gallery-sortable';
            var $images = $items.children('.stg-gallery--item');

            if ($images != undefined && $images.length > 0) {
                $(this).addClass('stg-gallery-with-items');
            } else {
                $(this).removeClass('stg-gallery-with-items');
            }

            if ($items.attr(sortable_attr) != undefined) {
                sortable_init = false;
            }

            if (sortable_init) {
                $items.sortable({
                    handle: '.stg-gallery--item',
                    animation: 150,
                    onChange: function (evt) {
                        var $block = $(evt.from);
                        var $parent = $block.closest('.stg-gallery--block');

                        $parent.children('[stg-gallery--elements="all"]').trigger('change');
                    }
                });

                $items.attr(sortable_attr, 'init');
            }
        }

        $input_data.trigger('change');
    });

    // Remove button
    $(document).on('click', '.stg-gallery--item-remove', function () {
        var $parent = $(this).closest('.stg-gallery--block');
        var remove = confirm(trs.messages.delete_item_qsn);

        if (remove == true) {
            $(this).closest('.stg-gallery--item').remove();
            $parent.trigger('initBlock');
        }
    });

    // Zoom link
    $(document).on('click', '[stg-gallery--element="zoom-link"]', function (e) {
        e.preventDefault();
    });

    // Input init
    $('[stg-gallery--elements="data"]').trigger('setItems');

    $(document).on('change', '[stg-gallery--elements="all"]', function () {
        var array = [];
        var parent = $(this).closest('.stg-gallery--block');
        var $block = parent.children('.stg-gallery--in');
        var $images = $block.find('[stg-gallery--element="input"]');

        if ($images != undefined && $images.length > 0) {
            $images.each(function () {
                var image_url = $(this).val();

                if (image_url != undefined && image_url != '') {
                    array.push(image_url);
                }
            });
        }

        if (array != undefined && array.length > 0) {
            $(this).val(JSON.stringify(array));
        } else {
            $(this).val('');
        }
    });

    $(document).on('change setItems', '[stg-gallery--elements="data"]', function () {
        var json;
        var items = $(this).val();
        var parent = $(this).closest('.stg-gallery--block');
        var $block = parent.children('.stg-gallery--in');
        var $elements = parent.children('.stg-gallery--elements');
        var $gallery_element = $elements.children('.stg-gallery--item-element');

        try {
            json = JSON.parse(items);
        } catch (error) {
            json = null;
        }

        if (json != undefined && json != null) {
            $(this).val('');

            $.each(json, function (i, item) {
                var url = '';
                var $gallery_item = $gallery_element.clone();

                if (typeof item === 'object' && item != undefined) {
                    url = item.url;
                } else if (item != undefined && item != '') {
                    url = item;
                }

                $gallery_item.find('[stg-gallery--element="input"]').val(url);
                $gallery_item.find('[stg-gallery--element="zoom-link"]').attr('href', url);
                $gallery_item.find('[stg-gallery--element="src"]').attr('src', url);
                $block.append($gallery_item.html());
            });

            $('.stg-gallery--item-zoom').magnificPopup({ type: 'image' });
            parent.trigger('initBlock');
        }
    });
});