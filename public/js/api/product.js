/**
 * 
 */

let createButton = $('#product-create');
let editButton = $('#product-edit');
let deleteButton = $('[role=product-delete]');

const resetForm = () => {
  let vichInputs = $('.vich-image input[type="file"]');
  $.map(vichInputs, (input) => {
    let label = $(input).parent().parent().children("label")
      $(label).text("Choisir une photo");
      $(label).css('border', "1px solid #FF7C5C").css('color', "#FF7C5C");
  });

  $('form[name=product]')[0].reset();
}


/**
 * /!\ 
 *     Ne prend en charge que la création de produit.
 *     Dans le cas d'une édition, on souhaite rediriger l'utilisateur sur le produit en question 
 * /!\
 * On doit séparer en deux requêtes Ajax l'envoi du formulaire, puisque pour les fichiers, les données processData et contentType
 * sont différentes.
 * 1. On envoie la requête avec toutes les données sauf les photos.
 * 2. Le produit est créé en base de données, on récupère son ID dans la réponse.
 * 3. On utilise l'ID pour renvoyer une requête avec les bonnes entêtes pour les fichiers et on met à jour le produit
 * 4. On réinitialise le formulaire
 */
createButton.click((e) => {
  e.preventDefault();
  $('.form-error').remove();
  
  const sendPicturesAjaxCall = (id) => {
    let filesData = new FormData($('form[name=product]')[0]);
    $.ajax({
      url: "/api/product/create/insertPictures/" + parseInt(id),
      method: "POST",
      processData: false,
      contentType: false,
      data: filesData,
      success: function(response, statusCode) {
        successToast("Le formulaire a totalement été validé");
        resetForm();
      },
      error: function (result, status, error) {
        dangerToast("Les photos n'ont pas été envoyées, une erreur s'est produite");
      }
    })
  }

  let labels = [];
  $.map($('input[id*=product_label]:checked'), (input, index) => {
    labels[index] = $(input).val();
  })


  let data = {
    wording: $('#product_wording').val(),
    description: $('#product_description').val(),
    price: parseInt($('#product_price').val() * 100), // Corrige le bug qui fait que le divisor du MoneyType ne s'applique que lors de l'edit
    category: parseInt($('#product_category').val()),
    isOnSale: $('#product_isOnSale').is(':checked'),
    quantityInStock: parseInt($('#product_quantityInStock').val()),
    labels: JSON.stringify(labels)
  }

  $.ajax({
    url: "/api/product/create",
    method: "POST",
    data: JSON.stringify(data),
    success: function(response) {
      let productId = response;
      sendPicturesAjaxCall(productId);
      infoToast("Le formulaire a été validé. En attente du chargement des photos ...");
    },
    error: function (result, status, error) {
      dangerToast("Le formulaire n'a pas été envoyé, il n'a pas été rempli correctement");
      let apiErrors = JSON.parse(result.responseText);
      setErrors(apiErrors, 'product');

    }
  })
})

deleteButton.click(e => {
  e.preventDefault();
  let id = parseInt($(e.currentTarget).attr('data-id'));
  let row = $(e.currentTarget).parent().parent();

  $.ajax({
    url: '/api/product/delete/' + id,
    method: "POST",
    data: {id: JSON.stringify(id)},
    success: function(response) {
      successToast(response, 2000);
      $(row).remove();
    },
    error: function(result) {
      dangerToast(result.responseJSON);
    }
  })
})