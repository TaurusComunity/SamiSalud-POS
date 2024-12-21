function mostrarHoraActual() {
  const ahora = new Date();
  let horas = ahora.getHours();
  const minutos = String(ahora.getMinutes()).padStart(2, "0");
  const segundos = String(ahora.getSeconds()).padStart(2, "0");
  const amPm = horas >= 12 ? "P.M" : "A.M";
  horas = horas % 12 || 12;
  document.getElementById(
    "horaActual"
  ).textContent = `${horas}:${minutos}:${segundos} ${amPm}`;
}
setInterval(mostrarHoraActual, 1000);

function imprimirReporteDiario() {
  // Accedemos a `contadoresPago` y `totalDiario` desde `user`
  const contadoresPago = user.contadoresPago;
  const totalDiario = user.totalDiario;

  let reporteHtml = `
          <div style="text-align: center; font-family: 'Poppins', sans-serif;">
              <h2>SamiSalud-P</h2>
              <p>NIT: 80750925-6</p>
              <p>Calle 58c sur #45-03</p>
              <p>${new Date().toLocaleString()}</p>
              <p>Generado por: ${user.nombre}</p>
              <p>Local: ${user.id_local}</p>
          </div>
          <hr>
          <h3 style="text-align: center;">Reporte Diario</h3>
          <table style="width: 100%;">
              <thead>
                  <tr><th>Método de Pago</th><th>Cantidad</th><th>Total</th></tr>
              </thead>
              <tbody>`;

  contadoresPago.forEach((contador) => {
    reporteHtml += `
              <tr>
                  <td>${contador.metodo_pago}</td>
                  <td>${contador.cantidad}</td>
                  <td>$${contador.total.toLocaleString("es-CO")}</td>
              </tr>`;
  });

  reporteHtml += `
              </tbody>
          </table>
          <hr>
          <p><strong>Total del Día:</strong> $${totalDiario.toLocaleString(
            "es-CO"
          )}</p>
          <hr style="border: 1px solid black; margin: 10px 0;">
<p style="text-align: center; font-family: 'Poppins', sans-serif; font-size: 12px;">TAURUS COMUNITY<br>Todos los derechos reservados 2024</p>
<p style="text-align: center; font-family: 'Poppins', sans-serif; font-size: 12px;">www.taurusco.com</p>
          `;

  const ventanaImpresion = window.open("", "", "height=400,width=600");
  ventanaImpresion.document.write("<html><head><title>Reporte Diario</title>");
  ventanaImpresion.document.write(
    '<style>body { font-family: "Poppins", sans-serif; } table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; text-align: left; border-bottom: 1px solid black; }</style>'
  );
  ventanaImpresion.document.write(reporteHtml);
  ventanaImpresion.document.write("</body></html>");
  ventanaImpresion.print();
  ventanaImpresion.close();
}
