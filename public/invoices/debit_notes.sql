-- Nota de Débito: tabelas novas (executar uma vez, ANTES de usar os ficheiros PHP actualizados)
CREATE TABLE IF NOT EXISTS debit_notes (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  invoice_id           INT NOT NULL,
  contact_id           INT NOT NULL,
  company_id           INT NOT NULL,
  serie                VARCHAR(10) NOT NULL DEFAULT 'ND',
  number               INT UNSIGNED NOT NULL,
  issue_date           DATE NOT NULL,
  reason               TEXT NOT NULL,
  currency             VARCHAR(10) NOT NULL,
  subtotal_without_tax DECIMAL(18,2) NOT NULL DEFAULT 0,
  total_tax            DECIMAL(18,2) NOT NULL DEFAULT 0,
  final_total          DECIMAL(18,2) NOT NULL DEFAULT 0,
  user_id              INT NULL,
  created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_nd_company_serie_number (company_id, serie, number),
  KEY idx_nd_invoice (invoice_id),
  KEY idx_nd_company (company_id),
  CONSTRAINT fk_nd_invoice FOREIGN KEY (invoice_id)
    REFERENCES invoices(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS debit_note_items (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  debit_note_id INT UNSIGNED NOT NULL,
  description   VARCHAR(500) NOT NULL,
  quantity      DECIMAL(14,3) NOT NULL DEFAULT 1,
  unit_price    DECIMAL(18,2) NOT NULL DEFAULT 0,
  tax           DECIMAL(5,2) NOT NULL DEFAULT 0,
  KEY idx_ndi_note (debit_note_id),
  CONSTRAINT fk_ndi_note FOREIGN KEY (debit_note_id)
    REFERENCES debit_notes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
