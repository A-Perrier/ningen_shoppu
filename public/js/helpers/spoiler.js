$(function() {
  $('.spoiler-icon').mouseover((e) => {
    $(e.currentTarget).next().fadeIn(200, () => {
      $(e.currentTarget).parent().mouseleave((event) => {
        $(e.currentTarget).next().fadeOut();
      })
    });
  })
});