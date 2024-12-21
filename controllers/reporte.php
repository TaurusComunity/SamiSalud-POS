<?php
require_once 'models/reporteModel.php';

class Reporte extends SessionController {

    protected $user;
    protected $reporteModel;

    function __construct() {
        parent::__construct();
        $this->user = $this->getUserSessionData();
        $this->reporteModel = new ReporteModel(); // Crea instancia del modelo
        error_log('reporte::construct -> Inicio del controlador reporte');
    }

    function render() {
        error_log('reporte::render -> Cargando vista de reporte');
        
        $fecha = date('Y-m-d'); // Obtener la fecha actual
        $ventasDelDia = $this->reporteModel->obtenerVentasDelDia($fecha);
        $contadoresPago = $this->reporteModel->contarMetodosPago($fecha);
        $mercanciaInicial = $this->reporteModel->obtenerMercanciaInicial();
        $mercanciaIntervalo = $this->reporteModel->obtenerMercanciaEnIntervalo($ventasDelDia);
        $ganancias = $this->calcularGanancias($mercanciaInicial, $mercanciaIntervalo);

        $this->view->render('admin/reporteAdmin', [
            'user' => $this->user,
            'ventasDelDia' => $ventasDelDia,
            'contadoresPago' => $contadoresPago,
            'mercanciaInicial' => $mercanciaInicial,
            'mercanciaIntervalo' => $mercanciaIntervalo,
            'ganancias' => $ganancias,
            'fecha' => $fecha,
        ]);
    }

    private function calcularGanancias($mercanciaInicial, $mercanciaIntervalo) {
        $ganancias = [];
        foreach ($mercanciaInicial as $categoria => $valorInicial) {
            $valorIntervalo = $mercanciaIntervalo[$categoria] ?? 0;
            $ganancias[$categoria] = $valorInicial - $valorIntervalo; // Ganancia = Inicial - Intervalo
        }
        return $ganancias;
    }
}
