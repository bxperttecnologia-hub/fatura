<?php
require_once '../../../app/config/db.php';
session_start();

$company_id = $_SESSION['user']['company_id'];
$term = $_GET['term'] ?? '';

// Fase 1: a ligação principal passa a ser por e.position_id = p.id
// (chave, não texto). Mantemos um fallback por TRIM(nome) só para
// funcionários cujo position_id ainda não tenha sido preenchido pela
// migração (rh/migrations/migrate_fase1_position_id.php) — depois de
// essa migração correr em todas as empresas, o fallback nunca chega a
// ser usado, mas fica como rede de segurança em vez de voltar a
// "desaparecer" funcionários dos selects.
$sql = "SELECT e.id, e.name, e.salary_base AS salary, e.position, p.name as position_name, p.suggested_salary, p.food_allowance, p.transport_allowance, p.vacation_subsidy_pct, p.thirteenth_subsidy_pct FROM employees as e
        LEFT JOIN positions as p ON p.company_id = e.company_id
            AND (
                (e.position_id IS NOT NULL AND p.id = e.position_id)
                OR (e.position_id IS NULL AND TRIM(p.name) = TRIM(e.position))
            )
        WHERE e.company_id = ? AND e.status = 'ativo' AND e.name LIKE ? 
        ORDER BY e.name ASC LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute([$company_id, "%$term%"]);

$results = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $results[] = [
        'id' => $row['id'],
        'text' => $row['name'],
        'salary' => $row['salary'],
        'position' => $row['position'],
        // Com LEFT JOIN, um funcionário sem cargo correspondente em `positions`
        // vem com estes campos a NULL — normaliza para 0 para não quebrar o JS.
        'sub_suge' => $row['suggested_salary'] ?? 0,
        'sub_alim' => $row['food_allowance'] ?? 0,
        'sub_trans' => $row['transport_allowance'] ?? 0,
        'sub_ferias' => $row['vacation_subsidy_pct'] ?? 0,
        'sub_decimo' => $row['thirteenth_subsidy_pct'] ?? 0
    ];
}

echo json_encode($results);
