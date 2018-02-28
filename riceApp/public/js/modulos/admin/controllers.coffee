################################################################################
EstadisticasCrtl = ($scope, rcEstadisticas,Msg, $modal) ->
	
	$scope.dtHasta = new Date()
	$scope.dtDesde = new Date($scope.dtHasta.getTime() - (60*60*24*7*1000))

	getEstadisticas = ()->

		$scope.ventasSemana = false

		rcEstadisticas.get {iniDate: $scope.dtDesde, endDate: $scope.dtHasta}, (r)->
			if(r.data.ventasSemana)
				categorias = []
				montos = []
				for k,x of r.data.ventasSemana
					categorias.push(x.fecha)
					montos.push(parseInt(x.total))
				Highcharts.chart 'container',
					chart: type: 'line'
					title: text: 'Ventas de la ultima semana'
					subtitle: text: '(ultimos 7 dias)'
					xAxis: categories: categorias
					yAxis: title: text: 'Pesos'
					plotOptions: line:
						dataLabels: enabled: true
						enableMouseTracking: false
					series: [ { name: 'Ventas',	data: montos } ]
		,(error)->
			Msg.Error()
	getEstadisticas()



################################################################################
OrdenesCrtl = ($scope, rcOrdenes, NgTableParams, cC, Msg, $modal) ->

	$scope.format = 'yyyy/MM/dd';
	$scope.dtHasta = new Date()
	$scope.dtDesde = new Date($scope.dtHasta.getTime() - (60*60*24*7*1000))

	inicializeDatePickers = ->
		$scope.dateOptionsDesde =
			'year-format': '\'yy\''
			'starting-day': 1
			'show-weeks': false
			'show-button-bar': false
			'maxDate': new Date()

		$scope.dateOptionsHasta =
			'year-format': '\'yy\''
			'starting-day': 1
			'show-weeks': false
			'show-button-bar': false
			'maxDate': new Date()	
			'minDate': $scope.dtDesde	

	$scope.changeFecha = ()->
		inicializeDatePickers()
		getOrdenes()


	inicializeDatePickers()

	getOrdenes = ()->
		rcOrdenes.get {iniDate: $scope.dtDesde, endDate: $scope.dtHasta}, (r)->
			if(r.success is true && (r.data.length))
				$scope.tableData = r.data
				$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
			else
				Msg.Info("Sin datos")
				$scope.tableParams = new NgTableParams();
		,(error)->
			Msg.Error()
	getOrdenes()

	#Cada 5 minutos reviso si hay ordenes nuevas
	setInterval ()->
		getOrdenes()
	,1000*60*5

	$scope.verDetalle = (id)->
		rcOrdenes.get {id : id}, (r)->
			if(r.success is true && (r.data.length))
				modalInstance = $modal.open
					templateUrl: 'verDetallePedido.html'
					controller: 'ModalInstanceCrtl'
					size: "md"
					backdrop: 'static'
					resolve:
						data: ()->
							{
								detalle : r.data[0]
							}
				.result.then(
					(resultado)->

						rcOrdenes.statusChange {id : id, estado:resultado}, (r)->
							if(r.success is true)
								for key, v of $scope.tableData
									if(v.id is id)
										$scope.tableData[key].estado = r.data.estado
								Msg.Success()
							else
								Msg.Error("No se ha podido cambiar el estado asociado")
						
					(close)->
						Msg.Info("Accion cancelada")
					)
			else
				Msg.Info("Sin datos")	

	$scope.despachar = (id)->
		rcOrdenes.statusChange {id : id, estado:4}, (r)->
			if(r.success is true)
				for key, v of $scope.tableData
					if(v.id is id)
						$scope.tableData[key].estado = r.data.estado
				Msg.Success()
			else
				Msg.Error("No se ha podido cambiar el estado asociado")


################################################################################
ReportesCrtl = ($scope,rcReportes,NgTableParams, cC,Msg, $modal) ->

	inicializeDatePickers = ->
		$scope.dateOptionsDesde =
			'year-format': '\'yy\''
			'starting-day': 1
			'show-weeks': false
			'show-button-bar': false
			'maxDate': new Date()

		$scope.dateOptionsHasta =
			'year-format': '\'yy\''
			'starting-day': 1
			'show-weeks': false
			'show-button-bar': false
			'maxDate': new Date()	
			'minDate': $scope.dtDesde

	$scope.format = 'yyyy/MM/dd';
	$scope.dtHasta = new Date()
	$scope.dtDesde = new Date($scope.dtHasta.getTime() - (60*60*24*7*1000))

	getPedidos = ()->
		rcReportes.get {iniDate: $scope.dtDesde, endDate: $scope.dtHasta}, (r)->
			if(r.success is true && (r.data.length))
				$scope.tableData = r.data
				$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
			else
				Msg.Info("Sin datos")
				$scope.tableParams = new NgTableParams();
		,(error)->
			Msg.Error()
	getPedidos()

	$scope.changeFecha = ()->
		inicializeDatePickers()
		getPedidos()

