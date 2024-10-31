function mostrarHoraActual() {
  const ahora = new Date();
  let horas = ahora.getHours();
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  
  // Determinamos AM o PM y ajustamos el formato de 24 horas a 12 horas
  const amPm = horas >= 12 ? 'P.M' : 'A.M';
  horas = horas % 12 || 12; // Convierte "0" a "12" para mostrar medianoche como "12 AM"
  
  const horaFormateada = `${horas}:${minutos}:${segundos} ${amPm}`;
  document.getElementById("horaActual").textContent = horaFormateada;
}

// Actualiza la hora cada segundo
setInterval(mostrarHoraActual, 1000);
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
