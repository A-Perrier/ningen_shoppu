let sendFeedbackBtn = $('#send-feedback');
let rating = 1;
let productId = parseInt($('#product-id').val());

$(':radio').change(function() {
  rating = parseInt(this.value);
});

$(sendFeedbackBtn).click(e => {
  e.preventDefault();
  $('.form-error').remove();

  data = {
    comment: $('#feedback_comment').val(),
    rating,
    productId
  }

  $.ajax({
    url: "/api/product/feedback",
    method: "POST",
    data: JSON.stringify(data),
    success: (response) => {
      successToast("Votre avis a été publié, merci beaucoup !");
      let parsedResponse = JSON.parse(response);
      let comment = getComment(parsedResponse);
      $('.feedback-none').remove();
      $('.feedback-container').append(comment);
      $('.toHide').remove();
      $('.comment').append('<p style="padding-top:.4rem;">Merci pour votre avis !<p>')
    },
    error: (response) => {
      let apiErrors = JSON.parse(response.responseText);
      setErrors(apiErrors, 'feedback');
    }
  })
})


/**
 * Takes feedback data as an argument and return the HTML to inject into the card
 * @returns String HTML comment
 */
const getComment = (data) => {
  return `<article class="feedback-comment"><p class="feedback-header">${data.user.firstName} ${data.user.lastName}</p><p class="feedback-header">Maintenant</p><p class="feedback-content">${data.comment}</p>
</article>`
}