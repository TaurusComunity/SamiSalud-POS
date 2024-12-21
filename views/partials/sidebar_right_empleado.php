<section id="auxiliar" class=" w-[22%] my-5">
        
        <!-- component -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<div class="my-5 mx-5 py-3 mb-9 flex justify-between items-center">
<div class="flex items-center gap-2"><img src="public/img/user.svg" class="rounded-full w-[40px] h-[40px]"
            alt="">
            <div class="flex flex-col">
                <span class="font-medium text-[19px]"> <?php echo ($user->getNombre() != '')? $user->getNombre() : $user->getUsuario();?></span>
                    <span class="-mt-2 text-[12px] text-gray-400"> <?php echo ($user->getId_rol() == '2')? 'Administrador' : 'Empleado' ?></span>
                </div>
            </div>
   
</div>
        
        <article id="calendario" class="px-5">
            <div class="calendar w-full bg-blanco">
                <div class="header items-center flex justify-between">
                    <span id="month-year" class="font-semibold text-[17px]"></span>
                    
                    <div>
                        <button id="prev-month"
                            class="shadow-lg hover:bg-azul_oscuro_opacidad transition-all p-2 rounded-full h-[40px] w-[40px] text-azul"><i
                            class="fa-solid fa-caret-left"></i></button>
                            <button id="next-month"
                            class="shadow-lg hover:bg-azul_oscuro_opacidad transition-all p-2 rounded-full h-[40px] w-[40px] text-azul"><i
                                class="fa-solid fa-caret-right"></i></button>
                            </div>
                            
                </div>
                <div class="weekdays grid grid-cols-7 text-center text-[12px] mt-3 font-medium">
                    <div>Dom</div>
                    <div>Lun</div>
                    <div>Mar</div>
                    <div>Mie</div>
                    <div>Jue</div>
                    <div>Vie</div>
                    <div>Sab</div>
                </div>
                <div class="days grid grid-cols-7 text-center text-[14px] font-medium" id="days"></div>
            </div>
        </article>

        <article id="novedades" class=" mx-5 mt-5 ">
        <a href="<?php echo constant('URL'); ?>/login/logout" class="flex justify-between items-center p-4 bg-gray-800 text-white rounded-sm text-center font-bold py-2">Cerrar sesión <i class="fa-solid fa-right-from-bracket"></i></a>
        </article> 
    </section>