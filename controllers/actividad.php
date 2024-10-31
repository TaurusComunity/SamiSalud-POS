<?php

class Actividad extends SessionController {

protected $user;

function __construct(){
    parent::__construct();
    $this->user = $this->getUserSessionData();
    error_log('actividad::construct -> Inicio del controlador actividad');
}

function render(){
    error_log('actividad::render -> Cargando vista de indexactividad');
    $user = $this->getUserSessionData(); // Obtén los datos del usuario
    $actividadModel = new ActividadModel();
    $actividades = $actividadModel->getAll(); // Obtén todas las actividades

    $this->view->render('admin/registroActividadAdmin', [
        'user' => $user,
        'actividad' => $actividades
    ]); // Pasa las actividades a la vista
}

// Método para registrar la actividad (puedes llamarlo desde el controlador de productos)
public function registrarActividad($descripcion) {
    $actividadModel = new ActividadModel();
    $usuario = $this->user->getNombre(); // Obtener nombre del usuario logueado
    return $actividadModel->save($descripcion, $usuario);
}
}
