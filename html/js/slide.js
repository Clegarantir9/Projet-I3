function opencloseNav() {
  if (document.getElementById("mySidebar").style.width == "0px" ){
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
  }else{
    document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
    
  }
}

var navbar = document.getElementById("navbar");


var sticky = navbar.offsetTop;

window.onscroll = function (e) { 

  if (window.pageYOffset >= sticky) {
    navbar.classList.add("sticky")
  } else {
    navbar.classList.remove("sticky");
  }
    document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0"; 
} 