function myFunction() {
  var x = document.getElementById("myNavBar");
  if (x.className === "topnav") {
      x.className += " responsive";
  } else {
      x.className = "topnav";
  }
}

/* //show and hide chart form
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('chartForm');
  var toggleButton = document.getElementById('toggleFormButton');

  // Add a click event listener to the button
  toggleButton.addEventListener('click', function() {
      // Toggle the form visibility
      form.style.display = (form.style.display === 'none') ? 'block' : 'none';
  });
}); */

