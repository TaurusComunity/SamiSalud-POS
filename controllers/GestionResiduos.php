<?php
require_once 'models/qualityControlModel.php';

class GestionResiduos extends SessionController {
    protected $user;
  
    function __construct(){
        parent::__construct();
        $this->user = $this->getUserSessionData(); // Asegurarse de inicializar la sesión del usuario
        error_log('Admin::construct -> Inicio del controlador Gestion residuos');
    }

    function render(){
        error_log('Gestion residuos::render -> Cargando vista de Gestion residuos');
        
        $controlCalidadModel = new QualityControlModel();
        $GestionResiduos = $controlCalidadModel->getAllGestion_residuos();
        
        // Obtén los datos del usuario logueado
        $user = $this->getUserSessionData(); 
      

        $this->view->render('admin/registroGestionResiduos', [
            'user' => $user,
            'GestionResiduos' => $GestionResiduos,
        ]);
    }
    public function newResiduo(){
        if(!$this->existPOST(['mes', 'tipo_residuo', 'cantidad'])){
            error_log('Campos POST faltantes: ' . print_r($_POST, true)); // Depurar
            $this->redirect('/GestionResiduos', ['error' => ErrorMessages::ERROR_CAMPOS_VACIOS_CONTROL_CALIDAD]);

            return;
        }
    
    
        try {
            // Crear 
            $registroResiduos = new QualityControlModel();
            $registroResiduos->setMes($this->getPOST('mes'));
            $registroResiduos->setTipo_residuo($this->getPOST('tipo_residuo'));
            $registroResiduos->setCantidad($this->getPOST('cantidad'));
            $registroResiduos->setId_local($this->user->getId_local());  // Añadir local según el usuario logueado
            $registroResiduos->setId_usuario($this->user->getId());      // Usuario logueado que registra la factura
        
    
            // Guardar 
            if(!$registroResiduos->saveGestion_residuos()){
                throw new Exception('Error al registrar la factura.');
            }
    
            // Redirigir con mensaje de éxito
            $this->redirect('/GestionResiduos', ['success' => SuccessMessages::SUCCESS_CREAR_CONTROL_CALIDAD]);
    
        } catch (Exception $e) {
            $this->redirect('/GestionResiduos', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_CONTROL_CALIDAD]);
        }
    }
    
    public function create(){
        $this->user = $this->getUserSessionData(); // A
        $GestionResiduosModel = new QualityControlModel();
        $GestionResiduos = $GestionResiduosModel->getAllGestion_residuos();
    
        // Log para verificar si se obtuvieron proveedores
        error_log('Registro de facturas encontrados: ' . print_r($GestionResiduos, true));
    
        // Asegurarse de que los proveedores se están pasando correctamente a la vista
        $this->view->render('GestionResiduos/create',[
            'GestionResiduos' => $GestionResiduos,
            'user' => $this->user
        ]);
    }


    public function delete($params){
        if ($params == null) {
            $this->redirect('/GestionResiduos', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_ELIMINAR_CONTROL_CALIDAD]); 
            return;
        }
    
        $id = $params;
        error_log("ID recibido en el controlador: " . $id); // Agregar log para verificar qué valor se recibe
    
        $res = $this->model->delete($id);
    
        if($res){
            $this->redirect('/GestionResiduos',  ['success' => SuccessMessages::SUCCESS_ELIMINAR_CONTROL_CALIDAD]); 
        }else{
            $this->redirect('/GestionResiduos', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_CONTROL_CALIDAD]);
        }
    }

}
