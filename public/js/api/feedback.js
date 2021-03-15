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

  console.log(data);

  $.ajax({
    url: "/api/product/feedback",
    method: "POST",
    data: JSON.stringify(data),
    success: (response) => {
      successToast(response);
      console.log(response)
    },
    error: (response) => {
      let apiErrors = JSON.parse(response.responseText);
      setErrors(apiErrors, 'feedback');
    }
  })
})