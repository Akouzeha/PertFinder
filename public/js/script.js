function myFunction() {
  var x = document.getElementById("myNavBar");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}

//show and hide chart form
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('chartForm');
  var toggleButton = document.getElementById('toggleFormButton');

  // Add a click event listener to the button
  toggleButton.addEventListener('click', function() {
      // Toggle the form visibility
      form.style.display = (form.style.display === 'none') ? 'block' : 'none';
  });
});

document.addEventListener("DOMContentLoaded", function() {
  // Add click event listener for "Modifier" links
  var editLinks = document.querySelectorAll('.edit-comment-link');
  editLinks.forEach(function(link) {
      link.addEventListener('click', function(event) {
          event.preventDefault();
          var commentId = link.dataset.commentId;
          toggleCommentEdit(commentId);
      });
  });

  // Function to toggle the visibility of comment content and edit form
  function toggleCommentEdit(commentId) {
      var contentDiv = document.getElementById('comment-content-' + commentId);
      var editDiv = document.getElementById('comment-edit-' + commentId);

      if (contentDiv && editDiv) {
          contentDiv.style.display = 'none';
          editDiv.style.display = 'block';
      }
  }
});  