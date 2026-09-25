<?php
require_once '../../../app/config/db.php';
session_start();

header('Content-Type: application/json');

$company_id = $_SESSION['user']['company_id'] ?? null;
if (!$company_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sessão expirada ou inválida.']);
    exit;
}

$id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$name = trim($_POST['name'] ?? '');
$parent_department_id = !empty($_POST['parent_department_id']) ? (int)$_POST['parent_department_id'] : null;

if ($name === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nome do departamento é obrigatório.']);
    exit;
}

if ($parent_department_id !== null) {

    if ($id !== null && $parent_department_id === $id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Um departamento não pode ser pai de si próprio.']);
        exit;
    }

    // Confirma que o pai pertence à mesma empresa.
    $stmtParent = $pdo->prepare("SELECT id, parent_department_id FROM departments WHERE id = ? AND company_id = ?");
    $stmtParent->execute([$parent_department_id, $company_id]);
    $parent = $stmtParent->fetch(PDO::FETCH_ASSOC);

    if (!$parent) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Departamento-pai inválido.']);
        exit;
    }

    // Impede ciclos: ao editar um departamento existente, o novo pai
    // (ou qualquer antepassado do novo pai) não pode ser o próprio
    // departamento que estamos a gravar.
    if ($id !== null) {
        $ancestorId = $parent['parent_department_id'];
        $depth = 0;
        while ($ancestorId !== null && $depth < 50) {
            if ((int)$ancestorId === $id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Essa hierarquia criaria um ciclo entre departamentos.']);
                exit;
            }
            $stmtAsc = $pdo->prepare("SELECT parent_department_id FROM departments WHERE id = ? AND company_id = ?");
            $stmtAsc->execute([$ancestorId, $company_id]);
            $ancestorId = $stmtAsc->fetchColumn();
            $depth++;
        }
    }
}

if ($id) {
    $stmt = $pdo->prepare("UPDATE departments SET name = ?, parent_department_id = ? WHERE id = ? AND company_id = ?");
    $stmt->execute([$name, $parent_department_id, $id, $company_id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO departments (company_id, name, parent_department_id) VALUES (?, ?, ?)");
    $stmt->execute([$company_id, $name, $parent_department_id]);
    $id = $pdo->lastInsertId();
}

echo json_encode(['success' => true, 'id' => $id]);
