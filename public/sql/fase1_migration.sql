-- =====================================================================
-- FASE 1 — Reestruturação de dados (departamentos, position_id,
-- department_id, manager_id)
--
-- ORDEM DE EXECUÇÃO: correr este ficheiro ANTES de colocar em produção
-- o código da Fase 1 (search_employees.php, delete_position.php,
-- employees.php, save_employee.php, departments.php e ajax
-- relacionados) e ANTES de correr
-- rh/migrations/migrate_fase1_position_id.php.
--
-- Nenhuma instrução aqui apaga dados. Faz backup da base antes de
-- correr, como combinado.
--
-- REQUISITO DE VERSÃO: "ADD COLUMN IF NOT EXISTS" / "ADD KEY IF NOT
-- EXISTS" só funcionam em MySQL 8.0.29+ ou MariaDB 10.x+. Se o teu
-- servidor for mais antigo, corre `SELECT VERSION();` primeiro e avisa
-- -me: removo os "IF NOT EXISTS" e faço a verificação manualmente via
-- information_schema, como já faço para as FOREIGN KEYs abaixo.
-- =====================================================================

START TRANSACTION;

-- 1) Departamentos, com hierarquia (departamento-pai opcional)
CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  parent_department_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_departments_company (company_id),
  KEY idx_departments_parent (parent_department_id),
  CONSTRAINT fk_departments_parent
    FOREIGN KEY (parent_department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) positions passa a poder pertencer a um departamento
ALTER TABLE positions
  ADD COLUMN IF NOT EXISTS department_id INT NULL AFTER name,
  ADD KEY IF NOT EXISTS idx_positions_department (department_id);

-- MySQL < 8.0.29 não suporta "ADD FOREIGN KEY IF NOT EXISTS"; a FK é
-- adicionada à parte, protegida por uma verificação de existência.
SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'positions'
    AND CONSTRAINT_NAME = 'fk_positions_department'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE positions ADD CONSTRAINT fk_positions_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) employees ganha position_id, department_id e manager_id.
--    employees.position (texto) é MANTIDO como cache de leitura —
--    não apagar nesta fase.
ALTER TABLE employees
  ADD COLUMN IF NOT EXISTS position_id INT NULL AFTER position,
  ADD COLUMN IF NOT EXISTS department_id INT NULL AFTER position_id,
  ADD COLUMN IF NOT EXISTS manager_id INT NULL AFTER department_id,
  ADD KEY IF NOT EXISTS idx_employees_position_id (position_id),
  ADD KEY IF NOT EXISTS idx_employees_department_id (department_id),
  ADD KEY IF NOT EXISTS idx_employees_manager_id (manager_id);

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'employees'
    AND CONSTRAINT_NAME = 'fk_employees_position'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE employees ADD CONSTRAINT fk_employees_position FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'employees'
    AND CONSTRAINT_NAME = 'fk_employees_department'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE employees ADD CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'employees'
    AND CONSTRAINT_NAME = 'fk_employees_manager'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE employees ADD CONSTRAINT fk_employees_manager FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;

-- =====================================================================
-- A seguir a este ficheiro, correr:
--   php rh/migrations/migrate_fase1_position_id.php
-- para preencher employees.position_id a partir do texto existente em
-- employees.position.
-- =====================================================================
