<?php
require_once '../app/config/db.php';
require_once '../app/helpers/authentication.php';
require_once '../app/helpers/subscription.php';

try {
    subscription_require_feature($pdo, (int)($_SESSION['user']['company_id'] ?? 0), 'rh');
} catch (Exception $e) {
    $cid = (int)($_SESSION['user']['company_id'] ?? 0);
    header('Location: subscription.php?company_id=' . $cid . '&upgrade=rh');
    exit;
}

require_once '../app/views/layout_creation.php';
?>

<style>
    /* ===== TABELA ESTILO (igual a positions.php) ===== */
    #departmentsTable {
        border-collapse: separate;
        border-spacing: 0 12px;
        width: 100%;
    }

    #departmentsTable thead th {
        border: none;
        font-size: 12px;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 16px;
        text-align: left;
        border-right: 1px solid #e5e7eb57;
    }

    #departmentsTable thead th:last-child {
        border-right: none;
    }

    #departmentsTable tbody tr td {
        border-right: 1px solid #e5e7eb57;
    }

    #departmentsTable tbody tr {
        background: #fff !important;
        border-radius: 14px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        text-align: left !important;
    }

    #departmentsTable tbody tr:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    #departmentsTable tbody td {
        border: none;
        padding: 18px 16px;
        vertical-align: middle;
        font-size: 0.95rem;
        background: #fff !important;
        text-align: left !important;
    }

    #departmentsTable thead td {
        background: #111 !important;
        display: none;
        max-width: 80px !important;
    }

    #departmentsTable tbody td:first-child {
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        background: #fff !important;
        font-weight: 600;
        color: #111;
    }

    #departmentsTable tbody th {
        text-align: left !important;
    }

    #departmentsTable tbody td:last-child {
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
        text-align: right;
        padding-right: 24px;
    }
</style>

<main class="main-content">
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-12">
                <div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 fw-bold mt-5">Departamentos</h2>
                        <button class="btn btn-primary d-flex align-items-center justify-items-center align-content-center" data-bs-toggle="modal" data-bs-target="#modalDepartment">
                            <i class="material-icons-round">add</i>
                            Adicionar Departamento
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="departmentsTable" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Departamento-pai</th>
                                        <th>Funcionários</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Departamento -->
<div class="modal fade" id="modalDepartment" tabindex="-1" aria-labelledby="modalDepartmentLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formDepartment">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDepartmentLabel">Cadastrar Departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome do Departamento</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Departamento-pai (opcional)</label>
                        <select name="parent_department_id" id="parentDepartmentSelect" class="form-select">
                            <option value="">Nenhum (topo da hierarquia)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        let currentEditId = null;

        // Carrega as opções de "departamento-pai". Ao editar, remove
        // o próprio departamento da lista (não pode ser pai de si mesmo).
        function loadParentOptions(excludeId) {
            return $.getJSON('rh/ajax/list_departments.php', function(resp) {
                const data = (resp && resp.data) || [];
                const options = data
                    .filter(d => !excludeId || String(d.id) !== String(excludeId))
                    .map(d => `<option value="${d.id}">${d.name}</option>`)
                    .join('');
                $('#parentDepartmentSelect').html(`<option value="">Nenhum (topo da hierarquia)</option>${options}`);
            });
        }

        const table = $('#departmentsTable').DataTable({
            ajax: 'rh/ajax/list_departments.php',
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            columns: [{
                    data: 'name'
                },
                {
                    data: 'parent_name',
                    render: data => data || '<span class="text-muted">—</span>'
                },
                {
                    data: 'employees_count',
                    render: data => parseInt(data || 0)
                },
                {
                    data: null,
                    render: function(row) {
                        return `
                          <button class='btn btn-sm text-warning editDepartment'
                            data-id='${row.id}'
                            data-name='${row.name}'
                            data-parent_department_id='${row.parent_department_id || ''}'
                          ><i class="bi bi-pencil"></i></button>
                          <button class='btn btn-sm text-danger deleteDepartment'
                            data-id='${row.id}'
                            data-name='${row.name}'
                          ><i class="bi bi-trash"></i></button>
                        `;
                    }
                }
            ]
        });

        $('#modalDepartment').on('show.bs.modal', function() {
            if (!currentEditId) {
                loadParentOptions(null);
            }
        });

        $('#formDepartment').on('submit', function(e) {
            e.preventDefault();
            $.post('rh/ajax/save_department.php', $(this).serialize(), function(resp) {
                if (!resp || !resp.success) {
                    Swal.fire('Erro', (resp && resp.message) || 'Não foi possível salvar.', 'error');
                    return;
                }
                $('#modalDepartment').modal('hide');
                table.ajax.reload();
                Swal.fire('Sucesso', 'Departamento salvo com sucesso!', 'success');
                $('#editDepartmentId').remove();
                currentEditId = null;
            }, 'json').fail(function(xhr) {
                const resp = xhr.responseJSON;
                Swal.fire('Erro', (resp && resp.message) || 'Não foi possível salvar.', 'error');
            });
        });

        $('#departmentsTable').on('click', '.editDepartment', function() {
            const btn = $(this);
            currentEditId = btn.data('id');

            loadParentOptions(currentEditId).then(function() {
                $('#formDepartment input[name=name]').val(btn.data('name'));
                $('#parentDepartmentSelect').val(btn.data('parent_department_id') || '');
            });

            $('#formDepartment').append(`<input type="hidden" name="id" value="${currentEditId}" id="editDepartmentId">`);
            $('#modalDepartment').modal('show');
        });

        $('#modalDepartment').on('hidden.bs.modal', function() {
            $('#formDepartment')[0].reset();
            $('#editDepartmentId').remove();
            currentEditId = null;
        });

        $('#departmentsTable').on('click', '.deleteDepartment', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Eliminar departamento?',
                text: `Tem certeza que deseja eliminar o departamento "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.post('rh/ajax/delete_department.php', {
                    id
                }, function(resp) {
                    if (resp.success) {
                        table.ajax.reload();
                        Swal.fire('Ok', 'Departamento eliminado.', 'success');
                    } else {
                        Swal.fire('Erro', resp.message || 'Não foi possível eliminar.', 'error');
                    }
                }, 'json');
            });
        });
    });
</script>

<?php require_once '../app/views/footer.php'; ?>
