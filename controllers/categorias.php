<?php

class Categorias extends SessionController {
    protected $user;
    
    function __construct(){
        parent::__construct();
        $this->user = $this->getUserSessionData();
        $this->loadModel('products');  // Cargamos el modelo de productos
        error_log('Categorias::construct -> Inicio del controlador categorias');
    }

    function render() {
        error_log('Categorias::render -> Cargando vista de categorias');
        $user = $this->getUserSessionData(); // Obtén los datos del usuario logueado
        
        // Obtener el conteo de productos por categoría filtrado por local
        $categoriesCount = $this->model->countProductsByCategory($user->getId_local());
    
        // Obtener los totales por categoría
        $totalesPorCategoria = $this->obtenerTotalesPorCategoria(); 
    
        // Pasar los datos a la vista
        $this->view->render('admin/categoryAdmin', [ 
            'user' => $user,
            'categoriesCount' => $categoriesCount,
            'totalesPorCategoria' => $totalesPorCategoria // Pasar los totales a la vista
        ]);
    }
    

    public function obtenerTotalesPorCategoria() {
        $db = new Database();
        $conn = $db->connect();
    
        $query = "SELECT 
    c.nombre AS categoria, 
    COUNT(p.id) AS total_productos, 
    SUM(p.precio * p.stock) AS total_categoria
FROM 
    categorias c
LEFT JOIN 
    productos p ON c.id = p.id_categoria
GROUP BY 
    c.nombre;";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $resultados;  // Retornar los resultados
    }
}