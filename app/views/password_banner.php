<?php
// Incluir no layout das páginas autenticadas (ex.: no topo do footer.php).
// Precisa de $pdo, Bootstrap 5 e jQuery já carregados.
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) return;

$stmt = $pdo->prepare("SELECT must_change_password FROM users WHERE id = ?");
$stmt->execute([(int) $_SESSION['user_id']]);
if (!$stmt->fetchColumn()) return;
?>

<div class="alert alert-warning d-flex justify-content-between align-items-center mb-0 rounded-0" id="pwBanner" role="alert">
    <span><i class="bi bi-shield-lock me-2"></i>Defina a sua senha para poder entrar novamente.</span>
    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#pwModal">Definir senha</button>
</div>

<div class="modal fade" id="pwModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="pwForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Defina a sua senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">A sua conta foi criada com o telefone verificado. Escolha uma senha para entrar da próxima vez.</p>
                    <div class="mb-3">
                        <label class="form-label" for="pw1">Nova senha</label>
                        <input class="form-control" type="password" id="pw1" name="password" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="pw2">Confirmar senha</label>
                        <input class="form-control" type="password" id="pw2" name="password_confirm" required>
                    </div>
                    <small class="text-muted">Mínimo 6 caracteres, com maiúscula, minúscula e um caractere especial (!@#$%^&amp;*).</small>
                    <div id="pwErro" class="text-danger small mt-2 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="pwBtn" class="btn btn-success">Guardar senha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        var el = document.getElementById('pwModal');
        if (window.bootstrap) new bootstrap.Modal(el).show(); // abre ao entrar; o banner fica até definir a senha

        $('#pwForm').on('submit', function(e) {
            e.preventDefault();
            var $b = $('#pwBtn').prop('disabled', true),
                $err = $('#pwErro').addClass('d-none');

            $.ajax({
                    url: 'register/ajax/set_password.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json'
                })
                .done(function() {
                    bootstrap.Modal.getInstance(el).hide();
                    $('#pwBanner').remove();
                    if (typeof Swal !== 'undefined') Swal.fire({
                        icon: 'success',
                        title: 'Senha definida!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                })
                .fail(function(xhr) {
                    $err.text((xhr.responseJSON && xhr.responseJSON.message) || 'Erro. Tente novamente.').removeClass('d-none');
                })
                .always(function() {
                    $b.prop('disabled', false);
                });
        });
    });
</script>