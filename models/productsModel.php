<?php

class ProductsModel extends Model implements IModel
{
    private $id;
    private $nombre;
    private $id_categoria;
    private $id_local;  // Local asignado automáticamente
    private $precio_neto;
    private $precio;
    private $iva;
    private $icui;
    private $stock;
    private $codigo_barras;
    private $lote;
    private $fechaVencimiento;
    private $distribuidor;
    private $registroSanitario;
    private $fecha_Creacion;
    private $fecha_Actualizacion;

    public function __construct()
    {
        parent::__construct();
        $this->id = "";
        $this->nombre = "";
        $this->id_categoria = "";
        $this->id_local = ""; // Se asignará dinámicamente
        $this->precio_neto = "";
        $this->precio = "";
        $this->iva = "";
        $this->icui = "";
        $this->stock = "";
        $this->codigo_barras = "";
        $this->lote = "";
        $this->fechaVencimiento = "";
        $this->distribuidor = "";
        $this->registroSanitario = "";
        $this->fecha_Creacion = "";
        $this->fecha_Actualizacion = "";
    }
    // Guardar producto
    public function save()
    {
        $this->fecha_Creacion = date("Y-m-d H:i:s");
        $this->fecha_Actualizacion = date("Y-m-d H:i:s");

        try {
            // Iniciar la transacción
            $this->db->beginTransaction();

            // Insertar en la tabla productos
            $queryProducto = $this->prepare('
                INSERT INTO productos 
                (nombre, id_categoria, precio_neto, icui, precio, iva, stock, codigo_barras, fecha_Creacion, fecha_Actualizacion, id_local) 
                VALUES(:nombre, :id_categoria, :precio_neto, :icui, :precio, :iva, :stock, :codigo_barras, :fecha_Creacion, :fecha_Actualizacion, :id_local)'
            );

            $queryProducto->execute([
                'nombre' => $this->nombre,
                'id_categoria' => $this->id_categoria,
                'precio_neto' => $this->precio_neto,
                'icui' => $this->icui,
                'precio' => $this->precio - 1,
                'iva' => $this->iva,
                'stock' => $this->stock,
                'codigo_barras' => $this->codigo_barras,
                'fecha_Creacion' => $this->fecha_Creacion,
                'fecha_Actualizacion' => $this->fecha_Actualizacion,
                'id_local' => $this->id_local  // Local se asigna desde el usuario logueado
            ]);

            // Insertar en la tabla informacionProducto
            $queryInfoProducto = $this->prepare('
                INSERT INTO informacionProducto 
                (codigo_barras, lote, fechaVencimiento, distribuidor, registroSanitario, fecha_Creacion) 
                VALUES(:codigo_barras, :lote, :fechaVencimiento, :distribuidor, :registroSanitario, :fecha_Creacion)'
            );

            $queryInfoProducto->execute([
                'codigo_barras' => $this->codigo_barras,
                'lote' => $this->lote,
                'fechaVencimiento' => $this->fechaVencimiento,
                'distribuidor' => $this->distribuidor,
                'registroSanitario' => $this->registroSanitario,
                'fecha_Creacion' => $this->fecha_Creacion
            ]);

            // Confirmar la transacción
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    public function actualizarStock($id_producto, $cantidad) {
        try {
            $query = "UPDATE productos SET stock = stock + :cantidad WHERE id = :id_producto";
            $stmt = $this->prepare($query);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt->execute();
            return true; // Retorna true si la operación fue exitosa
        } catch (PDOException $e) {
            error_log("Error actualizando stock en ProductsModel: " . $e->getMessage());
            return false; // Retorna false si ocurrió un error
        }
    }
    // Obtener la información básica del producto
    public function getProductById($productId)
    {
        try {
            $query = $this->db->connect()->prepare("SELECT * FROM productos WHERE id = :id");
            $query->execute(['id' => $productId]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ProductsModel::getProductById -> ' . $e->getMessage());
            return false;
        }
    }

    // Obtener la información adicional del producto
    public function getProductInfoById($productId)
    {
        try {
            $query = $this->db->connect()->prepare("SELECT * FROM informacionProducto WHERE id_producto = :id");
            $query->execute(['id' => $productId]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ProductsModel::getProductInfoById -> ' . $e->getMessage());
            return false;
        }
    }
    public function delete($id)
{
    try {
        // Desvincular el producto de la tabla detallesfactura (establecer la relación como NULL)
        $query = $this->prepare('UPDATE detallefacturas SET id_producto = NULL WHERE id_producto = :id');
        $query->execute(['id' => $id]);

        // Eliminar el producto de la tabla productos
        $query = $this->prepare('DELETE FROM productos WHERE id = :id');
        $query->execute(['id' => $id]);

        if ($query->rowCount() > 0) {
            error_log("Producto con ID $id eliminado correctamente.");
            return true;
        } else {
            error_log("No se eliminó ningún producto con ID $id.");
            return false;
        }
    } catch (PDOException $e) {
        error_log("Error al eliminar el producto con ID $id: " . $e->getMessage());
        return false;
    }
}


    // Actualizar producto
    public function update()
    {
        $this->fecha_Actualizacion = date("Y-m-d H:i:s");

        try {
            $this->db->beginTransaction();

            // Actualizar en tabla productos
            $queryProducto = $this->prepare('
                UPDATE productos 
                SET nombre = :nombre, stock = :stock, precio_neto = :precio_neto, precio = :precio, iva = :iva, icui = :icui, fecha_Actualizacion = :fecha_Actualizacion 
                WHERE codigo_barras = :codigo_barras AND id_local = :id_local'
            );

            $queryProducto->execute([
                'nombre' => $this->nombre,
                'stock' => $this->stock,
                'precio_neto' => $this->precio_neto,
                'precio' => $this->precio - 1,
                'icui' => $this->icui,
                'iva' => $this->iva,
                'fecha_Actualizacion' => $this->fecha_Actualizacion,
                'codigo_barras' => $this->codigo_barras,
                'id_local' => $this->id_local // Solo actualiza si coincide con el local del usuario
            ]);

            // Actualizar en tabla informacionProducto
            $queryInfoProducto = $this->prepare('
                UPDATE informacionProducto 
                SET lote = :lote, fechaVencimiento = :fechaVencimiento, registroSanitario = :registroSanitario, distribuidor = :distribuidor 
                WHERE codigo_barras = :codigo_barras'
            );

            $queryInfoProducto->execute([
                'lote' => $this->lote,
                'fechaVencimiento' => $this->fechaVencimiento,
                'registroSanitario' => $this->registroSanitario,
                'distribuidor' => $this->distribuidor,
                'codigo_barras' => $this->codigo_barras
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('ProductsModel::update -> ' . $e);
            return false;
        }
    }

    public function from($array)
    {
        $this->id = $array['id'];
        $this->nombre = $array['nombre'];
        $this->id_categoria = $array['id_categoria'];
        $this->precio_neto = $array['precio_neto'];
        $this->precio = $array['precio'];
        $this->iva = $array['iva'];
        $this->icui = $array['icui'];
        $this->stock = $array['stock'];
        $this->codigo_barras = $array['codigo_barras'];
        $this->fecha_Actualizacion = $array['fecha_Actualizacion'];
        $this->fecha_Creacion = $array['fecha_Creacion'];
        $this->id_local = $array['id_local'];
    }
    // Dentro de tu clase ProductsModel
public function getNombreGlobal() {
    return $this->nombre; // Asegúrate de que 'nombre' sea la propiedad correcta
}
    public function get($id)
    {
        try {
            $query = $this->prepare("SELECT * FROM productos WHERE id = :id");
            $query->execute(['id' => $id]);

            $producto = $query->fetch(PDO::FETCH_ASSOC);

            $this->setId($producto["id"]);
            $this->setNombre($producto["nombre"]);
            $this->setId_Categoria($producto["id_categoria"]);
            $this->setPrecio_Neto($producto["precio_neto"]);
            $this->setPrecio($producto["precio"]);
            $this->setIva($producto["iva"]);
            $this->setIcui($producto["icui"]);
            $this->setStock($producto["stock"]);
            $this->setCodigo_barras($producto["stock"]);
            $this->setFecha_Creacion($producto["fecha_Creacion"]);
            $this->setFecha_Actualizacion($producto["fecha_Actualizacion"]);
            $this->setId_local($producto["id_local"]);


            return $this;

        } catch (PDOException $e) {
            error_log("models/userModel ::getID -> PDOException " . $e);
            return false;
        }
    }
    public function getAll(){}
    public function getByLocal($id_local)
    {
        try {
            // Consulta para seleccionar los productos del local específico
            $query = $this->prepare('SELECT * FROM productos WHERE id_local = :id_local');
            $query->execute(['id_local' => $id_local]);

            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            return $results; // Devuelve la lista de productos
        } catch (PDOException $e) {
            error_log('ProductsModel::getByLocal -> ' . $e);
            return [];
        }
    }
    public function getAllByCategoryAndLocal($id_categoria, $id_local)
    {
        try {
            // Consulta para seleccionar productos por categoría y local
            $query = $this->prepare('SELECT * FROM productos WHERE id_categoria = :id_categoria AND id_local = :id_local');
            $query->execute(['id_categoria' => $id_categoria, 'id_local' => $id_local]);

            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            error_log('ProductsModel::getAllByCategoryAndLocal -> ' . $e);
            return [];
        }
    }

    public function getAllByCategory($id_categoria)
    {
        $items = [];
        try {
            $query = $this->prepare('SELECT * FROM productos where id_categoria = :id_categoria');

            $query->execute([
                'id_categoria' => $id_categoria,

            ]);


            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = [
                    'id' => $row['id'],
                    'codigo_barras' => $row['codigo_barras'],
                    'nombre' => $row['nombre'],
                    'stock' => $row['stock'],
                    'icui' => $row['icui'],
                    'iva' => $row['iva'],
                    'precio_neto' => $row['precio_neto'],
                    'precio' => $row['precio'],
                ];
                array_push($items, $item);
            }

            return $items;


        } catch (PDOException $e) {
            return [];
        }
    }

    public function countProductsByCategory($id_local)
    {
        try {
            $query = $this->prepare('
                SELECT c.nombre AS categoria, COUNT(p.id) AS total 
                FROM categorias c
                LEFT JOIN productos p ON p.id_categoria = c.id AND p.id_local = :id_local
                GROUP BY c.id
            ');

            $query->execute(['id_local' => $id_local]);

            return $query->fetchAll(PDO::FETCH_ASSOC); // Retorna los resultados como un array asociativo
        } catch (PDOException $e) {
            error_log('ProductsModel::countProductsByCategory -> ' . $e);
            return [];
        }
    }

    // Buscar producto por código de barras
    public function getProductByBarcode($codigo_barras){
        try {
            $query = $this->prepare("SELECT * FROM productos WHERE codigo_barras = :codigo_barras");
            $query->execute(['codigo_barras' => $codigo_barras]);
            $product = $query->fetch(PDO::FETCH_ASSOC);

            return $product ?: false; // Devuelve falso si no existe el producto
        } catch (PDOException $e) {
            error_log('ProductModel::getProductByBarcode -> PDOException: ' . $e);
            return false;
        }
    }

    // Guardar factura en tabla facturas físicas
    public function saveFacturaFisica($data){
        try {
            $query = $this->prepare("INSERT INTO facturasfisicas (productos, total, fecha, id_cliente, id_local) VALUES (:productos, :total, :fecha, :id_usuario, :id_local)");
            $query->execute($data);
            return true;
        } catch (PDOException $e) {
            error_log('ProductModel::saveFacturaFisica -> PDOException: ' . $e);
            return false;
        }
    }

    // Guardar factura en tabla facturas electrónicas
    public function saveFacturaElectronica($data){
        try {
            $query = $this->prepare("INSERT INTO facturasElectronicas (productos, total, fecha, id_usuario, id_local) VALUES (:productos, :total, :fecha, :id_usuario, :id_local)");
            $query->execute($data);
            return true;
        } catch (PDOException $e) {
            error_log('ProductModel::saveFacturaElectronica -> PDOException: ' . $e);
            return false;
        }
    }


    // Obtener información del producto por código de barras
    public function getProductByCode($codigo_barras)
    {
        // Reemplaza esta consulta con la correcta según tu estructura de base de datos
        $stmt = $this->prepare("SELECT * FROM productos WHERE codigo_barras = :codigo_barras");
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Debería retornar un arreglo asociativo o false si no se encuentra
    }

    public function getProductInfoByCode($codigo_barras)
    {
        // Similar a la anterior, asegúrate de que esta consulta también esté correcta
        $stmt = $this->prepare("SELECT * FROM informacionProducto WHERE codigo_barras = :codigo_barras");
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Debería retornar un arreglo asociativo o false si no se encuentra
    }

    public function findByCodigoBarras($codigo_barras) {
        // Asumiendo que tienes acceso a la conexión a la base de datos
        $query = "SELECT * FROM productos WHERE codigo_barras = :codigo_barras";
        $stmt = $this->prepare($query);
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->execute();
    
        // Obtener el resultado como un objeto
        return $stmt->fetchObject();
    }

    public function updateValues($codigo_barras, $nombre, $stock, $precio_neto, $precio, $iva, $icui, $id_categoria, $lote, $fechaVencimiento, $registroSanitario, $distribuidor) {
        // Actualizar solo los campos de la tabla productos
        $stmt = $this->prepare("UPDATE productos SET 
            nombre = :nombre,
            stock = :stock,
            precio_neto = :precio_neto,
            precio = :precio,
            iva = :iva,
            icui = :icui,
            id_categoria = :id_categoria
            WHERE codigo_barras = :codigo_barras");
    
        // Vincular los parámetros
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio_neto', $precio_neto);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':iva', $iva);
        $stmt->bindParam(':icui', $icui);
        $stmt->bindParam(':id_categoria', $id_categoria);
    
        // Ejecutar la consulta y verificar el resultado
        $result = $stmt->execute();
    
        // Ahora actualizar los campos en la tabla informacionproducto
        $stmt = $this->prepare("UPDATE informacionproducto SET 
            lote = :lote,
            fechaVencimiento = :fechaVencimiento,
            registroSanitario = :registroSanitario,
            distribuidor = :distribuidor
            WHERE codigo_barras = :codigo_barras"); // Asegúrate de tener la relación bien definida
    
        // Vincular los parámetros para la segunda consulta
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->bindParam(':lote', $lote);
        $stmt->bindParam(':fechaVencimiento', $fechaVencimiento);
        $stmt->bindParam(':registroSanitario', $registroSanitario);
        $stmt->bindParam(':distribuidor', $distribuidor);
    
        // Ejecutar la segunda consulta y verificar el resultado
        $result &= $stmt->execute(); // Usar AND para verificar que ambas consultas se ejecuten correctamente
    
        return $result; // Retorna verdadero si ambas actualizaciones fueron exitosas
    }
    


    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getId_Categoria()
    {
        return $this->id_categoria;
    }
    public function getId_local()
    {
        return $this->id_local;
    }
    public function getPrecio()
    {
        return $this->precio;
    }
    public function getPrecio_Neto()
    {
        return $this->precio_neto;
    }
    public function getIva()
    {
        return $this->iva;
    }
    public function getIcui()
    {
        return $this->icui;
    }
    public function getStock()
    {
        return $this->stock;
    }
    public function getCodigo_barras()
    {
        return $this->codigo_barras;
    }
    public function getfecha_Creacion()
    {
        return $this->fecha_Creacion;
    }
    public function getfecha_Actualizacion()
    {
        return $this->fecha_Actualizacion;
    }
    public function getLote()
    {
        return $this->lote;
    }
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }
    public function getRegistroSanitario()
    {
        return $this->registroSanitario;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function setId_Categoria($id_categoria)
    {
        $this->id_categoria = $id_categoria;
    }
    public function setId_local($id_local)
    {
        $this->id_local = $id_local;
    }
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function setPrecio_Neto($precio_neto)
    {
        $this->precio_neto = $precio_neto;
    }
    public function setIva($iva)
    {
        $this->iva = $iva;
    }
    public function setIcui($icui)
    {
        $this->icui = $icui;
    }
    public function setStock($stock)
    {
        $this->stock = $stock;
    }
    public function setCodigo_barras($codigo_barras)
    {
        $this->codigo_barras = $codigo_barras;
    }
    public function setFecha_Creacion($fecha_Creacion)
    {
        $this->fecha_Creacion = $fecha_Creacion;
    }
    public function setfecha_Actualizacion($fecha_Actualizacion)
    {
        $this->fecha_Actualizacion = $fecha_Actualizacion;
    }
    public function setLote($lote)
    {
        $this->lote = $lote;
    }
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;
    }
    public function setDistribuidor($distribuidor)
    {
        $this->distribuidor = $distribuidor;
    }
    public function setRegistroSanitario($registroSanitario)
    {
        $this->registroSanitario = $registroSanitario;
    }
}
