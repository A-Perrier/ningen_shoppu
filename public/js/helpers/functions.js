/**
 * 
 * @param {Array} apiErrors 
 * @param {string} formName 
 */
const setErrors = (apiErrors, formName) => {
  $.map(apiErrors, (message, field) => {
    $('label[for=' + formName + '_' + field + ']').append('<span class="form-error"><span class="error-badge">Erreur</span>'+ message +'</span>');
  })
}