
//   $("form[name='user'").on("submit", function (event) {
//     event.preventDefault();
//     console.log("form user submitted");
//   });
$("form[name='registration'").on("submit", function (e) {
    $(".sucess").remove();
  var $form = $(e.currentTarget);
  $.ajax({
    type: "POST",
    url: "/fgo/index",
    data: $form.serialize(),
    dataType: "json",
    encode: true,
  }).done(function (data) {
    console.log(data);

    if(!data.success){
        $form.append('<p>Some errors occured, try again</p>');
    }
    else{
        $form.append('<p class="success alert alert-success">' + data.msg + '</p>')
    }
  });
});
