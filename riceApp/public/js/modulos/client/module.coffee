app = angular.module "riceClient", [ "ngAnimate",  "ui.bootstrap",  "ngResource", "angular-loading-bar" ]

############################### CONTROLLERS ####################################
app.controller "IndexCrtl", ["$scope", "rcClientIndex", "$uibModal" , IndexCrtl]
app.controller "MenuCrtl", ["$scope", "rcClientIndex", "$uibModal" , MenuCrtl]
app.controller "ModalInstanceCrtl", ["$scope", "$uibModalInstance", "data", ModalInstanceCrtl]
app.controller "micontroller", ["$scope", micontroller]

################################ RESOURCES #####################################
app.factory 'rcClientIndex', ['$resource',rcClientIndex]

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