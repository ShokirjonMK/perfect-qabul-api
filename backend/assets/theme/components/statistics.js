$(document).ready(function () {
    $(document).on('click', '[apex-chart-btn]', function () {
        var $parent = $(this).parent();
        var $body = $(this).closest('.card-body');
        var element = $(this).attr('apex-chart-btn');

        $parent.find('.btn').removeClass('active');
        $body.find('.apex-charts').addClass('d-none');

        $(this).addClass('active');
        $(element).removeClass('d-none');
    });
});

function refreshDashboardWidgets() {
    $.ajax({
        url: window.location.href,
        data: {
            'for-refresh': 1
        },
        type: 'GET',
        success: function (data) {
            var block = $(data).find('#dashboard-widgets-block');

            if (block != undefined && block.length > 0) {
                load = block.find('[data-dw-load]');

                load.each(function () {
                    var key = $(this).attr('data-dw-load');
                    var html = $(this).html();

                    $('[data-dw-load="' + key + '"]').html(html);
                });
            }
        },
    });
}