################################################################################
CategoriasCrtl = ($scope, rcCategorias, NgTableParams, cC, Msg, $modal) ->
	rcCategorias.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.tableData = r.data
			$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
		else
			Msg.Info("Sin datos")
	,(error)->
		Msg.Error()

	$scope.statusChange = (mov, id)->
		modalInstance = $modal.open
			templateUrl: '/admin/modal-confirmar'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{}
		.result.then(
			(resultado)->
				rcCategorias.statusChange {mov: mov, id: id}, (r)->
					if(r.success is true)
						for key, v of $scope.tableData
							if(v.id is id)
								$scope.tableData[key].activo = r.data.activo
						Msg.Success()
					else
						Msg.Error("No se ha podido cambiar el estado asociado")
				,(error)->
					Msg.Error()
			(close)->
				Msg.Info("Accion cancelada")
			)

	$scope.editar = (id)->
		rcCategorias.get {id: id}, (r)->
			if(r.success is true)
				modal(r)
			else
				Msg.Info("Sin datos")
		,(error)->
			Msg.Error()

	$scope.nuevo = ()->
		modal({}) 

	modal = (r)->
		modalInstance = $modal.open
			templateUrl: 'editarCategoria.html'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{
						categoria : r.data
					}
		.result.then(
			(resultado)->
				rcCategorias.edit resultado, (rr)->
					if(rr.success is true)
						existe = true
						for key, v of $scope.tableData
							if(v.id is rr.data.id)
								existe = false
								$scope.tableData[key] = rr.data
						if(existe)
							$scope.tableData.push(rr.data)
						
						$scope.tableParams.reload();
						Msg.Success()
					else
						Msg.Error("Error al actualizar")
			(close)->
				Msg.Info("Accion cancelada")
			)

################################################################################
EspecificacionesCrtl = ($scope, rcEspecificacion, NgTableParams, cC, Msg, $modal) ->
	rcEspecificacion.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.tableData = r.data
			$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
		else
			Msg.Info("Sin datos")
	,(error)->
		Msg.Error()

	$scope.statusChange = (mov, id)->
		modalInstance = $modal.open
			templateUrl: '/admin/modal-confirmar'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{}
		.result.then(
			(resultado)->
				rcEspecificacion.statusChange {mov: mov, id: id}, (r)->
					if(r.success is true)
						for key, v of $scope.tableData
							if(v.id is id)
								$scope.tableData[key].activo = r.data.activo
						Msg.Success()
					else
						Msg.Error("No se ha podido cambiar el estado asociado")
				,(error)->
					Msg.Error()
			(close)->
				Msg.Info("Accion cancelada")
			)

	$scope.editar = (id)->
		rcEspecificacion.get {id: id}, (r)->
			if(r.success is true)
				modal(r)
			else
				Msg.Info("Sin datos")
		,(error)->
			Msg.Error()

	$scope.nuevo = ()->
		modal({}) 

	modal = (r)->
		modalInstance = $modal.open
			templateUrl: 'editarEspecificacion.html'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{
						especificacion : r.data
					}
		.result.then(
			(resultado)->
				rcEspecificacion.edit resultado, (rr)->
					if(rr.success is true)
						existe = true
						for key, v of $scope.tableData
							if(v.id is rr.data.id)
								existe = false
								$scope.tableData[key] = rr.data
						if(existe)
							$scope.tableData.push(rr.data)
						
						$scope.tableParams.reload();
						Msg.Success()
					else
						Msg.Error("Error al actualizar")
			(close)->
				Msg.Info("Accion cancelada")
			)


################################################################################
ClientesCrtl = ($scope, rcClientes, NgTableParams, cC, Msg, $modal) ->
	rcClientes.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.tableData = r.data
			$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
		else
			Msg.Info("Sin datos")
	,(error)->
		Msg.Error()

