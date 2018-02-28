// Generated by CoffeeScript 1.12.4
var enviar;

enviar = function() {
  $("#errorLogin").hide();
  return $.ajax({
    url: "/admin/set-login",
    data: {
      username: $("#username").val(),
      password: $("#password").val()
    },
    dataType: "json",
    method: "post",
    success: function(r) {
      if (r.success === true) {
        return window.location = "/admin";
      } else {
        return $("#errorLogin").show();
      }
    },
    beforeSend: function() {
      return $("#loading").show();
    },
    error: function() {
      return $("#errorLogin").show();
    },
    complete: function() {
      return $("#loading").hide();
    }
  });
};
