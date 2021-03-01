$(function() {
  let $vichInputs = $('.vich-image input[type="file"]');

  const fileMapper = () => {
    $vichInputs = $('.vich-image input[type="file"]');
    $.map($vichInputs, function (input, key) {
      let id = $(input).attr('id');
      let label = $(input).parent().parent().children("label");
      $(label).attr('for', id);
    }) 
  }

  const imageUploadListener = () => {
      $($vichInputs).change((e) => {
      let label = $(e.currentTarget).parent().parent().children("label")
      $(label).text("Photo mise en file d'attente !");
      $(label).css('border', "1px solid #68C068").css('color', "#68C068");
    })
  }
  

  fileMapper();
  imageUploadListener();
  

  $('.add-another-collection-widget').click(function (e) {
      let list = $($(this).attr('data-list-selector'));
      // Try to find the counter of the list or use the length of the list
      let counter = list.data('widget-counter') | list.children().length;

      // grab the prototype template
      let newWidget = list.attr('data-prototype');
      // replace the "__name__" used in the id and name of the prototype
      // with a number that's unique to your emails
      // end name attribute looks like name="contact[emails][2]"
      newWidget = newWidget.replace(/__name__/g, counter);
      // Increase the counter
      counter++;
      // And store it, the length cannot be used if deleting widgets is allowed
      list.data('widget-counter', counter);

      // create a new list element and add it to the list
      let newElem = $(list.attr('data-widget-tags')).html(newWidget);
      newElem.appendTo(list);

      fileMapper();
      imageUploadListener();
  });

});