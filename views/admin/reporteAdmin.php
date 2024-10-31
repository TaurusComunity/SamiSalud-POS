<?php 
$user = isset($this->data['user']) ? $this->data['user'] : null;
$ventasDelDia = $this->data['ventasDelDia'] ?? [];
$contadoresPago = $this->data['contadoresPago'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script src="https://cdn.jsdelivr.net/npm/jsprintmanager@2.0.13/jsprintmanager.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Generador de reporte diario</title>
</head>

<body class="bg-blanco flex w-full absolute min-h-[100vh]">
    <!-- Incluir la barra lateral izquierda-->
    <?php include 'views/partials/sidebar_left.php'; ?>

    <section id="contenido" class="flex flex-col bg-azul_oscuro_opacidad w-[60%] relative rounded-3xl my-5">
        <article class="w-full pt-5 px-8">
            <h2 class="text-azul_oscuro text-[32px] font-semibold">Reporte diario</h2>
            <p class="font-medium text-[18px] -mt-2">Apartado donde podrás las ventas y ganancias, ten en cuenta que si
                no la solicitas ANTES de las 11:50 PM todos los valores se PERDERÁN.</p>
        </article>

        <article class="flex justify-between h-full flex-wrap gap-5 pt-14 pb-5 mx-8">

            <?php foreach ($contadoresPago as $pago): ?>

            <div
                class="hover:cursor-pointer hover:-translate-y-4 transition-all shadow-lg relative w-[22.5%] h-[170px] rounded-xl bg-[url('public/img/degradado.jpg')] bg-cover bg-center ">

                <!-- Mostrar el total de la categoría aquí -->
                <div class="absolute left-0 z-10 font-semibold text-white bg- p-4 text-[14px]">
                    $<?php echo number_format($pago['total'], 0); ?>
                </div>

                <!-- Mostrar el número total de productos -->
                <div class="absolute right-0 z-10 font-bold text-white p-4 text-[45px]">
                    <?php echo $pago['cantidad']; ?>
                </div>

                <!-- Nombre de la categoría -->
                <div class="absolute bottom-0 z-10 text-white p-4 text-[20px]">
                    <?php echo $pago['metodo_pago']; ?>
                </div>
            </div>
            <?php endforeach; ?>

        </article>

        <article
            class="my-8 p-5 bg-cover bg-[#0A0D2D] h-[100%] rounded-xl mx-8 flex flex-col items-start justify-center">
            <div class="flex items-center justify-between w-full">
                <h3 class="text-white text-[35px] font-medium">
                    <p>
                        <?php 
                    $total = array_sum(array_column($contadoresPago, 'total'));
                    echo "Total: $" . number_format($total, 0); 
                    ?>
                    </p>
                </h3>
                <div id="horaActual" class="text-white text-[25px] font-medium">0:0:00</div>
            </div>


            <div class="flex justify-star gap-5">


                <button onclick="imprimirReporteDiario()"
                    class="group w-12 hover:w-60 h-12 hover:bg-sky-600 relative bg-sky-700 rounded text-neutral-50 duration-700 before:duration-500 before:hover:500 font-bold flex justify-start gap-2 items-center mt-2 p-2 pr-6 before:absolute before:-z-10 before:left-8 before:hover:left-40 before:w-6 before:h-6 before:bg-sky-700 before:hover:bg-sky-600 before:rotate-45">
                    <svg xmlns="http://www.w3.org/2000/svg"  x="0" width="100" viewBox="0 0 384 512"  preserveAspectRatio="xMidYMid meet" height="100" class="w-8 h-8 shrink-0 fill-neutral-50">
                        <path d="M14 2.2C22.5-1.7 32.5-.3 39.6 5.8L80 40.4 120.4 5.8c9-7.7 22.3-7.7 31.2 0L192 40.4 232.4 5.8c9-7.7 22.3-7.7 31.2 0L304 40.4 344.4 5.8c7.1-6.1 17.1-7.5 25.6-3.6s14 12.4 14 21.8l0 464c0 9.4-5.5 17.9-14 21.8s-18.5 2.5-25.6-3.6L304 471.6l-40.4 34.6c-9 7.7-22.3 7.7-31.2 0L192 471.6l-40.4 34.6c-9 7.7-22.3 7.7-31.2 0L80 471.6 39.6 506.2c-7.1 6.1-17.1 7.5-25.6 3.6S0 497.4 0 488L0 24C0 14.6 5.5 6.1 14 2.2zM96 144c-8.8 0-16 7.2-16 16s7.2 16 16 16l192 0c8.8 0 16-7.2 16-16s-7.2-16-16-16L96 144zM80 352c0 8.8 7.2 16 16 16l192 0c8.8 0 16-7.2 16-16s-7.2-16-16-16L96 336c-8.8 0-16 7.2-16 16zM96 240c-8.8 0-16 7.2-16 16s7.2 16 16 16l192 0c8.8 0 16-7.2 16-16s-7.2-16-16-16L96 240z"/></svg>
                    
                    <span
                        class="origin-left inline-flex duration-60 group-hover:duration-300 group-hover:delay-500 opacity-0 group-hover:opacity-100 border-l-2 px-1 transform scale-x-0 group-hover:scale-x-100 transition-all">Generar Reporte</span>
                </button>





            </div>
        </article>
</body>

</section>

<?php include 'views/partials/sidebar_right.php'; ?>


<script src="https://kit.fontawesome.com/db59255a97.js" crossorigin="anonymous"></script>


<script>
// Aquí se asume que tienes una forma de obtener el nombre del usuario y el local
const user = {
    nombre: '<?php echo $user->getNombre(); ?>',
    id_local: '<?php echo $user->getId_local(); ?>', 
    contadoresPago: <?php echo json_encode($contadoresPago); ?>,
    totalDiario: <?php echo array_sum(array_column($contadoresPago, 'total')); ?>
};
</script>
<script src="public/js/reporte.js"></script>

</body>

</html>