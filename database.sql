CREATE DATABASE IF NOT EXISTS recuerdos_pareja
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recuerdos_pareja;

CREATE TABLE IF NOT EXISTS recuerdos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    tipo ENUM('imagen','video') NOT NULL,
    descripcion VARCHAR(500) DEFAULT '',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
