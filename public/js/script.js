var asc = document.querySelectorAll(".btn-asc");
var costume = document.querySelectorAll(".btn-costume");
var skill = document.querySelectorAll(".btn-skill");
var noblePhantasm = document.querySelectorAll(".btn-np")

for (i = 0; i < asc.length; ++i) {
  asc[i].onclick = function () {
    var selectedBtn = this.id;
    var selectedAscension = selectedBtn.replace("btn-", "");
    var selectedQuery = ".asc:not(#" + selectedAscension + ")";
    var ascensions = document.querySelectorAll(selectedQuery);
    var costumes = document.querySelectorAll(".costume");

    document.getElementById(selectedAscension).classList.add("is-visible");
    for (j = 0; j < ascensions.length; j++) {
      ascensions[j].classList.remove("is-visible");
    }
    for (j2 = 0; j2 < costumes.length; j2++) {
      costumes[j2].classList.remove("is-visible");
    }
  };
}

for (k = 0; k < costume.length; ++k) {
  costume[k].onclick = function () {
    var selectedBtn = this.id;
    var selectedAscension = selectedBtn.replace("btn-", "");
    var selectedQuery = ".costume:not(#" + selectedAscension + ")";
    var costumes = document.querySelectorAll(selectedQuery);
    var ascensions = document.querySelectorAll(".asc");

    document.getElementById(selectedAscension).classList.add("is-visible");
    for (l = 0; l < costumes.length; l++) {
      costumes[l].classList.remove("is-visible");
    }
    for (l2 = 0; l2 < ascensions.length; l2++) {
      ascensions[l2].classList.remove("is-visible");
    }
  };
}

for (n = 0; n < skill.length; ++n) {
  skill[n].onclick = function () {
    var selectedBtn = this.id;
    var selectedSkill = selectedBtn.replace("btn-", "");
    var selectedQuery = ".table-skills:not(#" + selectedSkill + ")";
    var skills = document.querySelectorAll(selectedQuery);
    
    document.getElementById(selectedSkill).classList.add("table-is-visible");
    for (nb = 0; nb < skills.length; nb++) {
      skills[nb].classList.remove("table-is-visible");
    }
    
  };
}

for(np = 0; np < noblePhantasm.length; np++){
  noblePhantasm[np].onclick = function () {
    var selectedBtn = this.id;
    var selectedNp = selectedBtn.replace("btn-", "");
    var selectedQuery = ".table-np:not(#" + selectedNp + ")";
    var noble = document.querySelectorAll(selectedQuery);
    document.getElementById(selectedNp).classList.add("table-is-visible");
    for (npb = 0; npb < noble.length; npb++) {
      noble[npb].classList.remove("table-is-visible");
    }
  };
}