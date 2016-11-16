jQuery(document).ready(function(){
	jQuery('.slider-container').slick({
		prevArrow: '.on-prev-slide',
		nextArrow: '.on-next-slide',
		autoplay: true,
		autoplaySpeed: 4000,
		fade: true,
		speed: 500,
		pauseOnHover: false,
		lazyLoad: 'ondemand',
	});
	jQuery('.slider-container').show();
});