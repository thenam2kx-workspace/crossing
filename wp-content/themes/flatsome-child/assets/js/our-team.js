document.addEventListener("DOMContentLoaded", function () {
  new Swiper(".our-team-swiper", {
    slidesPerView: 3,
    spaceBetween: 20,
    navigation: {
      nextEl: ".our-team-nav .swiper-button-next",
      prevEl: ".our-team-nav .swiper-button-prev",
    },
    breakpoints: {
      0: { slidesPerView: 1 },
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  });
});
