let products;

// Au chargement de la page, on veut lancer la requête en GET pour récupérer tous les products et les sauvegarder
$(document).ready(e => {
    products = getProducts();  
})

// C'est dans cette variable qu'on effectuera les recherches de filtrage
$('#searchbar').keyup(e => {
    let userInput = $(e.currentTarget).val()
    let matchResults = {};

    // Filtrage par libellé
    $.map(products, (product, index) => {
        if (product.wording.toLowerCase().includes(userInput)) {
            matchResults[index] = product;
            showFiltered(matchResults);
        }
    })
    
    if (userInput.length === 0) resetResults();
});

/**
 * Récupère les produits en AJAX
 */
const getProducts = function () {
  let products = {};

  $.ajax({
    url: '/api/product',
    method: "GET",
    async: false,
    success: (response) => {
      let parsedProducts = JSON.parse(response);
      $.map(parsedProducts, (product, index)=> {
        products[index] = product;
      });
    },
    error: (response) => {
      infoToast("Certaines données n'ont pas été récupérées, utilisation de la barre de recherche impossible")
    }
  });
  return products;
};


/**
 * Actualise la liste des résultats de la recherche
 * @param {Object} matchResults 
 */
const showFiltered = (matchResults) => {
  resetResults();

  $.map(matchResults, (product, index) => {
    $('#search-results').append(`<li data-id="${product.id}" class=\"search-result\"><a href="/product/${product.id}-${product.slug}">${product.wording}</a></li>`)
  });
}

const resetResults = () => {
  $('#search-results').children().remove();
}