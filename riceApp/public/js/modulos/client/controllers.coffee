################################################################################
IndexCrtl = ($scope, rcClientIndex, $modal) ->
	return false


MenuCrtl = ($scope, rcClientIndex, $modal) ->

	$scope.categorias = {}
	$scope.productos = {}
	$scope.itemsCarro = {}
	$scope.nElementsCarro = 0

	getCategorias = ()->
		rcClientIndex.getCategorias {}, (r)->
			$scope.categorias = r.data

	getProductos = ()->
		rcClientIndex.getProductos {}, (r)->
			$scope.productos = r.data

	$scope.AddItemToCart = (pro)->
		if(pro.especificacion.length)
			modalInstance = $modal.open
				templateUrl: 'seleccionEspecificacion.html'
				controller: 'ModalInstanceCrtl'
				size: "md"
				backdrop: 'static'
				resolve:
					data: ()->
						{
							especificaciones : pro.especificacion
						}
			.result.then(
				(resultado)->
					$scope.addItem(pro, resultado)
		
				(close)->
					#Msg.Info("Accion cancelada")
				)

		else
			$scope.addItem(pro, '')

	$scope.addItem = (pro, resultado)->
		if($scope.itemsCarro[pro.id])
			$scope.itemsCarro[pro.id].cantidad++;
			$scope.itemsCarro[pro.id].especificacionSeleccionada = resultado;
		else
			$scope.itemsCarro[pro.id] = pro
			$scope.itemsCarro[pro.id].cantidad = 1
			$scope.itemsCarro[pro.id].especificacionSeleccionada = resultado;
		# });
		$scope.nElementsCarro = Object.keys($scope.itemsCarro).length;

	$scope.confirmarCompra = ()->
		console.log($scope.itemsCarro)
		modalInstance = $modal.open
			templateUrl: 'resumen.html'
			controller: 'ModalInstanceCrtl'
			size: "md"
			backdrop: 'static'
			resolve:
				data: ()->
					{
						resumen : $scope.itemsCarro
					}
		.result.then(
			(resultado)->
					
			(close)->
				#Msg.Info("Accion cancelada")
			)

	getProductos()
	getCategorias()

ModalInstanceCrtl = ($scope, $modalInstance, data)->
	$scope.data = data

	$scope.aceptar = (responce = true)->
		$modalInstance.close responce
	$scope.cancelar = ()->
		$modalInstance.dismiss('cancel')


micontroller = ($scope)->
	console.log("asdas")
