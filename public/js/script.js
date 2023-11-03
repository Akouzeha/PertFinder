// Get the current page's URL from Symfony's path function
var currentPageURL = "{{ app.request.attributes.get('_route') }}";

// Find the corresponding link and add the 'active' class
var navLinks = document.querySelectorAll('.navItem a');
for (var i = 0; i < navLinks.length; i++) {
  if (navLinks[i].getAttribute('href') === currentPageURL) {
    navLinks[i].parentElement.classList.add('activated');
  }
}
