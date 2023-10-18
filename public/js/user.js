var btnPicture = document.getElementById("btn-picture");
var divPicture = document.getElementById("profile-picture");
var pictures = document.querySelectorAll(".picture");
var submitPicture = document.getElementById("submit-picture");

btnPicture.onclick = function () {
  if (divPicture.classList.contains("is-visible")) {
    divPicture.classList.remove("is-visible");
    submitPicture.classList.remove("is-visible");
  } else {
    divPicture.classList.add("is-visible");
    submitPicture.classList.add("is-visible");
  }
};

var pictureUrl = "";
for (i = 0; i < pictures.length; i++) {
  pictures[i].onclick = function () {
    pictureUrl = this.src;
    this.classList.add("selected-picture");
    for (j = 0; j < pictures.length; j++) {
      if (pictures[j] != this) {
        pictures[j].classList.remove("selected-picture");
      }
    }
  };
}

$("form[name='pp'").on("submit", function (e) {
  $.ajax({
    url: "/fgo/user/profile",
    type: "POST",
    dataType: "JSON",
    data: {
      url: pictureUrl,
    },
    success: function(data) {
        console.log(data);
     }
  });
});
