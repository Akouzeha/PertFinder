

function myFunction() {
  var x = document.getElementById("myNavList");
  if (x.className === "nav-list") {
      x.className += " responsive";
  } else {
      x.className = "nav-list";
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

setTimeout(function() {
  // Récupère le 1er élément ayant la classe message
  message = document.getElementsByClassName('alert')[0]
  if (message != undefined) {
      message.style.display = 'none';
  }
}, 3975);


