$(document).ready(function () {
    // Password eye icon
    $(document).on('click', '.password-eye-icon img', function () {
        var show = $(this).attr('data-show');
        var hide = $(this).attr('data-hide');
        var $parent = $(this).closest('.form-group');
        var $input = $parent.find('.input-password');

        if ($input.attr('type') == 'password') {
            $input.attr('type', 'text');
            $(this).attr('src', hide);
        } else {
            $input.attr('type', 'password');
            $(this).attr('src', show);
        }
    });
});