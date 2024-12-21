<?php 
$user = isset($this->data['user']) ? $this->data['user'] : null;
$ventasDelDia = $this->data['ventasDelDia'] ?? [];
$contadoresPago = $this->data['contadoresPago'] ?? [];
$mercanciaInicial = $this->data['mercanciaInicial'] ?? [];
$mercanciaIntervalo = $this->data['mercanciaIntervalo'] ?? [];
$ganancias = $this->data['ganancias'] ?? [];
$fecha = $this->data['fecha'] ?? date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Reporte de Ventas</title>
</head>
<body>
    <h2>Reporte Diario</h2>
    <p><?php echo date('Y-m-d H:i:s'); ?></p>
    <p>Empleado: <?php echo $user->getNombre(); ?></p>
    <p>Local: <?php echo $user->getId_local(); ?></p>
    <hr>

    <h3>Mercancía Inicial</h3>
    <ul>
        <?php foreach ($mercanciaInicial as $categoria => $valor): ?>
            <li><?php echo $categoria . ": $" . number_format($valor, 2); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Mercancía en Intervalo</h3>
    <ul>
        <?php foreach ($mercanciaIntervalo as $categoria => $valor): ?>
            <li><?php echo $categoria . ": $" . number_format($valor, 2); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Ganancias</h3>
    <ul>
        <?php foreach ($ganancias as $categoria => $ganancia): ?>
            <li><?php echo $categoria . ": $" . number_format($ganancia, 2); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Contadores de Métodos de Pago</h3>
    <table>
        <thead>
            <tr>
                <th>Método de pago</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contadoresPago as $pago): ?>
                <tr>
                    <td><?php echo $pago['metodo_pago']; ?></td>
                    <td><?php echo $pago['cantidad']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Dinero por Método de Pago</h3>
    <table>
        <thead>
            <tr>
                <th>Método de pago</th>
                <th>Dinero</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contadoresPago as $pago): ?>
                <tr>
                    <td><?php echo $pago['metodo_pago']; ?></td>
                    <td><?php echo number_format($pago['total'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total</h3>
    <p>
        <?php 
        $total = array_sum(array_column($contadoresPago, 'total'));
        echo "Total: $" . number_format($total, 2); 
        ?>
    </p>

    <button onclick="window.print()">Imprimir Reporte</button>
</body>
</html>
