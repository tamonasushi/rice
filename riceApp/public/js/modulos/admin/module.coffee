app = angular.module "riceApp", [ "ngAnimate",  "ui.bootstrap",  "ngResource", "angular-loading-bar", "ngTable", "ngFileUpload" ]

############################### CONTROLLERS ####################################
app.controller "EstadisticasCrtl", ["$scope", "rcEstadisticas", "MensajeSmallBox","$uibModal" , EstadisticasCrtl]
app.controller "OrdenesCrtl", ["$scope", "rcOrdenes","NgTableParams","ConfiguracionesComunes","MensajeSmallBox","$uibModal" , OrdenesCrtl]
app.controller "ReportesCrtl", ["$scope","rcReportes","NgTableParams","ConfiguracionesComunes","MensajeSmallBox", "$uibModal" ,ReportesCrtl]
app.controller "CategoriasCrtl", ["$scope", "rcCategorias","NgTableParams","ConfiguracionesComunes","MensajeSmallBox", "$uibModal" ,CategoriasCrtl]
app.controller "EspecificacionesCrtl", ["$scope", "rcEspecificacion","NgTableParams","ConfiguracionesComunes","MensajeSmallBox", "$uibModal" ,EspecificacionesCrtl]
app.controller "ClientesCrtl", ["$scope", "rcClientes","NgTableParams","ConfiguracionesComunes","MensajeSmallBox", "$uibModal" ,ClientesCrtl]
app.controller "ProductosCrtl", ["$scope", "rcProductos","rcCategorias","rcEspecificacion","NgTableParams","ConfiguracionesComunes","MensajeSmallBox", "$uibModal", ProductosCrtl]
app.controller "SectoresCrtl", ["$scope", "rcSectores","NgTableParams","ConfiguracionesComunes","MensajeSmallBox",'$uibModal', SectoresCrtl]
app.controller "UsuariosCrtl", ["$scope", "rcUsuarios","NgTableParams","ConfiguracionesComunes","MensajeSmallBox", "$uibModal" ,UsuariosCrtl]
app.controller "ProfileCrtl", ["$scope", "rcProfile","MensajeSmallBox", "$uibModal", ProfileCrtl]
app.controller "ModalInstanceCrtl", ["$scope", "MensajeSmallBox", "$uibModalInstance","Upload","data", ModalInstanceCrtl]

################################ RESOURCES #####################################
app.factory 'rcClientes', ['$resource',rcClientes]
app.factory 'rcEstadisticas', ['$resource', rcEstadisticas]
app.factory 'rcOrdenes', ['$resource', rcOrdenes]
app.factory 'rcReportes', ['$resource', rcReportes]
app.factory 'rcCategorias', ['$resource', rcCategorias]
app.factory 'rcProductos', ['$resource', rcProductos]
app.factory 'rcEspecificacion', ['$resource', rcEspecificacion]
app.factory 'rcSectores', ['$resource', rcSectores]
app.factory 'rcUsuarios', ['$resource', rcUsuarios]
app.factory 'rcProfile', ['$resource', rcProfile]

###########################  CONFIGURACIONES  ##################################
loadingBar = (cfpLoadingBarProvider)->
	cfpLoadingBarProvider.includeSpinner = false;
	cfpLoadingBarProvider.includeBar = true;
	cfpLoadingBarProvider.loadingBarTemplate = '''
	<div id="loadingHTTP">
		<div class="spinnerCenterLoad">
			<img src="/img/32.gif" alt="">
		</div>
	</div>''';
app.config ['cfpLoadingBarProvider', loadingBar]


################################# FACTORY ######################################
app.factory "ConfiguracionesComunes",	[ ConfiguracionesComunes ]
app.factory "MensajeSmallBox",	[ MensajeSmallBox ]