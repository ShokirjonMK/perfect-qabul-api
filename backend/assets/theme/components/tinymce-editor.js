$(document).ready(function () {
    $('[data-tinymce="compact"]').each(function () {
        var id = this.id;
        var height = parseInt($(this).attr('data-height'));

        if (!isNaN(height) && height > 0) {
            editor_height = height;
        } else {
            editor_height = 200;
        }

        tinymce.init({
            selector: "#" + id,
            language: site_lang,
            height: editor_height,
            plugins: ["advlist autolink link lists print preview hr anchor pagebreak", "searchreplace wordcount code fullscreen insertdatetime nonbreaking", "save table contextmenu directionality paste"],
            toolbar: "insertfile undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link forecolor backcolor",
        });
    });

    $('[data-tinymce="inline"]').each(function () {
        var id = this.id;
        var toolbar = $(this).attr('data-toolbar');
        var plugins = $(this).attr('data-plugins');
        var valid_styles = $(this).attr('data-valid-styles');
        var block_formats = $(this).attr('data-block-formats');
        var valid_elements = $(this).attr('data-valid-elements');
        var save_to = $(this).attr('data-save-to');

        var config = {
            selector: "#" + id,
            menubar: false,
            contextmenu: false,
            inline: true,
            language: site_lang,
        };

        if (toolbar != undefined && toolbar != '') {
            config.toolbar = toolbar;
        } else {
            config.toolbar = "formatselect | bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | emoticons image table paragraphgroup | removeformat insertgroup";
        }

        if (plugins != undefined && plugins != '') {
            config.plugins = plugins;
        } else {
            config.plugins = ["advlist autolink link lists print preview hr anchor pagebreak searchreplace code fullscreen help nonbreaking table directionality paste image media emoticons"];
        }

        if (valid_elements != undefined && valid_elements != '') {
            config.valid_elements = valid_elements;
        }

        if (valid_styles != undefined && valid_styles != '') {
            config.valid_styles = valid_styles;
        }

        config.toolbar_groups = {
            formatgroup: {
                icon: 'chevron-down',
                tooltip: '',
                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
            },
            paragraphgroup: {
                icon: 'image-options',
                tooltip: '',
                items: 'blockquote bullist numlist | outdent indent | media hr pagebreak'
            },
            insertgroup: {
                icon: 'plus',
                tooltip: '',
                items: 'undo redo | searchreplace fullscreen code | help'
            }
        };

        if (block_formats != undefined && block_formats != '') {
            config.block_formats = block_formats;
        } else {
            config.block_formats = 'Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6; Paragraph=p; Preformatted=pre';
        }

        if (save_to != undefined && save_to != '') {
            var $save_to = $(save_to);

            if ($save_to != undefined && save_to.length > 0) {
                config.setup = function (editor) {
                    editor.on('NodeChange', function (e) {
                        var content = editor.getContent();
                        $save_to.val(content);
                    });
                };
            }
        }

        tinymce.init(config);
    });
});