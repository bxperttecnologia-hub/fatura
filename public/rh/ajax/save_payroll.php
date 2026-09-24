<?php
require_once '../../../app/config/db.php';
require_once '../helpers/payroll_calc.php';
session_start();

$company_id = (int)($_SESSION['user']['company_id'] ?? 0);

$employee_id     = (int)($_POST['employee_id'] ?? 0);
$reference_month = $_POST['reference_month'] ?? '';
$payment_date    = $_POST['payment_date'] ?? null;
$status          = $_POST['status'] ?? 'Pendente';
$id              = $_POST['id'] ?? null;

if (!$company_id || !$employee_id || !$reference_month) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    exit;
}

try {
    $calc = calculate_employee_payroll($pdo, $company_id, $employee_id, $reference_month, [
        'base_salary'            => $_POST['base_salary'] ?? 0,
        'bonuses'                => $_POST['bonuses'] ?? 0,
        'food_allowance'         => $_POST['food_allowance'] ?? 0,
        'transport_allowance'    => $_POST['transport_allowance'] ?? 0,
        'vacation_subsidy_pct'   => $_POST['vacation_subsidy_pct'] ?? 0,
        'thirteenth_subsidy_pct' => $_POST['thirteenth_subsidy_pct'] ?? 0,
        'commissions'            => $_POST['commissions'] ?? 0,
        'sales'                  => $_POST['sales'] ?? 0,
        'discounts'              => $_POST['discounts'] ?? 0,
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

if ($id) {
    $stmt = $pdo->prepare("UPDATE payroll
        SET reference_month = ?, base_salary = ?, bonuses = ?, food_allowance = ?, transport_allowance = ?, vacation_subsidy_pct = ?, thirteenth_subsidy_pct = ?, commissions = ?, sales = ?, discounts = ?, inss_value = ?, irt_value = ?, net_salary = ?, payment_date = ?, status = ?
        WHERE id = ? AND company_id = ?");
    $stmt->execute([
        $calc['reference_month'],
        $calc['base_salary'],
        $calc['bonuses'],
        $calc['food_allowance'],
        $calc['transport_allowance'],
        $calc['vacation_subsidy_pct'],
        $calc['thirteenth_subsidy_pct'],
        $calc['commissions'],
        $calc['sales'],
        $calc['discounts'],
        $calc['inss_value'],
        $calc['irt_value'],
        $calc['net_salary'],
        $payment_date,
        $status,
        $id,
        $company_id
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO payroll
        (employee_id, company_id, reference_month, base_salary, bonuses, food_allowance, transport_allowance, vacation_subsidy_pct, thirteenth_subsidy_pct, commissions, sales, discounts, inss_value, irt_value, net_salary, payment_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $employee_id,
        $company_id,
        $calc['reference_month'],
        $calc['base_salary'],
        $calc['bonuses'],
        $calc['food_allowance'],
        $calc['transport_allowance'],
        $calc['vacation_subsidy_pct'],
        $calc['thirteenth_subsidy_pct'],
        $calc['commissions'],
        $calc['sales'],
        $calc['discounts'],
        $calc['inss_value'],
        $calc['irt_value'],
        $calc['net_salary'],
        $payment_date,
        $status
    ]);
}

echo json_encode(['success' => true]);
