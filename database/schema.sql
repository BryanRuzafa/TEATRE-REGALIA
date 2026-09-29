-- Script SQL de Creació i Dades Inicials - TR 2DAW
-- Aquest script és una representació de l'estructura de la base de dades SQLite.

-- 1. Taula d'Esdeveniments
CREATE TABLE IF NOT EXISTS esdeveniments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titol VARCHAR(255) NOT NULL,
    descripcio TEXT,
    data_hora DATETIME NOT NULL,
    recinte VARCHAR(255) NOT NULL,
    imatge_url VARCHAR(255),
    preu_base DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 2. Taula de Seients
CREATE TABLE IF NOT EXISTS seients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    esdeveniment_id INTEGER NOT NULL,
    id_referencia VARCHAR(255) NOT NULL,
    estat VARCHAR(255) DEFAULT 'disponible',
    reservat_per VARCHAR(255),
    temps_bloqueig_finalitza TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (esdeveniment_id) REFERENCES esdeveniments(id) ON DELETE CASCADE
);

-- 3. Taula de Vendes
CREATE TABLE IF NOT EXISTS vendes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    esdeveniment_id INTEGER NOT NULL,
    seient_id INTEGER NOT NULL,
    nom_comprador VARCHAR(255) NOT NULL,
    email_comprador VARCHAR(255) NOT NULL,
    preu_final DECIMAL(10,2) NOT NULL,
    codi_confirmacio VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (esdeveniment_id) REFERENCES esdeveniments(id) ON DELETE CASCADE,
    FOREIGN KEY (seient_id) REFERENCES seients(id) ON DELETE CASCADE
);

-- DADES INICIALS (Seeders)
INSERT INTO esdeveniments (titol, descripcio, data_hora, recinte, preu_base) 
VALUES ('Gran Estrena: El Gran Gatsby (Sala VIP)', 'Una experiència cinemàtica única amb la nostra millor sala de vellut vermell.', '2026-04-17 21:00:00', 'Auditori Regalia', 25.0);

-- Seients inicials per a l'esdeveniment 1
INSERT INTO seients (esdeveniment_id, id_referencia, estat) VALUES (1, '1', 'disponible');
INSERT INTO seients (esdeveniment_id, id_referencia, estat) VALUES (1, '2', 'disponible');
INSERT INTO seients (esdeveniment_id, id_referencia, estat) VALUES (1, '3', 'disponible');
INSERT INTO seients (esdeveniment_id, id_referencia, estat) VALUES (1, '4', 'disponible');
INSERT INTO seients (esdeveniment_id, id_referencia, estat) VALUES (1, '5', 'disponible');
