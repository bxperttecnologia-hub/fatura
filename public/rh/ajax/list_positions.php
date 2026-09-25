<?php
require_once '../../../app/config/db.php';
session_start();
header('Content-Type: application/json');
$company_id = $_SESSION['user']['company_id'] ?? null;

if (!$company_id) {
    echo json_encode(['data' => []]);
    exit;
}

// Fase 1: expõe department_id (para o formulário de edição) e o nome
// do departamento (para a tabela), via LEFT JOIN — um cargo sem
// departamento definido continua a aparecer normalmente.
$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.suggested_salary, p.food_allowance, p.transport_allowance,
           p.vacation_subsidy_pct, p.thirteenth_subsidy_pct,
           p.department_id, d.name AS department_name
    FROM positions p
    LEFT JOIN departments d ON d.id = p.department_id AND d.company_id = p.company_id
    WHERE p.company_id = ?
    ORDER BY p.name ASC
");
$stmt->execute([$company_id]);

$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data' => $positions]);
