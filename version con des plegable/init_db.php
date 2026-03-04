<?php
// Ejecutar una sola vez para crear la base de datos
$dbFile = __DIR__ . '/muestras.db';

try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("DROP TABLE IF EXISTS muestras");
    $db->exec("CREATE TABLE muestras (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        alta DATE,
        vencimiento DATE,
        cliente VARCHAR,
        tipo VARCHAR,
        nombre_buque VARCHAR,
        partida INT,
        vbd FLOAT,
        tbd FLOAT,
        dr FLOAT,
        rco2 FLOAT,
        raire FLOAT,
        resistividad FLOAT,
        aceite FLOAT,
        humedad FLOAT,
        mv FLOAT,
        ih TINYINT,
        s FLOAT,
        si INT,
        fe INT,
        ca INT,
        na INT,
        v INT,
        ni INT,
        ti INT,
        p INT,
        ubicacion VARCHAR UNIQUE
    )");

    echo "Base de datos creada exitosamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
