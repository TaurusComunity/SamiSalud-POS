<?php

class ReporteModel extends Model {

    public function obtenerVentasDelDia($fecha) {
        $sql = "SELECT metodo_pago, SUM(total) AS total, COUNT(*) AS cantidad
                FROM facturasfisicas
                WHERE DATE(fecha) = :fecha
                GROUP BY metodo_pago";
        
        $stmt = $this->prepare($sql);
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarMetodosPago($fecha) {
        $sql = "SELECT metodo_pago, COUNT(*) AS cantidad, SUM(total) AS total
                FROM facturasfisicas
                WHERE DATE(fecha) = :fecha
                GROUP BY metodo_pago";
        
        $stmt = $this->prepare($sql);
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMercanciaInicial() {
        // Aquí debes tener una forma de obtener el valor inicial por categoría
        // Este es solo un ejemplo de cómo podría verse
        return [
            'nevera' => 100000,
            'drogueria' => 400000,
            // Agrega más categorías según tu sistema
        ];
    }

    public function obtenerMercanciaEnIntervalo($ventas) {
        $mercancia = [];
        foreach ($ventas as $venta) {
            $categoria = $this->obtenerCategoriaPorMetodoPago($venta['metodo_pago']); // Asegúrate de definir esta función
            $mercancia[$categoria] = ($mercancia[$categoria] ?? 0) + $venta['total'];
        }
        return $mercancia;
    }

    private function obtenerCategoriaPorMetodoPago($metodo_pago) {
        // Asocia el método de pago con una categoría
        // Modifica según tu lógica
        switch ($metodo_pago) {
            case 'nequi':
                return 'nevera';
            case 'daviplata':
                return 'drogueria';
            default:
                return 'otros';
        }
    }
}
