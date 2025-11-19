<?php
require_once __DIR__ . '/../config/Database.php';

class CalendarController {

    // 1. Cargar la vista del calendario
    public function index() {
        // Verificamos si está logueado
        if (!isset($_SESSION['is_logged_in'])) { header('Location: /login'); exit(); }
        
        // Ajustamos la ruta a tu estructura actual (Views con V minúscula)
        require __DIR__ . '/../views/html/calendario.php';
    }

    // 2. API: Devolver eventos en formato JSON (Para que el calendario los pinte)
    public function getEvents() {
        $pdo = Database::connect();
        // Traemos ID, Título, Inicio, Fin y Color
        $stmt = $pdo->query("SELECT id, title, start_event as start, end_event as end, color FROM Eventos");
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($eventos);
    }

    // 3. API: Guardar Evento (Crear o Editar)
    public function saveEvent() {
        // SEGURIDAD: Solo Admins (Rol 1) pueden guardar cambios
        if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] != 1) { 
            http_response_code(403); // Prohibido
            echo json_encode(['status' => 'error', 'message' => 'Solo admins']);
            exit(); 
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $pdo = Database::connect();

        try {
            if (isset($data['id']) && !empty($data['id'])) {
                // EDITAR UN EVENTO EXISTENTE (Aunque no sea tuyo, si sos admin podés)
                $sql = "UPDATE Eventos SET title=?, start_event=?, end_event=?, color=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$data['title'], $data['start'], $data['end'], $data['color'], $data['id']]);
            } else {
                // CREAR NUEVO EVENTO
                $sql = "INSERT INTO Eventos (title, start_event, end_event, color, created_by) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$data['title'], $data['start'], $data['end'], $data['color'], $_SESSION['user_id']]);
            }
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // 4. API: Eliminar Evento
    public function deleteEvent() {
        // SEGURIDAD: Solo Admins pueden borrar
        if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] != 1) { 
            http_response_code(403); 
            exit(); 
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $pdo = Database::connect();
        
        // Borra el evento sin importar quién lo creó
        $stmt = $pdo->prepare("DELETE FROM Eventos WHERE id = ?");
        $stmt->execute([$data['id']]);
        
        echo json_encode(['status' => 'deleted']);
    }
}
?>