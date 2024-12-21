tailwind.config = {
    theme: {
      extend: {
        colors: {
            azul: "#4169e1",
            azul_oscuro: "#1A2130",
            verde_oscuro: "#1A2130",
            blanco: "#f5f5f5",
            azul_oscuro_opacidad: "rgba(26, 33, 48, 0.100)",
            azul_oscuro_opacidad2: "rgba(26, 33, 48, 0.397)",
            azul_opacidad: "rgba(97, 94, 252, 0.45)",
            rojo: "#D04848",
            verde: "#81A263",
            amarillo: "#FFA62F",
        }
      }
    }
  }

  document.addEventListener('DOMContentLoaded', (event) => {
    const fechaElemento = document.getElementById('fecha');
    const añoElemento = document.getElementById('año');
    const fecha = new Date();

    const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const dia = dias[fecha.getDay()];
    const diaNumero = fecha.getDate();
    const mes = meses[fecha.getMonth()];
    const año = fecha.getFullYear();

    fechaElemento.textContent = `Hoy ${diaNumero} de ${mes} de ${año}`;
    añoElemento.textContent = `${año}`;
});


document.addEventListener('DOMContentLoaded', () => {
    const daysContainer = document.getElementById('days');
    const monthYearSpan = document.getElementById('month-year');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');

    let currentDate = new Date();
    const today = new Date();

    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    function generateCalendar(date) {
        daysContainer.innerHTML = '';
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);
        const firstDayOfWeek = firstDayOfMonth.getDay();

        monthYearSpan.textContent = `${meses[month]} ${year}`;

        // Días del mes anterior
        const prevMonth = new Date(year, month - 1, 0);
        const daysInPrevMonth = prevMonth.getDate();
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const dayDiv = document.createElement('div');
            dayDiv.textContent = daysInPrevMonth - i;
            dayDiv.classList.add('text-gray-400', 'p-2', 'border-b', 'border-gray-300');
            daysContainer.appendChild(dayDiv);
        }

        // Días del mes actual
        for (let day = 1; day <= lastDayOfMonth.getDate(); day++) {
            const dayDiv = document.createElement('div');
            dayDiv.textContent = day;
            dayDiv.classList.add('p-2', 'border-b', 'border-gray-300');

            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                dayDiv.classList.add('bg-blue-500', 'text-white', 'rounded-full');
            }

            daysContainer.appendChild(dayDiv);
        }

        // Días del mes siguiente
        const nextMonthStart = (firstDayOfWeek + lastDayOfMonth.getDate()) % 7;
        if (nextMonthStart !== 0) {
            for (let i = 1; i <= (7 - nextMonthStart); i++) {
                const dayDiv = document.createElement('div');
                dayDiv.textContent = i;
                dayDiv.classList.add('text-gray-400', 'p-2', 'border-b', 'border-gray-300');
                daysContainer.appendChild(dayDiv);
            }
        }
    }

    prevMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate);
    });

    nextMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate);
    });

    generateCalendar(currentDate);
});


document.addEventListener('DOMContentLoaded', () => {
    const quantityCells = document.querySelectorAll('.quantity-cell');

    quantityCells.forEach(cell => {
        const text = cell.textContent.trim();
        const quantityMatch = text.match(/\d+/);
        
        if (quantityMatch) {
            const quantity = parseInt(quantityMatch[0], 10);

            if (quantity >= 11 ) {
                cell.classList.add('bg-[#81A263]', 'text-white');
            } else if (quantity >= 6 && quantity <= 10) {
                cell.classList.add('bg-[#FFA62F]', 'text-white');
            } else if (quantity <= 5) {
                cell.classList.add('bg-[#D04848]', 'text-white');
            }
        }
    });
});

document.getElementById('precioForm').addEventListener('input', function() {
    // Obtener los valores del precio neto y el IVA
    const precioNeto = parseFloat(document.getElementById('precio_neto').value) || 0;
    const iva = parseFloat(document.getElementById('iva').value) || 0;
    const ganancia = parseFloat(document.getElementById('ganancia').value) || 0;

    // Calcular el precio final
    const subtotal = precioNeto * (1 + (iva / 100));
    const total = subtotal + ganancia;

    // Mostrar el resultado en el campo de Precio Final
    document.getElementById('precioFinal').value = total.toFixed(2); // Redondeo a dos decimales
});


