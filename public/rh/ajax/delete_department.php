<?php
require_once '../../../app/config/db.php';
session_start();

header('Content-Type: application/json');

$company_id = $_SESSION['user']['company_id'] ?? null;
$id = (int)($_POST['id'] ?? 0);

if (!$company_id || !$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// Bloqueia a eliminação se o departamento ainda estiver em uso —
// mesmo padrão de delete_position.php / delete_employee.php: as FKs
// têm ON DELETE SET NULL, mas eliminar "em silêncio" desligaria
// funcionários, cargos ou subdepartamentos sem o utilizador perceber.
$stmt = $pdo->prepare("
    SELECT
        (SELECT COUNT(*) FROM employees   WHERE department_id = ? AND company_id = ?) AS employees_count,
        (SELECT COUNT(*) FROM positions   WHERE department_id = ? AND company_id = ?) AS positions_count,
        (SELECT COUNT(*) FROM departments WHERE parent_department_id = ? AND company_id = ?) AS children_count
");
$stmt->execute([$id, $company_id, $id, $company_id, $id, $company_id]);
$counts = $stmt->fetch(PDO::FETCH_ASSOC);

if (($counts['employees_count'] ?? 0) > 0 || ($counts['positions_count'] ?? 0) > 0 || ($counts['children_count'] ?? 0) > 0) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => 'Não é possível eliminar: há funcionários, cargos ou subdepartamentos associados a este departamento. Reatribui-os primeiro.'
    ]);
    exit;
}

$stmt = $pdo->prepare('DELETE FROM departments WHERE id = ? AND company_id = ?');
$ok = $stmt->execute([$id, $company_id]);

echo json_encode(['success' => (bool)$ok]);
