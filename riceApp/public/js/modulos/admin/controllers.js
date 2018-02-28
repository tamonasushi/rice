// Generated by CoffeeScript 1.12.4
var CategoriasCrtl, ClientesCrtl, EspecificacionesCrtl, EstadisticasCrtl, ModalInstanceCrtl, OrdenesCrtl, ProductosCrtl, ProfileCrtl, ReportesCrtl, SectoresCrtl, UsuariosCrtl;

EstadisticasCrtl = function($scope, rcEstadisticas, Msg, $modal) {
  var getEstadisticas;
  $scope.dtHasta = new Date();
  $scope.dtDesde = new Date($scope.dtHasta.getTime() - (60 * 60 * 24 * 7 * 1000));
  getEstadisticas = function() {
    $scope.ventasSemana = false;
    return rcEstadisticas.get({
      iniDate: $scope.dtDesde,
      endDate: $scope.dtHasta
    }, function(r) {
      var categorias, k, montos, ref, x;
      if (r.data.ventasSemana) {
        categorias = [];
        montos = [];
        ref = r.data.ventasSemana;
        for (k in ref) {
          x = ref[k];
          categorias.push(x.fecha);
          montos.push(parseInt(x.total));
        }
        return Highcharts.chart('container', {
          chart: {
            type: 'line'
          },
          title: {
            text: 'Ventas de la ultima semana'
          },
          subtitle: {
            text: '(ultimos 7 dias)'
          },
          xAxis: {
            categories: categorias
          },
          yAxis: {
            title: {
              text: 'Pesos'
            }
          },
          plotOptions: {
            line: {
              dataLabels: {
                enabled: true
              },
              enableMouseTracking: false
            }
          },
          series: [
            {
              name: 'Ventas',
              data: montos
            }
          ]
        });
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  return getEstadisticas();
};

OrdenesCrtl = function($scope, rcOrdenes, NgTableParams, cC, Msg, $modal) {
  var getOrdenes, inicializeDatePickers;
  $scope.format = 'yyyy/MM/dd';
  $scope.dtHasta = new Date();
  $scope.dtDesde = new Date($scope.dtHasta.getTime() - (60 * 60 * 24 * 7 * 1000));
  inicializeDatePickers = function() {
    $scope.dateOptionsDesde = {
      'year-format': '\'yy\'',
      'starting-day': 1,
      'show-weeks': false,
      'show-button-bar': false,
      'maxDate': new Date()
    };
    return $scope.dateOptionsHasta = {
      'year-format': '\'yy\'',
      'starting-day': 1,
      'show-weeks': false,
      'show-button-bar': false,
      'maxDate': new Date(),
      'minDate': $scope.dtDesde
    };
  };
  $scope.changeFecha = function() {
    inicializeDatePickers();
    return getOrdenes();
  };
  inicializeDatePickers();
  getOrdenes = function() {
    return rcOrdenes.get({
      iniDate: $scope.dtDesde,
      endDate: $scope.dtHasta
    }, function(r) {
      if (r.success === true && r.data.length) {
        $scope.tableData = r.data;
        return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
          counts: cC.tablaPaginador,
          dataset: $scope.tableData
        });
      } else {
        Msg.Info("Sin datos");
        return $scope.tableParams = new NgTableParams();
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  getOrdenes();
  setInterval(function() {
    return getOrdenes();
  }, 1000 * 60 * 5);
  $scope.verDetalle = function(id) {
    return rcOrdenes.get({
      id: id
    }, function(r) {
      var modalInstance;
      if (r.success === true && r.data.length) {
        return modalInstance = $modal.open({
          templateUrl: 'verDetallePedido.html',
          controller: 'ModalInstanceCrtl',
          size: "md",
          backdrop: 'static',
          resolve: {
            data: function() {
              return {
                detalle: r.data[0]
              };
            }
          }
        }).result.then(function(resultado) {
          return rcOrdenes.statusChange({
            id: id,
            estado: resultado
          }, function(r) {
            var key, ref, v;
            if (r.success === true) {
              ref = $scope.tableData;
              for (key in ref) {
                v = ref[key];
                if (v.id === id) {
                  $scope.tableData[key].estado = r.data.estado;
                }
              }
              return Msg.Success();
            } else {
              return Msg.Error("No se ha podido cambiar el estado asociado");
            }
          });
        }, function(close) {
          return Msg.Info("Accion cancelada");
        });
      } else {
        return Msg.Info("Sin datos");
      }
    });
  };
  return $scope.despachar = function(id) {
    return rcOrdenes.statusChange({
      id: id,
      estado: 4
    }, function(r) {
      var key, ref, v;
      if (r.success === true) {
        ref = $scope.tableData;
        for (key in ref) {
          v = ref[key];
          if (v.id === id) {
            $scope.tableData[key].estado = r.data.estado;
          }
        }
        return Msg.Success();
      } else {
        return Msg.Error("No se ha podido cambiar el estado asociado");
      }
    });
  };
};

ReportesCrtl = function($scope, rcReportes, NgTableParams, cC, Msg, $modal) {
  var getPedidos, inicializeDatePickers;
  inicializeDatePickers = function() {
    $scope.dateOptionsDesde = {
      'year-format': '\'yy\'',
      'starting-day': 1,
      'show-weeks': false,
      'show-button-bar': false,
      'maxDate': new Date()
    };
    return $scope.dateOptionsHasta = {
      'year-format': '\'yy\'',
      'starting-day': 1,
      'show-weeks': false,
      'show-button-bar': false,
      'maxDate': new Date(),
      'minDate': $scope.dtDesde
    };
  };
  $scope.format = 'yyyy/MM/dd';
  $scope.dtHasta = new Date();
  $scope.dtDesde = new Date($scope.dtHasta.getTime() - (60 * 60 * 24 * 7 * 1000));
  getPedidos = function() {
    return rcReportes.get({
      iniDate: $scope.dtDesde,
      endDate: $scope.dtHasta
    }, function(r) {
      if (r.success === true && r.data.length) {
        $scope.tableData = r.data;
        return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
          counts: cC.tablaPaginador,
          dataset: $scope.tableData
        });
      } else {
        Msg.Info("Sin datos");
        return $scope.tableParams = new NgTableParams();
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  getPedidos();
  return $scope.changeFecha = function() {
    inicializeDatePickers();
    return getPedidos();
  };
};

CategoriasCrtl = function($scope, rcCategorias, NgTableParams, cC, Msg, $modal) {
  var modal;
  rcCategorias.get({}, function(r) {
    if (r.success === true && r.data.length) {
      $scope.tableData = r.data;
      return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
        counts: cC.tablaPaginador,
        dataset: $scope.tableData
      });
    } else {
      return Msg.Info("Sin datos");
    }
  }, function(error) {
    return Msg.Error();
  });
  $scope.statusChange = function(mov, id) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: '/admin/modal-confirmar',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {};
        }
      }
    }).result.then(function(resultado) {
      return rcCategorias.statusChange({
        mov: mov,
        id: id
      }, function(r) {
        var key, ref, v;
        if (r.success === true) {
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === id) {
              $scope.tableData[key].activo = r.data.activo;
            }
          }
          return Msg.Success();
        } else {
          return Msg.Error("No se ha podido cambiar el estado asociado");
        }
      }, function(error) {
        return Msg.Error();
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
  $scope.editar = function(id) {
    return rcCategorias.get({
      id: id
    }, function(r) {
      if (r.success === true) {
        return modal(r);
      } else {
        return Msg.Info("Sin datos");
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  $scope.nuevo = function() {
    return modal({});
  };
  return modal = function(r) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: 'editarCategoria.html',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {
            categoria: r.data
          };
        }
      }
    }).result.then(function(resultado) {
      return rcCategorias.edit(resultado, function(rr) {
        var existe, key, ref, v;
        if (rr.success === true) {
          existe = true;
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === rr.data.id) {
              existe = false;
              $scope.tableData[key] = rr.data;
            }
          }
          if (existe) {
            $scope.tableData.push(rr.data);
          }
          $scope.tableParams.reload();
          return Msg.Success();
        } else {
          return Msg.Error("Error al actualizar");
        }
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
};

EspecificacionesCrtl = function($scope, rcEspecificacion, NgTableParams, cC, Msg, $modal) {
  var modal;
  rcEspecificacion.get({}, function(r) {
    if (r.success === true && r.data.length) {
      $scope.tableData = r.data;
      return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
        counts: cC.tablaPaginador,
        dataset: $scope.tableData
      });
    } else {
      return Msg.Info("Sin datos");
    }
  }, function(error) {
    return Msg.Error();
  });
  $scope.statusChange = function(mov, id) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: '/admin/modal-confirmar',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {};
        }
      }
    }).result.then(function(resultado) {
      return rcEspecificacion.statusChange({
        mov: mov,
        id: id
      }, function(r) {
        var key, ref, v;
        if (r.success === true) {
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === id) {
              $scope.tableData[key].activo = r.data.activo;
            }
          }
          return Msg.Success();
        } else {
          return Msg.Error("No se ha podido cambiar el estado asociado");
        }
      }, function(error) {
        return Msg.Error();
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
  $scope.editar = function(id) {
    return rcEspecificacion.get({
      id: id
    }, function(r) {
      if (r.success === true) {
        return modal(r);
      } else {
        return Msg.Info("Sin datos");
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  $scope.nuevo = function() {
    return modal({});
  };
  return modal = function(r) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: 'editarEspecificacion.html',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {
            especificacion: r.data
          };
        }
      }
    }).result.then(function(resultado) {
      return rcEspecificacion.edit(resultado, function(rr) {
        var existe, key, ref, v;
        if (rr.success === true) {
          existe = true;
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === rr.data.id) {
              existe = false;
              $scope.tableData[key] = rr.data;
            }
          }
          if (existe) {
            $scope.tableData.push(rr.data);
          }
          $scope.tableParams.reload();
          return Msg.Success();
        } else {
          return Msg.Error("Error al actualizar");
        }
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
};

ClientesCrtl = function($scope, rcClientes, NgTableParams, cC, Msg, $modal) {
  return rcClientes.get({}, function(r) {
    if (r.success === true && r.data.length) {
      $scope.tableData = r.data;
      return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
        counts: cC.tablaPaginador,
        dataset: $scope.tableData
      });
    } else {
      return Msg.Info("Sin datos");
    }
  }, function(error) {
    return Msg.Error();
  });
};

ProductosCrtl = function($scope, rcProductos, rcCategorias, rcEspecificacion, NgTableParams, cC, Msg, $modal) {
  var modal;
  rcCategorias.get({}, function(r) {
    if (r.success === true && r.data.length) {
      return $scope.categorias = r.data;
    } else {
      return Msg.Info("Error obteniendo las categorias");
    }
  }, function(error) {
    return Msg.Error();
  });
  rcEspecificacion.get({}, function(r) {
    if (r.success === true && r.data.length) {
      return $scope.especificacion = r.data;
    } else {
      return Msg.Info("Error obteniendo las especificaciones");
    }
  }, function(error) {
    return Msg.Error();
  });
  rcProductos.get({}, function(r) {
    if (r.success === true && r.data.length) {
      $scope.tableData = r.data;
      return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
        counts: cC.tablaPaginador,
        dataset: $scope.tableData
      });
    } else {
      return Msg.Info("Sin datos");
    }
  }, function(error) {
    return Msg.Error();
  });
  $scope.statusChange = function(mov, id, k) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: '/admin/modal-confirmar',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {};
        }
      }
    }).result.then(function(resultado) {
      return rcProductos.statusChange({
        mov: mov,
        id: id
      }, function(r) {
        var key, ref, v;
        if (r.success === true) {
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === id) {
              $scope.tableData[key].activo = r.data.activo;
            }
          }
          return Msg.Success();
        } else {
          return Msg.Error("No se ha podido cambiar el estado asociado");
        }
      }, function(error) {
        return Msg.Error();
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
  $scope.nuevo = function() {
    return modal({});
  };
  $scope.editar = function(id) {
    return rcProductos.get({
      id: id
    }, function(r) {
      var k, ref, v;
      if (r.success === true) {
        if (r.data.especificacion) {
          r.data.especificacionesSeleccionadas = {};
          ref = r.data.especificacion;
          for (k in ref) {
            v = ref[k];
            r.data.especificacionesSeleccionadas[v.id] = true;
          }
        }
        return modal(r);
      } else {
        return Msg.Info("Sin datos");
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  return modal = function(r) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: 'editarProducto.html',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {
            producto: r.data,
            categorias: $scope.categorias,
            especificacion: $scope.especificacion
          };
        }
      }
    }).result.then(function(resultado) {
      return rcProductos.edit(resultado.producto, function(rr) {
        var existe, key, ref, v;
        if (rr.success === true) {
          existe = true;
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === rr.data.id) {
              existe = false;
              $scope.tableData[key] = rr.data;
            }
          }
          if (existe) {
            $scope.tableData.push(rr.data);
          }
          $scope.tableParams.reload();
          return Msg.Success();
        } else {
          return Msg.Error("Error al actualizar");
        }
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
};

SectoresCrtl = function($scope, rcSectores, NgTableParams, cC, Msg, $modal) {
  var modal;
  rcSectores.get({}, function(r) {
    if (r.success === true && r.data.length) {
      $scope.tableData = r.data;
      return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
        counts: cC.tablaPaginador,
        dataset: $scope.tableData
      });
    } else {
      return Msg.Info("Sin datos");
    }
  }, function(error) {
    return Msg.Error();
  });
  $scope.statusChange = function(mov, id) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: '/admin/modal-confirmar',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {};
        }
      }
    }).result.then(function(resultado) {
      return rcSectores.statusChange({
        mov: mov,
        id: id
      }, function(r) {
        var key, ref, v;
        if (r.success === true) {
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === id) {
              $scope.tableData[key].activo = r.data.activo;
            }
          }
          return Msg.Success();
        } else {
          return Msg.Error("No se ha podido cambiar el estado asociado");
        }
      }, function(error) {
        return Msg.Error();
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
  $scope.editar = function(id) {
    return rcSectores.get({
      id: id
    }, function(r) {
      if (r.success === true) {
        return modal(r);
      } else {
        return Msg.Info("Sin datos");
      }
    }, function(error) {
      return Msg.Error();
    });
  };
  $scope.nuevo = function() {
    return modal({});
  };
  return modal = function(r) {
    var modalInstance;
    return modalInstance = $modal.open({
      templateUrl: 'editarSector.html',
      controller: 'ModalInstanceCrtl',
      size: "md",
      backdrop: 'static',
      resolve: {
        data: function() {
          return {
            sector: r.data
          };
        }
      }
    }).result.then(function(resultado) {
      return rcSectores.edit(resultado, function(rr) {
        var existe, key, ref, v;
        if (rr.success === true) {
          existe = true;
          ref = $scope.tableData;
          for (key in ref) {
            v = ref[key];
            if (v.id === rr.data.id) {
              existe = false;
              $scope.tableData[key] = rr.data;
            }
          }
          if (existe) {
            $scope.tableData.push(rr.data);
          }
          $scope.tableParams.reload();
          return Msg.Success();
        } else {
          return Msg.Error("Error al actualizar");
        }
      });
    }, function(close) {
      return Msg.Info("Accion cancelada");
    });
  };
};

UsuariosCrtl = function($scope, rcUsuarios, NgTableParams, cC, Msg, $modal) {
  return rcUsuarios.get({}, function(r) {
    if (r.success === true && r.data.length) {
      $scope.tableData = r.data;
      return $scope.tableParams = new NgTableParams(cC.tablaElementosPorPagina, {
        counts: cC.tablaPaginador,
        dataset: $scope.tableData
      });
    } else {
      return Msg.Info("Sin datos");
    }
  }, function(error) {
    return Msg.Error();
  });
};

ProfileCrtl = function($scope, rcProfile, Msg, $modal) {
  return rcProfile.get({}, function(r) {
    return console.log("ok");
  }, function(error) {
    return Msg.Error();
  });
};

ModalInstanceCrtl = function($scope, Msg, $modalInstance, Upload, data) {
  $scope.data = data;
  $scope.aceptar = function(responce) {
    if (responce == null) {
      responce = true;
    }
    return $modalInstance.close(responce);
  };
  $scope.cancelar = function() {
    return $modalInstance.dismiss('cancel');
  };
  return $scope.upload = function(file) {
    var d;
    if ($scope.data.producto) {
      d = {
        file: file,
        'id_producto': $scope.data.producto.id
      };
    } else {
      d = {
        file: file
      };
    }
    return Upload.upload({
      url: '/admin/upload-img-producto',
      data: d
    }).then(function(resp) {
      $scope.data.producto.img = resp.data.path;
      return console.log($scope.data.producto.img);
    }, function(error) {
      Msg.Error("Error al actualizar");
    }, function(evt) {
      var progressPercentage;
      progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
      return console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
    });
  };
};
