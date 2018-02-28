enviar = ()->
	$("#errorLogin").hide()
	$.ajax
		url : "/admin/set-login"
		data :
			username : $("#username").val()
			password : $("#password").val()
		dataType : "json"
		method : "post"
		success : (r)->
			# ...
			if(r.success is true)
				#iniciar session
				window.location = "/admin/estadistica";
			else
				#error de login
				$("#errorLogin").show()
		beforeSend: ()->
			$("#loading").show()
		error : ()->
			#error desconocido
			$("#errorLogin").show()
		complete: ()->
			$("#loading").hide()
	