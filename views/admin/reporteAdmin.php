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

        <article class="flex justify-between items-center h-full flex-wrap gap-5 pt-14 pb-5 mx-8">

            <?php foreach ($contadoresPago as $pago): ?>

            <div
                class="hover:cursor-pointer hover:-translate-y-4 transition-all shadow-lg relative w-[22.5%] h-[130px] rounded-xl bg-[url('public/img/degradado.jpg')] bg-cover bg-center ">

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
            class="my-8 p-5 bg-cover bg-[#0A0D2D] h-[40%] rounded-xl mx-8 flex flex-col items-start justify-center">
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


                <a href="facturador">
                    <button
                        class="mt-3 flex justify-center gap-2 items-center mx-auto shadow-xl text-lg text-gray-50 bg-[#0A0D2D] backdrop-blur-md lg:font-semibold isolation-auto border-gray-50 before:absolute before:w-full before:transition-all before:duration-700 before:hover:w-full before:-left-full before:hover:left-0 before:rounded-full before:bg-[#FFFFFF] hover:text-black before:-z-10 before:aspect-square before:hover:scale-200 before:hover:duration-500 relative z-10 px-4 py-2 overflow-hidden border-2 rounded-lg group"
                        type="submit">
                        Empezar a vender
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 19"
                            class="w-8 h-8 justify-end bg-gray-50 group-hover:rotate-90 group-hover:bg-gray-50 text-gray-50 ease-linear duration-300 rounded-full border border-gray-700 group-hover:border-gray-700 p-2 rotate-45">
                            <path class="fill-gray-800 group-hover:fill-gray-800"
                                d="M7 18C7 18.5523 7.44772 19 8 19C8.55228 19 9 18.5523 9 18H7ZM8.70711 0.292893C8.31658 -0.0976311 7.68342 -0.0976311 7.29289 0.292893L0.928932 6.65685C0.538408 7.04738 0.538408 7.68054 0.928932 8.07107C1.31946 8.46159 1.95262 8.46159 2.34315 8.07107L8 2.41421L13.6569 8.07107C14.0474 8.46159 14.6805 8.46159 15.0711 8.07107C15.4616 7.68054 15.4616 7.04738 15.0711 6.65685L8.70711 0.292893ZM9 18L9 1H7L7 18H9Z">
                            </path>
                        </svg>
                    </button>

                </a>




            </div>
        </article>
</body>

</section>

<?php include 'views/partials/sidebar_right.php'; ?>


<script src="https://kit.fontawesome.com/db59255a97.js" crossorigin="anonymous"></script>


<script>
// Aquí se asume que tienes una forma de obtener el nombre del usuario y el local
const user = {
    nombre: '<?php echo $user->getNombre(); ?>', // Cambia esto por la variable real
    id_local: '<?php echo $user->getId_local(); ?>' // Cambia esto por la variable real
};
</script>
<script src="public/js/reporte.js"></script>

</body>

</html>