<?php
require_once 'models/productsModel.php';
require_once 'models/categoryModel.php';
require_once 'models/facturasModel.php';

class Facturador extends SessionController {
  
    protected $user;
    
    function __construct() {
        parent::__construct();
        $this->user = $this->getUserSessionData();
    }

    function render() {
        $user = $this->getUserSessionData();
        $this->view->render('admin/facturadorAdmin', [
            'user' => $user,
        ]);
    }

    function buscador() {
        if (isset($_GET['barcode'])) {
            $barcode = $_GET['barcode'];
            $db = new Database();
            $conn = $db->connect();
            $query = "SELECT id, codigo_barras, nombre, precio, stock FROM productos WHERE codigo_barras = :barcode";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':barcode', $barcode);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                $producto['cantidad'] = 1; // Asignar cantidad por defecto
                $producto['precio_total'] = $producto['precio']; // Calcular precio total
                echo json_encode($producto);
            } else {
                echo json_encode(null);
            }
        }
    }

    public function newFactura() {
        // Obtener los datos JSON del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Verificar si todos los campos necesarios están presentes
        if (!isset($data['productos'], $data['total'], $data['amountPaid'], $data['cambio'], $data['paymentMethod'])) {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_PRODUCTO]);
            return;
        }
    
        try {
            // 1. Recoger los datos del JSON
            $productos = $data['productos'];
            $total = $data['total'];
            $amountPaid = $data['amountPaid'];
            $cambio = $data['cambio'];
            $paymentMethod = $data['paymentMethod'];
    
            // 2. Verificar el stock de cada producto
            foreach ($productos as $producto) {
                $productoId = $producto['codigo_barras'];
                $cantidad = $producto['cantidad'];
    
                // Consultar el stock actual del producto
                $db = new Database();
                $conn = $db->connect();
                $query = "SELECT stock FROM productos WHERE codigo_barras = :barcode";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':barcode', $productoId);
                $stmt->execute();
                $productoEnDB = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // Verificar si el stock es suficiente
                if ($productoEnDB && $productoEnDB['stock'] <= 0) {
                    $this->redirect('/facturador', ['error' => "El producto {$producto['nombre']} no puede ser facturado porque no hay stock disponible."]);
                    return;
                }
            }
    
            // 3. Crear una instancia del modelo FacturasModel
            $factura = new FacturasModel();
    
            // 4. Establecer los datos en el modelo
            $factura->setProductos($productos);
            $factura->setTotal($total);
            $factura->setCambio($cambio);
            $factura->setMetodoPago($paymentMethod);
            $factura->setMontoPagado($amountPaid);
            $factura->setFecha(date("Y-m-d H:i:s"));
            $factura->setId_usuario($this->user->getId());
            $factura->setId_local($this->user->getId_local());
    
            // 5. Guardar la factura
            $facturaId = $factura->saveFacturaFisica();
            if ($facturaId) {
                $descripcionVenta = "Venta realizada <br> Detalles: <br>";
    
                // 6. Guardar detalles y actualizar el stock
                foreach ($productos as $producto) {
                    $productoId = $producto['codigo_barras'];
                    $cantidad = $producto['cantidad'];
                    $precioTotal = $producto['precio_total'];
    
                    // Descontar el stock
                    $factura->actualizarStock($productoId, $cantidad);
    
                    // Guardar detalle de la factura
                    $factura->guardarDetalleFactura($facturaId, $productoId, $cantidad, $precioTotal);
    
                    // Añadir detalles del producto a la descripción de la venta con el formato especificado
                    $descripcionVenta .= "Producto: {$producto['nombre']} <br> Cantidad: {$cantidad} <br> Precio Total: {$precioTotal} <br>";
        
                }
    
                // Añadir información de pago a la descripción de la venta
                $descripcionVenta .= "Total: {$total} <br> Monto Pagado: {$amountPaid} <br>";
                $descripcionVenta .= "Medio de Pago: {$paymentMethod} <br>";
    
                // Registrar la actividad de la venta
                $this->registrarActividad($descripcionVenta);
    
                $this->redirect('/facturas', ['success' => 'Factura guardada exitosamente.', 'detalles' => $descripcionVenta]);
            } else {
                $this->redirect('/categorias', ['error' => 'Error al guardar la factura.']);
            }
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            $this->redirect('/categorias', ['error' => 'Error al guardar la factura.']);
        }
    }
    
    
    
    
    
    

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit; // Asegúrate de terminar el script después de enviar la respuesta
    }

    // Método para registrar la actividad
    private function registrarActividad($descripcion) {
        try {
            // Crear instancia de la clase Database y obtener la conexión
            $db = new Database();
            $conn = $db->connect();
    
            // Preparar la consulta SQL para insertar la actividad
            $query = "INSERT INTO registro_actividad (descripcion, usuario) VALUES (:descripcion, :usuario)";
            $stmt = $conn->prepare($query);
            
            // Bind de parámetros y ejecución de la consulta
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $this->user->getNombre(), PDO::PARAM_STR);
            $stmt->execute();
    
            // Registrar en el log la actividad
            error_log("Actividad registrada: $descripcion");
    
        } catch (PDOException $e) {
            error_log("Error al registrar la actividad: " . $e->getMessage());
        }
    }
}
