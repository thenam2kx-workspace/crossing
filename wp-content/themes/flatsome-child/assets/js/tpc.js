// assets/js/tpc.js
(function(){
    if (typeof Swiper === 'undefined') return;

    // init all instances with class .tpc-swiper
    document.querySelectorAll('.tpc-swiper').forEach(function(el){
        new Swiper(el, {
            slidesPerView: 2,
            spaceBetween: 26,
            loop: true,
			grabCursor: true,
			centeredSlides: true,
            navigation: {
                nextEl: '.tpc-next',
                prevEl: '.tpc-prev',
            },
            breakpoints: {
                0: { slidesPerView: 1 },
                640: { slidesPerView: 1.2 },
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    });
})();
