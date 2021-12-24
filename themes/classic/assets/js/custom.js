document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll('.page-home .categorysectionsmain-products .item').forEach((item, index) => {
    item.style.backgroundImage = `url('../img/cms/${index + 1}.png')`;
  })
  $('.page-home .categorysectionsmain-products .item').each(function (i) {
    $(this).css("background-image", "url(" + images[i] + ")");
  });

})
