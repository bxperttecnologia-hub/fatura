<?php
require_once '../../../app/config/db.php';
session_start();

header('Content-Type: application/json');

$company_id = $_SESSION['user']['company_id'] ?? null;
if (!$company_id) {
    http_response_code(401);
    echo json_encode(['data' => []]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        d.id,
        d.name,
        d.parent_department_id,
        parent.name AS parent_name,
        (SELECT COUNT(*) FROM employees e WHERE e.department_id = d.id AND e.company_id = d.company_id) AS employees_count
    FROM departments d
    LEFT JOIN departments parent ON parent.id = d.parent_department_id AND parent.company_id = d.company_id
    WHERE d.company_id = ?
    ORDER BY d.name ASC
");
$stmt->execute([$company_id]);

echo json_encode(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
