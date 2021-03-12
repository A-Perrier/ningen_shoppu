let cartButton = $('#add-cart');

cartButton.click((e) => {
  e.preventDefault();
  let data = {
    productId: parseInt($('#product-id').val()),
    quantity: parseInt($('#quantity').val())
  };

  $.ajax({
    url: "/api/cart-add",
    method: "POST",
    data: JSON.stringify(data),
    success: function (response) {
      successToast("Le produit a été ajouté à votre panier !");
      let count = parseInt($('.cart-count').text());
      $('.cart-count').text(count + data.quantity);
    },
    error: function (response) {
      dangerToast(response.responseJSON);
    }
  })
})
