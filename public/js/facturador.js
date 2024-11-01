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
      // Iniciamos un temporizador para procesar el código de barras después de 300ms de inactividad
      typingTimer = setTimeout(() => {
        buscarProducto(barcodeBuffer);
        barcodeBuffer = ""; // Limpiamos el buffer después de enviar el código
        document.getElementById("barcodeInput").value = ""; // Limpiamos el input
      }, 1500); // Espera 3000ms para asegurarse de que el usuario ha terminado de escribir o el lector ha completado
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
              if (data.stock <= 0) {
                alert(`El producto '${data.nombre}' no está disponible en stock.`);
              }
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
    // Selecciona el cuerpo de la tabla
    const productTableBody = document.getElementById("productTableBody");
  
    // Elimina todos los hijos (filas) del tbody
    while (productTableBody.firstChild) {
      productTableBody.removeChild(productTableBody.firstChild);
    }
  
    // Actualiza el total
    actualizarTotal();
  }

///////////////////////////

// Función para eliminar un producto (fila) de la tabla
function eliminarProducto(row) {
  row.remove();
  actualizarTotal(); // Actualiza el total después de eliminar
}

// METODOS DE PAGO
function openPaymentModal() {
  document.getElementById("paymentModal").classList.remove("hidden");
}

function closeModal() {
  document.getElementById("paymentModal").classList.add("hidden");
}

// METODO IMPRIMIR FACTURA
function openModalImprimir() {
  document.getElementById("modalImprimir").classList.remove("hidden");
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
<p style="margin: 2px 0; text-align:left;">Método de pago:</p>
<p style="margin: 2px 0;">${selectedPaymentMethod}: $${montoPagado.toLocaleString(
    "es-CO"
  )}</strong></p>
<p style="margin: 2px 0;">Cambio: $${
    selectedPaymentMethod === "Efectivo" ? cambio.toLocaleString("es-CO") : 0
  }</strong></p>
</div>
<hr style="border: 1px solid black; margin: 10px 0;">
<p style="text-align: center; font-family: 'Poppins', sans-serif; font-size: 12px;">TAURUS COMUNITY<br>Todos los derechos reservados 2024</p>
<p style="text-align: center; font-family: 'Poppins', sans-serif; font-size: 12px;">www.taurusco.com</p>
`;

  const ventanaImpresion = window.open("", "", "height=400,width=600");
  ventanaImpresion.document.write("<html><head><title>Factura</title>");
  ventanaImpresion.document.write(
    '<style>body { font-family: "Poppins", sans-serif; } table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; text-align: left; border-bottom: 1px solid black; }</style>'
  );
  ventanaImpresion.document.write(facturaHtml);
  ventanaImpresion.document.write("</body></html>");
  ventanaImpresion.print();
  ventanaImpresion.close();
}

function calcularCambioSinFactura() {
  const amountPaid =
    parseFloat(document.getElementById("amountPaid").value) || 0;
  const selectedPaymentMethod = document.getElementById("paymentMethod").value;

  const total = calcularTotal(); // Debes implementar esta función para obtener el total
  const cambio = amountPaid - total;

  // Asegúrate de que hay productos en la tabla
  const productos = Array.from(document.querySelectorAll("tbody tr")).map(
    (row) => {
      return {
        codigo_barras: row.cells[0].textContent,
        nombre: row.cells[1].textContent,
        cantidad: parseInt(row.cells[2].textContent),
        precio_total: parseFloat(row.cells[3].textContent.replace(/\$|,/g, "")),
      };
    }
  );

  // Validar todos los campos
  if (
    !selectedPaymentMethod ||
    amountPaid <= 0 ||
    cambio < 0 ||
    productos.length === 0
  ) {
    alert("No puedes facturar sin productos.");
    return;
  }

  // Ahora llama a guardarFactura con todos los datos recogidos
  guardarFactura(total, amountPaid, cambio, selectedPaymentMethod, productos);
  console.log({
    total: total,
    amountPaid: amountPaid,
    cambio: cambio,
    paymentMethod: selectedPaymentMethod,
    productos: productos,
  });

  closeModalImprimir();
  closeModal();
  eliminarTodosLosProductos();
  alert("Venta registrada sin factura.");
}

function calcularCambio() {
  const amountPaid =
    parseFloat(document.getElementById("amountPaid").value) || 0;
  const selectedPaymentMethod = document.getElementById("paymentMethod").value;

  const total = calcularTotal(); // Debes implementar esta función para obtener el total
  const cambio = amountPaid - total;

  // Asegúrate de que hay productos en la tabla
  const productos = Array.from(document.querySelectorAll("tbody tr")).map(
    (row) => {
      return {
        codigo_barras: row.cells[0].textContent,
        nombre: row.cells[1].textContent,
        cantidad: parseInt(row.cells[2].textContent),
        precio_total: parseFloat(row.cells[3].textContent.replace(/\$|,/g, "")),
      };
    }
  );

  // Validar todos los campos
  if (
    !selectedPaymentMethod ||
    amountPaid <= 0 ||
    cambio < 0 ||
    productos.length === 0
  ) {
    alert("No puedes facturar sin productos.");
    return;
  }

  // Llama a imprimirFactura con los argumentos necesarios
  imprimirFactura(amountPaid, cambio, user, selectedPaymentMethod);

  // Ahora llama a guardarFactura con todos los datos recogidos
  guardarFactura(total, amountPaid, cambio, selectedPaymentMethod, productos);
  console.log({
    total: total,
    amountPaid: amountPaid,
    cambio: cambio,
    paymentMethod: selectedPaymentMethod,
    productos: productos,
  });

  closeModalImprimir();
  closeModal();
  alert("Venta registrada con factura.");
  eliminarTodosLosProductos();
}

async function guardarFactura(
  total,
  amountPaid,
  cambio,
  selectedPaymentMethod,
  productos
) {
  try {
    // Guarda la factura con todos los detalles de productos
    const response = await fetch("facturador/newFactura", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        total: total,
        amountPaid: amountPaid,
        cambio: cambio,
        paymentMethod: selectedPaymentMethod,
        productos: productos, // Aquí enviamos el array de productos
      }),
    });

    if (!response.ok) {
      throw new Error(
        "Error en la respuesta del servidor al guardar la factura"
      );
    }

    const data = await response.json();
    console.log("Factura y detalles guardados:", data);
  } catch (error) {
    console.error("Error al guardar la factura:", error);
    throw error;
  }
}
