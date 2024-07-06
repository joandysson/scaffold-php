import "/public/src/js/libs/owl.carousel.min.js";

window.onload = function () {
  $(".carousel-principal").owlCarousel({
    loop: true,
    nav: false,
    dots: true,
    items: 1,
  });
};
