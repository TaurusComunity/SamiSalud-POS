let barcodeBuffer = "";
let typingTimer;

document
  .getElementById("barcodeInput")
  .addEventListener("input", function (event) {
    // Limpiamos el temporizador anterior si el usuario sigue escribiendo
    clearTimeout(typingTimer);

    // Agregamos el carácter al buffer
    barcodeBuffer = event.target.value;

    // Comprobamos si el tamaño del buffer está dentro del rango permitido
    if (barcodeBuffer.length >= 5 && barcodeBuffer.length <= 15) {
      // Iniciamos un temporizador para procesar el código de barras después de 1500ms de inactividad
      typingTimer = setTimeout(() => {
        buscarProducto(barcodeBuffer);
        barcodeBuffer = ""; // Limpiamos el buffer después de enviar el código
        document.getElementById("barcodeInput").value = ""; // Limpiamos el input
      }, 1500); // Espera 1500ms para asegurarse de que el usuario ha terminado de escribir o el lector ha completado
    } else if (barcodeBuffer.length > 15) {
      // Si el código de barras supera el límite, limpiamos el buffer
      alert("El código de barras no puede exceder los 15 caracteres.");
      barcodeBuffer = ""; // Reiniciar el buffer
      document.getElementById("barcodeInput").value = ""; // Limpiar el input
    }
  });

