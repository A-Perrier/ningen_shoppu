// =========================================== GESTION DE L'AFFICHAGE =========================================== //

// Affichage de la liste de livraison
let showList = (resumeItems) => {
  $.map(resumeItems, (quantity, name) => {
      // Vérifier s'il existe déjà cette ligne dans la liste
      if ($('li[data-name="' + name + '"]')) {
          $('li[data-name="' + name + '"]').remove();
      }
      // Si la quantité vaut 0, l'item ne sera pas réinvoqué au prochain appel
      if (quantity == 0) return;

      $('.resume-list').append('<li class="delivery-item" data-name="' + name + '">' + name + ' - x' + quantity + '</li>')
  });
}

let resetList = () => {
  $('input.stock-btn__input').val(0);
  $('li.delivery-item').remove();
  $('#carrier').val('');
}

let onQuantityChange = (event, resumeItems) => {
  let value = parseInt($(event.currentTarget).val());
  let productName = $(event.currentTarget).attr('data-name');
  resumeItems[productName] = value;

  showList(resumeItems);
}

// Incrémentation
let listIncrement = (event, resumeItems) => {
  let productName = $(event.currentTarget).prev().attr('data-name');

  if (productName in resumeItems) {
      resumeItems[productName] += 1;
  } else {
      resumeItems[productName] = 1;
  }
  showList(resumeItems);
}


// Décrémentation
let listDecrement = (event, resumeItems) => {
  let productName = $(event.currentTarget).next().attr('data-name');

  if (productName in resumeItems) {
      // Ne décrémente que si la valeur est supérieure à 0
      if (resumeItems[productName] >= 1) resumeItems[productName] -= 1;
  }
  showList(resumeItems);
}




// =========================================== GESTION DE L'ENVOI DE LA LIVRAISON EN AJAX =========================================== //

let handleSubmit = (event, resumeItems) => {
  event.preventDefault();
  let deliveryItems = JSON.stringify(resumeItems);
  let carrier = JSON.stringify($('#carrier').val());

  $.ajax({
    url: "/api/delivery/create",
    method: "POST",
    data: JSON.stringify({deliveryItems, carrier}),
    success: (result) => {
      successToast(result)
      resetList();

    },
    error: (result) => {
      dangerToast(result.message)
    }
  })

}