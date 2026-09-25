<?php
require_once '../../../app/config/db.php';
session_start();
header('Content-Type: application/json');

$company_id = $_SESSION['user']['company_id'] ?? null;
$id = (int)($_POST['id'] ?? 0);

if (!$company_id || !$id) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Impede deletar se estiver em uso por algum funcionário.
// Fase 1: verifica primeiro por position_id (a fonte de verdade); soma
// também quem ainda só tem o texto (TRIM) por ligar, para não deixar
// apagar um cargo em uso só porque a migração ainda não correu para
// esse funcionário.
$stmt = $pdo->prepare('
    SELECT COUNT(*) FROM employees
    WHERE company_id = ?
      AND (
        position_id = ?
        OR (position_id IS NULL AND TRIM(position) = (SELECT TRIM(name) FROM positions WHERE id = ? AND company_id = ?))
      )
');
$stmt->execute([$company_id, $id, $id, $company_id]);
$inUse = (int)$stmt->fetchColumn();
if ($inUse > 0) {
    echo json_encode(['success' => false, 'message' => 'Não é possível eliminar: há funcionários vinculados a este cargo.']);
    exit;
}

$stmt = $pdo->prepare('DELETE FROM positions WHERE id = ? AND company_id = ?');
$ok = $stmt->execute([$id, $company_id]);

echo json_encode(['success' => (bool)$ok]);
