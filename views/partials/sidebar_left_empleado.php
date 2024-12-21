<section id="sidebar" class="w-[18%] relative my-5">
    <article id="logo" class="flex flex-col items-center pt-5">
        <h1 class="text-azul text-[32px] font-semibold">Sami Salud</h1>
        <p class="font-medium text-[14px] -mt-2 ">
            <?php echo ($user->getId_local() == 2) ? 'Calle 58C sur #45-03' : 'Carrera 13F #54-03' ?></p>
        </article>

        <main class="flex flex-col justify-between pt-16 h-[87%]">
        <article id="navegacion">
            <ul class="px-5">
                <a href="empleado">
                    <li
                        class="text-azul p-2 rounded mt-1 flex justify-star items-center gap-2 cursor-pointer font-medium hover:text-blanco hover:bg-azul transition-all">
                        <i class="fa-solid fa-house"></i> Inicio
                    </li>
                </a>
                <a href="reporteEmpleado">
                    <li
                        class="text-azul p-2 rounded mt-1 flex justify-star items-center gap-2 cursor-pointer font-medium hover:text-blanco hover:bg-azul transition-all">
                        <i class="fa-solid fa-cash-register"></i> Reporte venta
                    </li>
                </a>

                

            </ul>


        </article>
        <footer class="px-5 pb-8">
            <p class="text-[10px] text-center font-medium"><a href="https://www.solutioncodeco.com" target="_blank"
                    class="text-azul font-bold">SolutionCode</a> | Todos los derechos reservados Sami Salud
                <span id="año">2024 :)</span>
            </p>
        </footer>

    </main>
</section>

<script src="https://kit.fontawesome.com/db59255a97.js" crossorigin="anonymous"></script>
<script src="public/js/main.js"></script>