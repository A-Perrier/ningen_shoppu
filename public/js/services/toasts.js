const successToast = (string, duration = 5000, destination = null) => {
  Toastify({
    text: string,
    duration,
    destination,
    newWindow: true,
    close: true,
    gravity: "bottom", // `top` or `bottom`
    position: "right", // `left`, `center` or `right`
    backgroundColor: "#68C068",
    stopOnFocus: true, // Prevents dismissing of toast on hover
    onClick: function(){} // Callback after click
  }).showToast();
}

const dangerToast = (string, destination = null) => {
  Toastify({
    text: string,
    duration: 5000,
    destination,
    newWindow: true,
    close: true,
    gravity: "bottom", // `top` or `bottom`
    position: "right", // `left`, `center` or `right`
    backgroundColor: "#cd0909",
    stopOnFocus: true, // Prevents dismissing of toast on hover
    onClick: function(){} // Callback after click
  }).showToast();
}