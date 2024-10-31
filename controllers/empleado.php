<?php

class Empleado extends SessionController {
    protected $user;

    
    function __construct(){
        parent::__construct();
        $this->user = $this->getUserSessionData();
        error_log('Empleado::construct -> Inicio del controlador Empleado');
    }

    function render(){
        error_log('Empleado::render -> Cargando vista de indexEmpleado');
        $user = $this->getUserSessionData(); // Obtén los datos del usuario
        $this->view->render('empleado/indexEmpleado', ['user' => $user]); 
    }
}