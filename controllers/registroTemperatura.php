<?php
require_once 'models/qualityControlModel.php';

class RegistroTemperatura extends SessionController {
    protected $user;
  
    function __construct(){
        parent::__construct();
        $this->user = $this->getUserSessionData(); // Asegurarse de inicializar la sesión del usuario
        error_log('Admin::construct -> Inicio del controlador registro temperaturas');
    }

    function render(){
        error_log('registro temperaturas::render -> Cargando vista de registro temperaturas');
        
        $controlCalidadModel = new QualityControlModel();
        $registroTemperatura = $controlCalidadModel->getAll();
        
        // Obtén los datos del usuario logueado
        $user = $this->getUserSessionData(); 
      

        $this->view->render('admin/registroTemperaturaAdmin', [
            'user' => $user,
            'registroTemperatura' => $registroTemperatura,
        ]);
    }
    public function newFactura(){
        if(!$this->existPOST(['numero_factura', 'proveedor', 'total_producto', 'total_faltantes', 'total_devolucion'])){
            $this->redirect('/registroTemperatura', ['error' => ErrorMessages::ERROR_CAMPOS_VACIOS_CONTROL_CALIDAD]);

            return;
        }
    
    
        try {
            // Crear 
            $registroFactura = new QualityControlModel();
            $registroFactura->setNumero_factura($this->getPOST('numero_factura'));
            $registroFactura->setProveedor($this->getPOST('proveedor'));
            $registroFactura->setTotal_productos($this->getPOST('total_producto'));
            $registroFactura->setTotal_faltantes($this->getPOST('total_faltantes'));
            $registroFactura->setTotal_devoluciones($this->getPOST('total_devolucion'));
            $registroFactura->setId_local($this->user->getId_local());  // Añadir local según el usuario logueado
            $registroFactura->setId_usuario($this->user->getId());      // Usuario logueado que registra la factura
        
    
            // Guardar 
            if(!$registroFactura->save()){
                throw new Exception('Error al registrar la factura.');
            }
    
            // Redirigir con mensaje de éxito
            $this->redirect('/registroTemperatura', ['success' => SuccessMessages::SUCCESS_CREAR_CONTROL_CALIDAD]);
    
        } catch (Exception $e) {
            $this->redirect('/registroTemperatura', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_CONTROL_CALIDAD]);
        }
    }
    
    public function create(){
        $this->user = $this->getUserSessionData(); // A
        $registroTemperaturaModel = new QualityControlModel();
        $registroTemperatura = $registroTemperaturaModel->getAll();
    
        // Log para verificar si se obtuvieron proveedores
        error_log('Registro de facturas encontrados: ' . print_r($registroTemperatura, true));
    
        // Asegurarse de que los proveedores se están pasando correctamente a la vista
        $this->view->render('registroTemperatura/create',[
            'registroTemperatura' => $registroTemperatura,
            'user' => $this->user
        ]);
    }


    public function delete($params){
        if ($params == null) {
            $this->redirect('/registroTemperatura', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_ELIMINAR_CONTROL_CALIDAD]); 
            return;
        }
    
        $id = $params;
        error_log("ID recibido en el controlador: " . $id); // Agregar log para verificar qué valor se recibe
    
        $res = $this->model->delete($id);
    
        if($res){
            $this->redirect('/registroTemperatura',  ['success' => SuccessMessages::SUCCESS_ELIMINAR_CONTROL_CALIDAD]); 
        }else{
            $this->redirect('/registroTemperatura', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_CONTROL_CALIDAD]);
        }
    }

}
