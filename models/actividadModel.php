<?php

class ActividadModel extends Model {
    
    // Guardar actividad
    public function save($descripcion, $usuario) {
        $fecha = date("Y-m-d H:i:s");
        
        try {
            // Iniciar la transacción
            $this->db->beginTransaction();

            // Insertar en la tabla actividad
            $query = $this->prepare('
                INSERT INTO actividad 
                (descripcion, fecha, usuario) 
                VALUES(:descripcion, :fecha, :usuario)'
            );

            $query->execute([
                'descripcion' => $descripcion,
                'fecha' => $fecha,
                'usuario' => $usuario
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

    // Obtener todas las actividades
    public function getAll() {
        $query = $this->query('SELECT * FROM registro_actividad  ORDER BY fecha DESC');
        return $query->fetchAll();
    }
}
