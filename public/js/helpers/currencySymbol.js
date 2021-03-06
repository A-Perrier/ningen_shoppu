$(function() {

  let priceDiv = $('label[for=product_price]').parent();
  let lostEuro = $(priceDiv).contents()[3];
  $(lostEuro).remove();
  
  $(priceDiv).prepend('<span class="currency">' + lostEuro.data + '</span>')
  
});