function myFunction() {
  var x = document.getElementById("myNavBar");
  if (x.className === "topnav") {
      x.className += " responsive";
  } else {
      x.className = "topnav";
  }
}

function toggleProjects() {
  var projectList = document.getElementById('projectList');
  projectList.style.display = (projectList.style.display === 'none') ? 'block' : 'none';
}

function toggleComments() {
  var commentList = document.getElementById('commentList');
  commentList.style.display = (commentList.style.display === 'none') ? 'block' : 'none';
}
function togglePropos() {
  var aboutMe = document.getElementById('aboutMe');
  aboutMe.style.display = (aboutMe.style.display === 'none') ? 'block' : 'none';
}

function toggleModifAboutMe() {
  var aboutMe = document.getElementById('modifAboutMe');
  aboutMe.style.display = (aboutMe.style.display === 'none') ? 'block' : 'none';
}

/* document.addEventListener('DOMContentLoaded', function () {
  var alertMessages = document.querySelector('.alert-messages');

  if (alertMessages) {
      setTimeout(function () {
          alertMessages.classList.add('hide-after-3s');
      }, 3000);
  }
}); */

setTimeout(function() {
  // Récupère le 1er élément ayant la classe message
  message = document.getElementsByClassName('alert')[0]
  if (message != undefined) {
      message.style.display = 'none';
  }
}, 3975);

