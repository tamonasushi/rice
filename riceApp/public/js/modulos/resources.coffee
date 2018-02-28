rcClientes = ($resource)->
	$resource '/admin/clientes', {} , 
		get : 
			url		: '/admin/get-clientes'
			method 	: 'post'
			isArray : false

rcEstadisticas = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-estadisticas'
			method 	: 'post'
			isArray : false
rcOrdenes = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-ordenes'
			method 	: 'post'
			isArray : false
		statusChange : 
			url		: '/admin/ordenes-status-change'
			method 	: 'post'
			isArray : false
rcReportes = ($resource)->
	$resource '', {},
		get :
			url		: '/admin/get-reportes'
			method 	: 'post'
			isArray : false
rcCategorias = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-categorias'
			method 	: 'post'
			isArray : false
		statusChange : 
			url		: '/admin/categorias-status-change'
			method 	: 'post'
			isArray : false
		edit : 
			url		: '/admin/categorias-edit'
			method 	: 'post'
			isArray : false
rcProductos = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-productos'
			method 	: 'post'
			isArray : false
		statusChange : 
			url		: '/admin/productos-status-change'
			method 	: 'post'
			isArray : false
		edit : 
			url		: '/admin/productos-edit'
			method 	: 'post'
			isArray : false

rcEspecificacion = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-especificacion'
			method 	: 'post'
			isArray : false
		statusChange : 
			url		: '/admin/especificacion-status-change'
			method 	: 'post'
			isArray : false
		edit : 
			url		: '/admin/especificacion-edit'
			method 	: 'post'
			isArray : false

rcSectores = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-sectores'
			method 	: 'post'
			isArray : false
		statusChange : 
			url		: '/admin/sectores-status-change'
			method 	: 'post'
			isArray : false
		edit : 
			url		: '/admin/sectores-edit'
			method 	: 'post'
			isArray : false
rcUsuarios = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-usuarios'
			method 	: 'post'
			isArray : false
rcProfile = ($resource)->
	$resource '', {},
		get : 
			url		: '/admin/get-profile'
			method 	: 'post'
			isArray : false
rcClientIndex = ($resource)->
	$resource '', {},
		getCategorias : 
			url		: '/index/get-categorias'
			method 	: 'get'
			isArray : false
		getProductos : 
			url		: '/index/get-productos'
			method 	: 'get'
			isArray : false