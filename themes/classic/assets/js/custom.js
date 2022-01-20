document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll('.page-home .categorysectionsmain-products .item .content').forEach((item, index) => {
    item.style.backgroundImage = `url('../img/cms/${index + 1}.jpg')`;
  })
  const mobile = window.innerWidth < 768
  const options = {
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
  }
  if (mobile) {
    $('#slick-feedback').slick(options);
    if ($('.product-accessories .product').length > 5) {
      $('.product-accessories .products').slick(options);
    }
    if ($('.featured-products .product').length > 5) {
      $('.featured-products .products').slick(options);
    }
  } else {
    $('#slick-feedback').slick({
      infinite: true,
      slidesToShow: 2,
      slidesToScroll: 1,
      arrows: true,
    });
    if ($('.product-accessories .product').length > 5) {
      $('.product-accessories .products').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        arrows: true,
      });
    }
    if ($('.featured-products .product').length > 5) {
      $('.featured-products .products').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        arrows: true,
      });
    }
  }

})
