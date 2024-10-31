<?php 
$user = isset($this->data['user']) ? $this->data['user'] : null;
$product = isset($this->data['product']) ? $this->data['product'] : null;
$actividades = isset($this->data['actividad']) ? $this->data['actividad'] : null;
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
    <title>Registro de Actividad</title>
</head>

<body class="bg-blanco flex w-full absolute min-h-[100vh]">
    <!-- Incluir la barra lateral izquierda-->
    <?php include 'views/partials/sidebar_left.php'; ?>

    <section id="contenido" class="flex flex-col bg-azul_oscuro_opacidad w-[60%] relative rounded-3xl my-5">
        <article class="w-full pt-5 px-8">
            <h2 class="text-azul_oscuro text-[32px] font-semibold">Registro de Actividad</h2>
            <p class="font-medium text-[18px] -mt-2">Apartado donde podrás ver todo el movimiento de Taurus POS</p>
        </article>

        <main class="px-5 mt-10 pt-10 max-h-[100vh] overflow-auto relative">
            <?php foreach ($actividades as $actividad): ?>
            <div class="flex relative mt-2 bg-azul_oscuro h-[auto]  rounded-lg items-center gap-4 p-2">
                <div
                    class="bg-amarillo text-azul_oscuro text-[18px] flex justify-center items-center p-2 rounded-full h-[30px] w-[30px]">
                    <i class="fa-solid fa-bell"></i>
                </div>


                <div class="text-blanco  text-[17px]">
                    <p><?= $actividad['descripcion']; ?></p>
                    <p class="text-[12px]"><?= date('d-m-Y H:i:s', strtotime($actividad['fecha'])); ?></p>

                </div>
            </div>
            <?php endforeach; ?>

        </main>
    </section>

    <?php include 'views/partials/sidebar_right.php'; ?>

    <script src="https://kit.fontawesome.com/db59255a97.js" crossorigin="anonymous"></script>
    <script src="public/js/facturador.js"></script>
    <script src="public/js/guardar.js"></script>

</body>

</html>