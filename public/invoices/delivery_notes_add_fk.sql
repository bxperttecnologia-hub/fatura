-- Executar UMA vez, apenas se as tabelas delivery_notes / delivery_note_items
-- JA existem sem chaves estrangeiras. (Em instalacao nova use delivery_notes.sql.)

-- 1) Verificar orfaos: ambas as consultas devem devolver 0 linhas.
SELECT dn.id FROM delivery_notes dn
  LEFT JOIN invoices i ON i.id = dn.invoice_id WHERE i.id IS NULL;
SELECT dni.id FROM delivery_note_items dni
  LEFT JOIN invoice_items ii ON ii.id = dni.invoice_item_id
  WHERE dni.invoice_item_id IS NOT NULL AND ii.id IS NULL;

-- 2) Criar as chaves estrangeiras (nao permitem apagar factura/linha com nota emitida).
ALTER TABLE delivery_notes
  ADD CONSTRAINT fk_dn_invoice FOREIGN KEY (invoice_id)
  REFERENCES invoices(id) ON DELETE RESTRICT;

ALTER TABLE delivery_note_items
  ADD CONSTRAINT fk_dni_invoice_item FOREIGN KEY (invoice_item_id)
  REFERENCES invoice_items(id) ON DELETE RESTRICT;
