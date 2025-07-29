// Gastos comunes fijos
const gastosFijos = [
  { nombre: "Electricidad", monto: 15000 },
  { nombre: "Limpieza", monto: 8000 },
  { nombre: "Seguro del edificio", monto: 5000 }
];

const totalSocios = 20;

// Función para generar gastos y calcular cuota por socio
function generarGastosComunes() {
  const tabla = document.getElementById("tablaGastos").querySelector("tbody");
  tabla.innerHTML = ""; // Limpiar tabla previa

  let total = 0;
  gastosFijos.forEach(gasto => {
    total += gasto.monto;
    const fila = document.createElement("tr");
    fila.innerHTML = `<td>${gasto.nombre}</td><td>$${gasto.monto.toLocaleString()}</td>`;
    tabla.appendChild(fila);
  });

  const cuota = total / totalSocios;
  document.getElementById("cuotaSocio").textContent =
    `Tu cuota correspondiente este mes es: $${cuota.toFixed(2)}`;
}
