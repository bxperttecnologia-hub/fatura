<?php
require_once '../../../app/config/db.php';
session_start();

$company_id = $_SESSION['user']['company_id']; // ou ajuste conforme sua sessão

// Fase 1: expõe department_id/manager_id (para o formulário de edição)
// e os respetivos nomes (para a tabela), via LEFT JOIN — um funcionário
// sem departamento/chefia definidos continua a aparecer normalmente.
$stmt = $pdo->prepare("
    SELECT
        e.id, e.name, e.bi, e.position, e.position_id,
        e.salary_base AS salary, e.status, e.document_type, e.birth_date,
        e.marital_status, e.academic_level, e.contract_type, e.admission_date,
        e.iban, e.photo_url, e.doc1_url, e.doc2_url,
        e.department_id, dept.name AS department_name,
        e.manager_id, mgr.name AS manager_name
    FROM employees e
    LEFT JOIN departments dept ON dept.id = e.department_id AND dept.company_id = e.company_id
    LEFT JOIN employees mgr ON mgr.id = e.manager_id AND mgr.company_id = e.company_id
    WHERE e.company_id = ?
    ORDER BY e.name ASC
");
$stmt->execute([$company_id]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data' => $employees]);
