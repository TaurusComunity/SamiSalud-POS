<?php
require_once 'models/productsModel.php';
require_once 'models/categoryModel.php';
require_once 'models/actividadModel.php';

class Products extends SessionController {
    protected $user;

    public function __construct() {
        parent::__construct();
        $this->user = $this->getUserSessionData();
    }

    public function render() {
        $this->view->render('admin/drogueriaAdmin.php', [
            'user' => $this->user
        ]);
    }


    public function newProduct() {
        if (!$this->existPOST(['nombre', 'id_categoria', 'precio', 'precio_neto', 'icui', 'iva', 'stock', 'codigo_barras', 'fechaVencimiento'])) {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_CAMPOS_VACIOS_PRODUCTO]);
            return;
        }
    
        try {
            $product = new ProductsModel();
            $product->setNombre($this->getPOST('nombre'));
            $product->setId_Categoria($this->getPOST('id_categoria'));
            $product->setPrecio_Neto((float)$this->getPOST('precio_neto'));
            $product->setPrecio((float)$this->getPOST('precio'));
            $product->setIva($this->getPOST('iva'));
            $product->setIcui($this->getPOST('icui'));
            $product->setStock($this->getPOST('stock'));
            $product->setCodigo_barras($this->getPOST('codigo_barras'));
            $product->setLote($this->getPOST('lote') ?? null);
            $product->setFechaVencimiento($this->getPOST('fechaVencimiento'));
            $product->setDistribuidor($this->getPOST('distribuidor') ?? null);
            $product->setRegistroSanitario($this->getPOST('registroSanitario') ?? null);
            $product->setId_local($this->user->getId_local());
    
            if (!$product->save()) {
                throw new Exception('Error al guardar el producto.');
            }
    
            // Registrar actividad
            $this->registrarActividad("Se creó el producto: " . $product->getNombre());
    
            $this->redirect('/categorias', ['success' => SuccessMessages::SUCCESS_CREAR_PRODUCTO]);
    
        } catch (Exception $e) {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_PRODUCTO]);
        }
    }
    
    

    public function create() {
        $category = new CategoryModel();
        $this->view->render('products/create', [
            'products' => $category->getAll(),
            'user' => $this->user
        ]);
    }

    public function delete($params) {
        if ($params == null) {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_SIN_ID_ELIMINAR_PRODUCTO]); 
            return;
        }
    
        $id = $params;
        error_log("ID recibido en el controlador: " . $id);
    
        // Obtener el nombre del producto antes de eliminarlo
        $product = $this->model->get($id); // Suponiendo que tienes un método para buscar el producto por su ID
    
        if (!$product) {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_PRODUCTO_NO_ENCONTRADO]);
            return;
        }
    
        $nombreProducto = $product->getNombre(); // Usar el método getNombre()
    
        $res = $this->model->delete($id);
    
        if ($res) {
            // Registrar actividad con el nombre del producto
            $this->registrarActividad("Se ELIMINÓ el producto con ID: $id. <br> Producto: $nombreProducto");
            $this->redirect('/categorias', ['success' => SuccessMessages::SUCCESS_ELIMINAR_PRODUCTO]); 
        } else {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_PRODUCTO]);
        }
    }
    
    
    

    public function updateProduct() {
        if ($this->existPOST(['codigo_barras', 'nombre', 'stock', 'precio_neto', 'precio', 'icui', 'iva', 'id_categoria', 'lote', 'fechaVencimiento', 'registroSanitario', 'distribuidor'])) {
    
            $codigo_barras = $this->getPOST('codigo_barras');
            $nombre = $this->getPOST('nombre');
            $stock = $this->getPOST('stock');
            $precio_neto = $this->getPOST('precio_neto');
            $precio = $this->getPOST('precio');
            $icui = $this->getPOST('icui');
            $iva = $this->getPOST('iva');
            $id_categoria = $this->getPOST('id_categoria');
            $lote = $this->getPOST('lote');
            $fechaVencimiento = $this->getPOST('fechaVencimiento');
            $registroSanitario = $this->getPOST('registroSanitario');
            $distribuidor = $this->getPOST('distribuidor');
    
            // Obtener el producto original
            $productModel = new ProductsModel();
            $originalProduct = $productModel->findByCodigoBarras($codigo_barras);
    
            // Comprobar si los campos obligatorios están vacíos
            if (empty($codigo_barras) || empty($nombre) || empty($stock) || empty($precio_neto)) {
                $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_CAMPOS_VACIOS_PRODUCTO]);
                return;
            }
    
            // Comparar valores y construir el mensaje de cambios
            $changes = [];
    
            // Comparar cada campo y registrar cambios solo si son diferentes
            if (isset($originalProduct->nombre) && $originalProduct->nombre !== $nombre) {
                $changes[] = "Nombre: {$originalProduct->nombre} → $nombre";
            }
            if (isset($originalProduct->stock) && $originalProduct->stock != $stock) {
                $changes[] = "Stock: {$originalProduct->stock} → $stock";
            }
            if (isset($originalProduct->precio_neto) && $originalProduct->precio_neto != $precio_neto) {
                $changes[] = "Precio Neto: {$originalProduct->precio_neto} → $precio_neto";
            }
            if (isset($originalProduct->precio) && $originalProduct->precio != $precio) {
                $changes[] = "Precio: {$originalProduct->precio} → $precio";
            }
            if (isset($originalProduct->iva) && $originalProduct->iva != $iva) {
                $changes[] = "IVA: {$originalProduct->iva} → $iva";
            }
            
            if (isset($originalProduct->icui) && $originalProduct->icui != $icui) {
                $changes[] = "ICUI: {$originalProduct->icui} → $icui";
            }
            if (isset($originalProduct->id_categoria) && $originalProduct->id_categoria != $id_categoria) {
                $changes[] = "Categoria: {$originalProduct->id_categoria} → $id_categoria";
            }
            if (isset($originalProduct->lote) && $originalProduct->lote !== $lote) {
                $changes[] = "Lote: {$originalProduct->lote} → $lote";
            }
            if (isset($originalProduct->fechaVencimiento) && $originalProduct->fechaVencimiento !== $fechaVencimiento) {
                $changes[] = "Fecha de Vencimiento: {$originalProduct->fechaVencimiento} → $fechaVencimiento";
            }
            if (isset($originalProduct->registroSanitario) && $originalProduct->registroSanitario !== $registroSanitario) {
                $changes[] = "Registro Sanitario: {$originalProduct->registroSanitario} → $registroSanitario";
            }
            if (isset($originalProduct->distribuidor) && $originalProduct->distribuidor !== $distribuidor) {
                $changes[] = "Distribuidor: {$originalProduct->distribuidor} → $distribuidor";
            }
    
            // Asignar nuevos valores al modelo solo si son diferentes
            if ($productModel->updateValues($codigo_barras, $nombre, $stock, $precio_neto, $precio, $iva, $icui, $id_categoria, $lote, $fechaVencimiento, $registroSanitario, $distribuidor)) {
                // Registrar actividad solo si hay cambios
                if (!empty($changes)) {
                    $changeMessage = implode("<br>", $changes); // Usar <br> para saltos de línea en HTML
                    $this->registrarActividad("Se actualizó el producto con código de barras: $codigo_barras. <br> Cambios: <br>$changeMessage");
                }
                $this->redirect('/infoID/show/' . $codigo_barras, ['success' => SuccessMessages::SUCCESS_ACTUALIZAR_PRODUCTO]);
            } else {
                $this->redirect('/infoID/show/' . $codigo_barras, ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_PRODUCTO]);
            }
        } else {
            $this->redirect('/categorias', ['error' => ErrorMessages::ERROR_PROCESAR_SOLICITUD_CREAR_PRODUCTO]);
        }
    }
    
    
    
    // Método para actualizar valores en el modelo
    public function updateValues($codigo_barras, $nombre, $stock, $precio_neto, $precio, $iva, $icui,$id_categoria, $lote, $fechaVencimiento, $registroSanitario, $distribuidor) {
        // Preparar la consulta para actualizar el producto
        $stmt = $this->db->prepare("UPDATE productos SET 
            nombre = :nombre,
            stock = :stock,
            precio_neto = :precio_neto,
            precio = :precio,
            iva = :iva,
            icui = :icui,
            id_categoria = :id_categoria,
            lote = :lote,
            fechaVencimiento = :fechaVencimiento,
            registroSanitario = :registroSanitario,
            distribuidor = :distribuidor
            WHERE codigo_barras = :codigo_barras");
    
        // Vincular los parámetros
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio_neto', $precio_neto);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':iva', $iva);
        $stmt->bindParam(':icui', $icui);
        $stmt->bindParam(':id_categoria', $id_categoria );
        $stmt->bindParam(':lote', $lote);
        $stmt->bindParam(':fechaVencimiento', $fechaVencimiento);
        $stmt->bindParam(':registroSanitario', $registroSanitario);
        $stmt->bindParam(':distribuidor', $distribuidor);
    
        // Ejecutar la consulta
        return $stmt->execute();  // Retorna true si se actualiza correctamente
    }
    
    
    
    
    function actualizarStock($id, $cantidad) {
        try {
            $db = new Database();
            $conn = $db->connect();
            $query = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando el stock: " . $e->getMessage());
        }
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
