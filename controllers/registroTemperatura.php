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
        $registroTemperatura = $controlCalidadModel->getAllRegistro_temperaturas();
        
        // Obtén los datos del usuario logueado
        $user = $this->getUserSessionData(); 
      

        $this->view->render('admin/registroTemperaturaAdmin', [
            'user' => $user,
            'registroTemperatura' => $registroTemperatura,
        ]);
    }
    public function newTemperatura(){
        if(!$this->existPOST(['temperatura', 'manana', 'tarde'])){
            $this->redirect('/registroTemperatura', ['error' => ErrorMessages::ERROR_CAMPOS_VACIOS_CONTROL_CALIDAD]);

            return;
        }
    
    
        try {
            // Crear 
            $registroTemperatura = new QualityControlModel();
            $registroTemperatura->setTemperatura($this->getPOST('temperatura'));
            $registroTemperatura->setmanana($this->getPOST('manana'));
            $registroTemperatura->setTarde($this->getPOST('tarde'));
            $registroTemperatura->setId_local($this->user->getId_local());  // Añadir local según el usuario logueado
            $registroTemperatura->setId_usuario($this->user->getId());      // Usuario logueado que registra la factura
        
    
            // Guardar 
            if(!$registroTemperatura->saveRegistro_temperatura()){
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
        $registroTemperatura = $registroTemperaturaModel->getAllRegistro_temperaturas();
    
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
