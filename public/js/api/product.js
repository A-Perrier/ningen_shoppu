let createButton = $('#product-create');
let editButton = $('#product-edit');


const resetForm = () => {
  let vichInputs = $('.vich-image input[type="file"]');
  $.map(vichInputs, (input) => {
    let label = $(input).parent().parent().children("label")
      $(label).text("Choisir une photo");
      $(label).css('border', "1px solid #FF7C5C").css('color', "#FF7C5C");
  });

  $('form[name=product]')[0].reset();
}

createButton.click((e) => {
  e.preventDefault();
  
  const sendPicturesAjaxCall = (id) => {
    let filesData = new FormData($('form[name=product]')[0]);
    $.ajax({
      url: "/api/product/create/insertPictures/" + parseInt(id),
      method: "POST",
      processData: false,
      contentType: false,
      data: filesData,
      success: function(response, statusCode) {
        successToast("Les photos ont correctement été envoyées, merci !");
        resetForm();
      },
      error: function (result, status, error) {
        dangerToast("Le formulaire n'a pas été envoyé, il n'a pas été rempli correctement");
        console.log(result.responseText);
      }
    })
  }

  
  let data = {
    wording: $('#product_wording').val(),
    description: $('#product_description').val(),
    price: parseInt($('#product_price').val()),
    category: parseInt($('#product_category').val())
  }

  $.ajax({
    url: "/api/product/create",
    method: "POST",
    data: JSON.stringify(data),
    success: function(response) {
      let productId = response;
      sendPicturesAjaxCall(productId);
      successToast("Le formulaire a été validé. En attente du chargement des photos ...");
    },
    error: function (result, status, error) {
      dangerToast("Le formulaire n'a pas été envoyé, il n'a pas été rempli correctement");
      console.log(result.responseText);
    }
  })
})


