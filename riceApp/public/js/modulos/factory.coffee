ConfiguracionesComunes = ()->
	configuracionesComunes = {}
	
	configuracionesComunes.tablaElementosPorPagina = { count: 10 }
	configuracionesComunes.tablaPaginador = [10, 50, 100]

	return configuracionesComunes

MensajeSmallBox = ()->
	
	msg = {}

	time = 4000

	msg.Info = (mensaje)->
		$.smallBox {
			title : "Información!",
			content : mensaje,
			color : "#3276B1",
			iconSmall : "fa fa-bell swing animated",
			timeout : time
		}

	msg.Success = (mensaje = "Cambios realizados")->
		$.smallBox {
			title : "Acción realizada con éxito!",
			content : mensaje,
			color : "#739E73",
			iconSmall : "fa fa-check",
			timeout : time
		}

	msg.Error = (mensaje = "Ha ocurrido un errer inesperado")->
		$.smallBox {
			title : "Error!",
			content : mensaje,
			color : "#C46A69",
			iconSmall : "fa fa-warning shake animated",
			timeout : time
		}				
	
	msg.Warning = (mensaje)->
		$.smallBox {
			title : "Atención!",
			content : mensaje,
			color : "#C79121",
			iconSmall : "fa fa-shield fadeInLeft animated",
			timeout : time
		}
	return msg