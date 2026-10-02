-- Nota de Entrega: tabelas novas (executar uma vez)
CREATE TABLE IF NOT EXISTS delivery_notes (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  invoice_id      INT NOT NULL,
  contact_id      INT NOT NULL,
  company_id      INT NOT NULL,
  serie           VARCHAR(10) NOT NULL DEFAULT 'NE',
  number          INT UNSIGNED NOT NULL,
  issue_date      DATE NOT NULL,
  delivery_address VARCHAR(255) NULL,
  notes           TEXT NULL,
  user_id         INT NULL,
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_dn_company_serie_number (company_id, serie, number),
  KEY idx_dn_invoice (invoice_id),
  KEY idx_dn_company (company_id),
  CONSTRAINT fk_dn_invoice FOREIGN KEY (invoice_id)
    REFERENCES invoices(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS delivery_note_items (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  delivery_note_id INT UNSIGNED NOT NULL,
  invoice_item_id  INT NULL,
  item_id          INT NULL,
  description      VARCHAR(500) NULL,
  quantity         DECIMAL(14,3) NOT NULL DEFAULT 0,
  KEY idx_dni_note (delivery_note_id),
  KEY idx_dni_invoice_item (invoice_item_id),
  CONSTRAINT fk_dni_note FOREIGN KEY (delivery_note_id)
    REFERENCES delivery_notes(id) ON DELETE CASCADE,
  CONSTRAINT fk_dni_invoice_item FOREIGN KEY (invoice_item_id)
    REFERENCES invoice_items(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
