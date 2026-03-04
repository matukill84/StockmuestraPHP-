<?php
class DB {
    public PDO $db;

    public function __construct(string $dbFile = null) {
        if ($dbFile === null) {
            $dbFile = __DIR__ . '/muestras.db';
        }
        $this->db = new PDO('sqlite:' . $dbFile);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
