$(function() {

  $('#hamburger').mouseover((e) => {
      $('.navbar').css('height', "21rem");
      $('.menu-icon').addClass('active');
      $('.dropdown-menu').css('display', 'block')
  })

  $('.navbar').mouseleave((e) => {
    $('.navbar').css('height', "4rem");
    $('.menu-icon').removeClass('active');
    $('.dropdown-menu').css('display', 'none')
  })

})