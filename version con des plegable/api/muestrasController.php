<?php
require_once __DIR__ . '/db.php';

class MuestrasController {
    private DB $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getAll(): void {
        $stmt = $this->db->db->query("SELECT * FROM muestras ORDER BY id DESC");
        $rows = $stmt->fetchAll();
        $this->jsonResponse($rows);
    }

    public function getById(int $id): void {
        $stmt = $this->db->db->prepare("SELECT * FROM muestras WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            $this->errorResponse(404, "No encontrado");
            return;
        }
        $this->jsonResponse($row);
    }

    public function delete(int $id): void {
        $stmt = $this->db->db->prepare("DELETE FROM muestras WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            $this->errorResponse(404, "No encontrado");
            return;
        }
        $this->jsonResponse(['deletedId' => $id]);
    }

    public function create(array $body): void {
        $fields = ['alta','vencimiento','cliente','tipo','nombre_buque','partida','vbd','tbd','dr','rco2','raire','resistividad','aceite','humedad','mv','ih','s','si','fe','ca','na','v','ni','ti','p','ubicacion'];
        $values = array_map(fn($f) => $body[$f] ?? null, $fields);

        try {
            $placeholders = implode(',', array_fill(0, count($fields), '?'));
            $cols = implode(',', $fields);
            $stmt = $this->db->db->prepare("INSERT INTO muestras ($cols) VALUES ($placeholders)");
            $stmt->execute($values);
            $this->jsonResponse(['id' => $this->db->db->lastInsertId()]);
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'UNIQUE constraint failed: muestras.ubicacion')) {
                $this->errorResponse(409, "Ya existe una muestra en la ubicación: " . ($body['ubicacion'] ?? ''));
            } elseif (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                preg_match('/UNIQUE constraint failed: (\w+)\.(\w+)/', $e->getMessage(), $m);
                $this->errorResponse(409, "Violación de UNIQUE en tabla {$m[1]}, columna {$m[2]}");
            } else {
                $this->errorResponse(500, $e->getMessage());
            }
        }
    }

    public function update(int $id, array $body): void {
        $fields = ['alta','vencimiento','cliente','tipo','nombre_buque','partida','vbd','tbd','dr','rco2','raire','resistividad','aceite','humedad','mv','ih','s','si','fe','ca','na','v','ni','ti','p','ubicacion'];
        $values = array_map(fn($f) => $body[$f] ?? null, $fields);
        $values[] = $id;

        try {
            $set = implode(',', array_map(fn($f) => "$f=?", $fields));
            $stmt = $this->db->db->prepare("UPDATE muestras SET $set WHERE id=?");
            $stmt->execute($values);
            $this->jsonResponse(['changes' => $stmt->rowCount()]);
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                $this->errorResponse(409, "Campo duplicado");
            } else {
                $this->errorResponse(500, $e->getMessage());
            }
        }
    }

    public function generateQR(int $id): void {
        $stmt = $this->db->db->prepare("SELECT * FROM muestras WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            $this->errorResponse(404, "No encontrado");
            return;
        }

        // Generate QR using Google Charts API (no external library needed)
        $data = urlencode(json_encode($row));
        $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=$data";
        $this->jsonResponse(['qr_url' => $url, 'data' => $row]);
    }

    private function jsonResponse(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function errorResponse(int $code, string $message): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    }
}
