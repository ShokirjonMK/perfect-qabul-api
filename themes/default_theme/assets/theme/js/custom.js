// Pageload init
pageLoadInit({
	selector: 'a',
	excludeJS: [
		'yii.js',
		'popper.min.js',
		'page-load.min.js',
		'bootstrap.min.js',
		'jquery.fancybox.min.js',
		'nprogress/init.min.js',
		'owl.carousel.min.js',
	],
	excludeElement: [
		'#nprogress',
		'.loader_bg',
	],
	beforeSend: function (href, data) {
		NProgress.start();
	},
	onSuccess: function (href, data, html) {
		NProgress.done();
	},
	onError: function (href, e, data) {
		NProgress.done();
	}
});

/* Preloader
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
$(window).on('load', function () {
	$('.loader_bg').fadeOut();
});

/* Tooltip
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
});

/* Mouseover
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
$(document).ready(function () {
	$(".main-menu ul li.megamenu").mouseover(function () {
		if (!$(this).parent().hasClass("#wrapper")) {
			$("#wrapper").addClass('overlay');
		}
	});
	$(".main-menu ul li.megamenu").mouseleave(function () {
		$("#wrapper").removeClass('overlay');
	});
});

/* Toggle sidebar
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
$(document).ready(function () {
	$('#sidebarCollapse').on('click', function () {
		$('#sidebar').toggleClass('active');
		$(this).toggleClass('active');
	});
});