################################################################################
ProductosCrtl = ($scope, rcProductos,rcCategorias,rcEspecificacion, NgTableParams, cC, Msg, $modal) ->

	rcCategorias.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.categorias = r.data
		else
			Msg.Info("Error obteniendo las categorias")
	,(error)->
		Msg.Error()

	rcEspecificacion.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.especificacion = r.data
		else
			Msg.Info("Error obteniendo las especificaciones")
	,(error)->
		Msg.Error()

	rcProductos.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.tableData = r.data
			$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
		else
			Msg.Info("Sin datos")
	,(error)->
		Msg.Error()

	$scope.statusChange = (mov, id, k)->
		modalInstance = $modal.open
			templateUrl: '/admin/modal-confirmar'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{}
		.result.then(
			(resultado)->
				rcProductos.statusChange {mov: mov, id: id}, (r)->
					if(r.success is true)
						for key, v of $scope.tableData
							if(v.id is id)
								$scope.tableData[key].activo = r.data.activo
						Msg.Success()
					else
						Msg.Error("No se ha podido cambiar el estado asociado")
				,(error)->
					Msg.Error()
			(close)->
				Msg.Info("Accion cancelada")
			)

	$scope.nuevo = ()->
		modal({}) 

	$scope.editar = (id)->
		rcProductos.get {id: id}, (r)->
			if(r.success is true)
				if(r.data.especificacion)
					r.data.especificacionesSeleccionadas = {}
					for k, v of r.data.especificacion
						r.data.especificacionesSeleccionadas[v.id] = true
				modal(r) 
			else
				Msg.Info("Sin datos")
		,(error)->
			Msg.Error()

	modal = (r)->


		modalInstance = $modal.open
			templateUrl: 'editarProducto.html'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{
						producto : r.data
						categorias : $scope.categorias
						especificacion : $scope.especificacion
					}
		.result.then(
			(resultado)->
				rcProductos.edit resultado.producto, (rr)->
					if(rr.success is true)
						existe = true
						for key, v of $scope.tableData
							if(v.id is rr.data.id)
								existe = false
								$scope.tableData[key] = rr.data
						if(existe)
							$scope.tableData.push(rr.data)
						
						$scope.tableParams.reload();
						Msg.Success()
					else
						Msg.Error("Error al actualizar")
			(close)->
				Msg.Info("Accion cancelada")
			)

################################################################################
SectoresCrtl = ($scope, rcSectores, NgTableParams, cC, Msg, $modal) ->
	rcSectores.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.tableData = r.data
			$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
		else
			Msg.Info("Sin datos")
	,(error)->
		Msg.Error()

	$scope.statusChange = (mov, id)->
		modalInstance = $modal.open
			templateUrl: '/admin/modal-confirmar'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{}
		.result.then(
			(resultado)->
				rcSectores.statusChange {mov: mov, id: id}, (r)->
					if(r.success is true)
						for key, v of $scope.tableData
							if(v.id is id)
								$scope.tableData[key].activo = r.data.activo
						Msg.Success()
					else
						Msg.Error("No se ha podido cambiar el estado asociado")
				,(error)->
					Msg.Error()
			(close)->
				Msg.Info("Accion cancelada")
			)

	$scope.editar = (id)->
		rcSectores.get {id: id}, (r)->
			if(r.success is true)
				modal(r)
			else
				Msg.Info("Sin datos")
		,(error)->
			Msg.Error()

	$scope.nuevo = ()->
		modal({}) 

	modal = (r)->
		modalInstance = $modal.open
			templateUrl: 'editarSector.html'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{
						sector : r.data
					}
		.result.then(
			(resultado)->
				rcSectores.edit resultado, (rr)->
					if(rr.success is true)
						existe = true
						for key, v of $scope.tableData
							if(v.id is rr.data.id)
								existe = false
								$scope.tableData[key] = rr.data
						if(existe)
							$scope.tableData.push(rr.data)
						
						$scope.tableParams.reload();
						Msg.Success()
					else
						Msg.Error("Error al actualizar")
			(close)->
				Msg.Info("Accion cancelada")
			)


################################################################################
UsuariosCrtl = ($scope, rcUsuarios, NgTableParams, cC, Msg, $modal) ->
	rcUsuarios.get {}, (r)->
		if(r.success is true && (r.data.length))
			$scope.tableData = r.data
			$scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {counts: cC.tablaPaginador, dataset: $scope.tableData});
		else
			Msg.Info("Sin datos")
	,(error)->
		Msg.Error()

################################################################################
ProfileCrtl = ($scope, rcProfile, Msg, $modal) ->
	rcProfile.get {}, (r)->
		console.log "ok"
	,(error)->
		Msg.Error()

################################################################################
ModalInstanceCrtl = ($scope, Msg, $modalInstance, Upload, data)->
	
	$scope.data = data

	$scope.aceptar = (responce = true)->
		$modalInstance.close responce
	$scope.cancelar = ()->
		$modalInstance.dismiss('cancel')

	$scope.upload = (file)->

		if($scope.data.producto)
			d = {
				file: file, 
				'id_producto': $scope.data.producto.id
			}
		else
			d = {
				file: file
			}

		Upload.upload(
			{
				url: '/admin/upload-img-producto',
				data: d
			}
		)
		.then(
			(resp)->
				$scope.data.producto.img = resp.data.path
				console.log($scope.data.producto.img)
			,(error)->
				#console.log('Error status: ' + error.status)
				Msg.Error("Error al actualizar")
				return
			,(evt)->
				progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
				console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
		)