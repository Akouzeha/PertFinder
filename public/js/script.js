function myFunction() {
  var x = document.getElementById("myNavBar");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}