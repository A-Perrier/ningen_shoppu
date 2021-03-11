// Affichage de la liste de livraison
let showList = (resumeItems) => {
  $.map(resumeItems, (quantity, name) => {
      // Vérifier s'il existe déjà cette ligne dans la liste
      if ($('li[data-name="' + name + '"]')) {
          $('li[data-name="' + name + '"]').remove();
      }
      // Si la quantité vaut 0, l'item ne sera pas réinvoqué au prochain appel
      if (quantity == 0) return;

      $('.resume-list').append('<li data-name="' + name + '">' + name + ' - ' + quantity + 'x</li>')
  });
}


// Incrémentation
let listIncrement = (event, resumeItems) => {
  
      let productId = $(event.currentTarget).prev().attr('data-id');
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
  let productId = $(event.currentTarget).next().attr('data-id');
  let productName = $(event.currentTarget).next().attr('data-name');

  if (productName in resumeItems) {
      // Ne décrémente que si la valeur est supérieure à 0
      if (resumeItems[productName] >= 1) resumeItems[productName] -= 1;
  }
  showList(resumeItems);
}
