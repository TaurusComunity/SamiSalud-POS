<?php $this->showMessages(); ?>
<div class="rounded mt-7 w-full overflow-auto max-h-[500px]">
    <?php if (!empty($this->data['GestionResiduos'])): ?>
        <table class="w-full rounded ">
            <thead class="text-left bg-azul_opacidad rounded w-full">
                <tr class="">
                    <th class="p-2 font-medium">ID</th>
                    <th class="p-2 font-medium">Mes</th>
                    <th class="p-2 font-medium">Tipo residuo</th>
                    <th class="p-2 font-medium">Cantidad</th>
                    <th class="p-2 font-medium">Fecha creacion</th>
                </tr>
            </thead>

            <div class="">
                <tbody class="text-left ">
                    <?php foreach ($this->data['GestionResiduos'] as $GestionResiduo): ?>
                        <tr>
                            <td class="p-2 font-medium"><?php echo $GestionResiduo->getId(); ?></td>
                            <td class="p-2 font-medium max-w-[250px]"><?php echo $GestionResiduo->getMes(); ?></td>
                            <td class="p-2 font-medium"><span
                                    class=" py-1 px-3 rounded-lg"><?php echo $GestionResiduo->getTipo_residuo(); ?></span>
                            </td>
                            <td class="p-2 font-medium"><?php echo $GestionResiduo->getCantidad(); ?></td>
                            <td class="p-2 font-medium"><?php echo $GestionResiduo->getFecha_residuo(); ?></td>
                            
                        
                        </tr>
                    <?php endforeach; ?>
                </tbody>

        </table>
    <?php else: ?>
        <div class="flex flex-col items-center gap-2">
            <p class="text-center font-bold text-3xl">No hay datos en la categoría seleccionada.</p>
            <img src="./public/img/vacio.svg" alt="sin datos" class="w-[560px]">
        </div>

    <?php endif; ?>
</div>
</div>