$(function() {
  // preloader
  $("#gallery").preloader();

  // error image
  $('img').error(function() {
    $('img').addClass('miss');
    $(this).attr({
      src: '../img/error.png',
      alt: 'image not found.'
    });
  });
});
