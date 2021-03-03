/**
 * Ne prend en charge que la création de catégorie.
 * Dans le cas d'une édition, on souhaite rediriger l'utilisateur sur la catégorie en question
 */

let createButton = $('#category-create');
let editButton = $('#category-edit');


/**
 * On doit séparer en deux requêtes Ajax l'envoi du formulaire, puisque pour les fichiers, les données processData et contentType
 * sont différentes.
 * 1. On envoie la requête avec toutes les données sauf les photos.
 * 2. Le produit est créé en base de données, on récupère son ID dans la réponse.
 * 3. On utilise l'ID pour renvoyer une requête avec les bonnes entêtes pour les fichiers et on met à jour le produit
 * 4. On réinitialise le formulaire
 */
createButton.click((e) => {
  e.preventDefault();
  
  let data = {
    title: $('#category_title').val(),
    description: $('#category_description').val(),
  }

  $.ajax({
    url: "/api/category/create",
    method: "POST",
    data: JSON.stringify(data),
    success: function(response) {
      $('form[name=category]')[0].reset();
      successToast("Le formulaire a été validé. Si vous le souhaitez, vous pouvez insérer une nouvelle catégorie");
    },
    error: function (result, status, error) {
      dangerToast("Le formulaire n'a pas été envoyé, il n'a pas été rempli correctement");
      console.log(result.responseText);
    }
  })
})