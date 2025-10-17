-- db.sql
-- Création de la base et des tables pour l'application vulnérable
-- À placer dans ./app/db.sql pour initialisation automatique par l'image MySQL

DROP DATABASE IF EXISTS vulnerable_app;
CREATE DATABASE IF NOT EXISTS vulnerable_app DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE vulnerable_app;

-- Table users : id, username, password (en clair pour l'exercice), balance = "nbre de thune"
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table loan_requests : id, user_id (FK vers users), amount, description, created_at
DROP TABLE IF EXISTS loan_requests;
CREATE TABLE loan_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  description TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_loan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index utile pour rechercher les prêts par utilisateur
CREATE INDEX idx_loan_user ON loan_requests(user_id);

-- Données d'exemple
INSERT INTO users (username, password, balance) VALUES
('alice', 'alice123', 1500.00),
('bob', 'bobpass', 250.50),
('charlie', 'charliepwd', 10000.00);

INSERT INTO loan_requests (user_id, amount, description, status) VALUES
(1, 5000.00, 'Rénovation appartement - devis joint', 'pending'),
(2, 1200.00, 'Achat matériel photo', 'pending'),
(1, 200.00, 'Avance salaire', 'approved');

-- Vérification simple (optionnel)
-- SELECT * FROM users;
-- SELECT * FROM loan_requests;