// BUSCAR Y AÑADIR PRODUCTO
function buscarProducto(barcode) {
  if (barcode) {
    fetch(`facturador/buscador?barcode=${barcode}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error al obtener el producto");
        }
        return response.json();
      })
      .then((data) => {
        if (data) {
          // Comprobar si el stock es 0
          if (data.stock <= 0) {
            alert(`El producto '${data.nombre}' no está disponible en stock.`);
          } else {
            agregarProductoATabla(data);
          }
        } else {
          alert("Producto no encontrado.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }
}

// AGREGAR PRODUCTO A LA TABLA
function agregarProductoATabla(producto) {
  const tableBody = document.getElementById("productTableBody");
  let existingRow = null;

  // Verificamos si el producto ya está en la tabla
  for (let row of tableBody.rows) {
    if (row.cells[0].textContent === producto.codigo_barras) {
      existingRow = row;
      break;
    }
  }

  if (existingRow) {
    // Si el producto ya está, aumentamos la cantidad y recalculamos el precio
    const cantidadCell = existingRow.cells[2];
    const precioCell = existingRow.cells[3];
    let cantidadActual = parseInt(cantidadCell.textContent);
    let nuevaCantidad = cantidadActual + producto.cantidad;
    cantidadCell.textContent = nuevaCantidad;
    let precioUnitario = producto.precio;
    let nuevoPrecioTotal = precioUnitario * nuevaCantidad;
    precioCell.textContent = `$${nuevoPrecioTotal}`;
  } else {
    // Si el producto no está, lo añadimos a la tabla
    const row = document.createElement("tr");
    row.innerHTML = `
                <td class="p-2 font-medium">${producto.codigo_barras}</td>
                <td class="p-2 font-medium">${producto.nombre}</td>
                <td class="p-2 font-medium">${producto.cantidad}</td>
                <td class="p-2 font-medium">$${producto.precio_total}</td>
                <td>
                    <div class="flex gap-4 justify-center items-center">
                        <a href="#" class="eliminar-producto hover:text-rojo hover:text-lg text-azul_oscuro">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </div>
                </td>
                `;

    row
      .querySelector(".eliminar-producto")
      .addEventListener("click", function (event) {
        event.preventDefault();
        eliminarProducto(row);
      });

    tableBody.appendChild(row);
  }

  actualizarTotal(); // Llama a la función para actualizar el total
}

// CALCULAR TOTAL
function calcularTotal() {
  const tableBody = document.getElementById("productTableBody");
  const rows = Array.from(tableBody.rows);

  return rows.reduce((total, row) => {
    const precio = parseFloat(
      row.cells[3].textContent.replace("$", "").replace(".", "").trim()
    );
    return total + precio;
  }, 0);
}

// ACTUALIZAR TOTAL
function actualizarTotal() {
  const tableBody = document.querySelector("tbody");
  let total = 0;

  Array.from(tableBody.rows).forEach((row) => {
    const precio = parseFloat(
      row.cells[3].textContent.replace(/\$/g, "").replace(/\./g, "").trim()
    );
    total += precio;
  });

  document.getElementById("total").textContent = total.toLocaleString("es-CO");
}

// ELIMINAR PRODUCTO
function eliminarProducto(row) {
  row.remove();
  actualizarTotal();
}

// ELIMINAR TODOS LOS PRODUCTOS
function eliminarTodosLosProductos() {
  const productTableBody = document.getElementById("productTableBody");

  while (productTableBody.firstChild) {
    productTableBody.removeChild(productTableBody.firstChild);
  }

  actualizarTotal();
}

// METODOS DE PAGO
function openPaymentModal() {
  document.getElementById("paymentModal").classList.remove("hidden");
}

function closeModal() {
  document.getElementById("paymentModal").classList.add("hidden");
  let inputElement = document.getElementById("amountPaid");
  inputElement.value = ""; // Limpiamos el input si existe
}

// METODO IMPRIMIR FACTURA
function openModalImprimir() {
  document.getElementById("modalImprimir").classList.remove("hidden");
  resetModal();
}

function resetModal() {
  document.getElementById('paymentMethod').selectedIndex = 0; // Vuelve a la opción predeterminada
  document.getElementById('amountPaid').value = ''; // Limpiar el campo
}

function closeModalImprimir() {
  document.getElementById("modalImprimir").classList.add("hidden");
}

// IMPRIMIR
function imprimirFactura(montoPagado, cambio, user, selectedPaymentMethod) {
  const tableBody = document.getElementById("productTableBody");
  const rows = Array.from(tableBody.rows);

  let facturaHtml = `
<div style="text-align: center; font-family: 'Poppins', sans-serif;">
<h2 style="margin-bottom: 5px;">SamiSalud-P</h2>
<p style="margin: -2px 0;">NIT: 80750925-6</p>
<p style="margin: -2px 0;">Calle 58c sur #45-03</p>
<p style="margin: -2px 0;">${new Date().toLocaleString()}</p>
<p style="margin: -2px 0;">Atendió: ${user.nombre}</p>
<p style="margin: -2px 0;">Local: ${user.id_local}</p>
</div>
<hr style="border: 2px solid black; margin: 10px 0;">
<table style="width: 100%; border-collapse: collapse; font-family: 'Poppins', sans-serif;">
<thead>
    <tr>
        <th style="text-align: left; padding: 8px; border-bottom: 1px solid black;">Cant.</th>
        <th style="text-align: left; padding: 8px; border-bottom: 1px solid black;">Producto</th>
        <th style="text-align: right; padding: 8px; border-bottom: 1px solid black;">Precio</th>
    </tr>
</thead>
<tbody>
`;

  let total = 0;
  rows.forEach((row) => {
    const cantidad = row.cells[2].textContent;
    const producto = row.cells[1].textContent;
    const precio = parseFloat(
      row.cells[3].textContent.replace(/\$/g, "").replace(/\./g, "").trim()
    );

    facturaHtml += `
<tr style="border-bottom: 1px solid black;">
    <td style="padding: 8px;">${cantidad}</td>
    <td style="padding: 8px;">${producto}</td>
    <td style="text-align: right; padding: 8px;">$${precio.toLocaleString(
      "es-CO"
    )}</td>
</tr>
`;
    total += precio;
  });

  facturaHtml += `
</tbody>
</table>
<hr style="border: 1px solid black; margin: 10px 0;">
<div style="text-align: right; font-family: 'Poppins', sans-serif;">
<p style="margin: 2px 0;">Total: $${total.toLocaleString("es-CO")}</strong></p>
<p style="margin: 2px 0;">Monto Pagado: $${montoPagado.toLocaleString("es-CO")}</strong></p>
<p style="margin: 2px 0;">Cambio: $${cambio.toLocaleString("es-CO")}</strong></p>
</div>
`;

  const ventanaImpresion = window.open("", "_blank");
  ventanaImpresion.document.write(facturaHtml);
  ventanaImpresion.document.close();
  ventanaImpresion.focus();
  ventanaImpresion.print();
  ventanaImpresion.close();

  closeModalImprimir();
}
