<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Cooperativa</title>
    
    <link rel="stylesheet" href="/views/CSS/estilos.css">
    
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    
    <style>
        #calendar { max-width: 1100px; margin: 40px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        /* Estilos del Modal (Pop-up) */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 400px; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        input, select { width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        .btn-guardar { background: #28a745; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; }
        .btn-borrar { background: #dc3545; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; margin-top: 10px; display: none; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a id="logo" href="/">⬅ Volver al Inicio</a>
            <nav><ul><li>Hola, <?= $_SESSION['Nombre'] ?? 'Usuario' ?></li></ul></nav>
        </div>
    </header>

    <main>
        <h2 style="text-align: center; margin-top: 20px;">📅 Calendario de Actividades</h2>
        
        <div id='calendar'></div>
    </main>

    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalTitle">Nuevo Evento</h3>
            <input type="hidden" id="eventId">
            
            <label>Título del evento:</label>
            <input type="text" id="eventTitle" placeholder="Ej: Asamblea, Pago, Mantenimiento">
            
            <label>Color:</label>
            <select id="eventColor">
                <option value="#3788d8">🔵 Azul (General)</option>
                <option value="#28a745">🟢 Verde (Pagos)</option>
                <option value="#dc3545">🔴 Rojo (Urgente)</option>
                <option value="#ffc107">🟡 Amarillo (Reunión)</option>
            </select>

            <?php if(isset($_SESSION['Rol']) && $_SESSION['Rol'] == 1): ?>
                <button class="btn-guardar" onclick="guardarEvento()">Guardar Evento</button>
                <button class="btn-borrar" id="btnDelete" onclick="borrarEvento()">🗑️ Borrar Evento</button>
            <?php else: ?>
                <p style="color:red; text-align:center; margin-top:10px;">⚠️ Solo los administradores pueden editar el calendario.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>

    <script>
        var calendar;
        var modal = document.getElementById("eventModal");
        var span = document.getElementsByClassName("close")[0];
        
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es', 
                selectable: true,
                editable: true, 
                events: '/api/eventos', // Carga desde la BD

                // CLICK EN DÍA VACÍO (CREAR)
                select: function(info) {
                    <?php if(isset($_SESSION['Rol']) && $_SESSION['Rol'] == 1): ?>
                        limpiarModal();
                        document.getElementById('modalTitle').innerText = "Nuevo Evento";
                        window.startStr = info.startStr;
                        window.endStr = info.endStr;
                        modal.style.display = "block";
                    <?php endif; ?>
                },

                // CLICK EN EVENTO EXISTENTE (EDITAR)
                eventClick: function(info) {
                    limpiarModal();
                    document.getElementById('modalTitle').innerText = "Editar Evento";
                    document.getElementById('eventId').value = info.event.id;
                    document.getElementById('eventTitle').value = info.event.title;
                    document.getElementById('eventColor').value = info.event.backgroundColor;
                    
                    <?php if(isset($_SESSION['Rol']) && $_SESSION['Rol'] == 1): ?>
                        document.getElementById('btnDelete').style.display = "block";
                        window.startStr = info.event.startStr;
                        window.endStr = info.event.endStr;
                        modal.style.display = "block";
                    <?php endif; ?>
                }
            });
            calendar.render();
        });

        span.onclick = function() { modal.style.display = "none"; }
        window.onclick = function(event) { if (event.target == modal) modal.style.display = "none"; }

        function limpiarModal() {
            document.getElementById('eventId').value = '';
            document.getElementById('eventTitle').value = '';
            document.getElementById('btnDelete').style.display = 'none';
        }

        function guardarEvento() {
            var titulo = document.getElementById('eventTitle').value;
            var id = document.getElementById('eventId').value;
            var color = document.getElementById('eventColor').value;
            
            if(!titulo) return alert("Escribí un título.");

            var datos = {
                id: id,
                title: titulo,
                start: window.startStr,
                end: window.endStr,
                color: color
            };

            fetch('/api/eventos/guardar', {
                method: 'POST',
                body: JSON.stringify(datos),
                headers: {'Content-Type': 'application/json'}
            }).then(res => res.json())
            .then(data => {
                if(data.status === 'success'){
                    modal.style.display = "none";
                    calendar.refetchEvents();
                } else {
                    alert("Error: " + data.message);
                }
            });
        }

        function borrarEvento() {
            var id = document.getElementById('eventId').value;
            if(!confirm("¿Borrar este evento?")) return;

            fetch('/api/eventos/eliminar', {
                method: 'POST',
                body: JSON.stringify({id: id}),
                headers: {'Content-Type': 'application/json'}
            }).then(res => res.json())
            .then(data => {
                modal.style.display = "none";
                calendar.refetchEvents();
            });
        }
    </script>
</body>
</html>