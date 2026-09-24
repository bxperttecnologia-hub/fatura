<?php
require_once '../app/helpers/translation.php';
require_once '../app/config/db.php';
require_once '../app/helpers/functions.php';
require_once '../app/views/head.php';
?>
<style>
    /* Cor de destaque do botão/foco. Para verde como na referência: --accent: #198754; */
    .login-col {
        --accent: var(--blue, #0d6efd);
        --login-bg: #f7f7f7;
        background: var(--login-bg);
    }

    .login-wrap {
        width: 100%;
        max-width: 420px;
        padding: 0 1.25rem;
    }

    .login-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #212529;
    }

    .field {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .field label {
        position: absolute;
        top: -0.65rem;
        left: 1.4rem;
        padding: 0 .5rem;
        background: var(--login-bg);
        font-size: .8rem;
        font-weight: 600;
        color: #6c757d;
        z-index: 2;
    }

    .field .form-control {
        height: 58px;
        border-radius: 999px;
        border: 1px solid #dcdcdc;
        background: #fff;
        padding: 0 3rem;
    }

    .field .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 .2rem rgba(0, 0, 0, .05);
    }

    .field .icon-left,
    .field .icon-right {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: #9aa0a6;
        z-index: 3;
        display: flex;
    }

    .field .icon-left { left: 1.1rem; }
    .field .icon-right { right: 1.1rem; cursor: pointer; }

    .btn-login {
        width: 100%;
        height: 52px;
        border: 0;
        border-radius: 999px;
        background: var(--accent);
        color: #fff;
        font-weight: 500;
        transition: opacity .15s;
    }

    .btn-login:hover { opacity: .9; }

    .login-divider {
        display: flex;
        align-items: center;
        gap: .75rem;
        color: #9aa0a6;
        font-size: .85rem;
        margin: 1.5rem 0 1rem;
    }

    .login-divider::before,
    .login-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e2e2e2;
    }

    .login-link {
        font-weight: 700;
        text-decoration: none;
        color: var(--accent);
    }
</style>

<div class="row w-100 mx-0 gx-0">

    <!-- Coluna do formulário -->
    <div class="col-12 col-sm-6 login-col">
        <div class="d-flex flex-column justify-content-between align-items-center min-vh-100">
            <div class="w-100 d-md-none d-flex justify-content-center aling-items-center">
                <?= gerarDropdownPaises($paises, $paisSelecionado); ?>
            </div>

            <div class="login-wrap my-auto py-4">
                <!-- Título -->
                <div class="text-center mb-4">
                    <h2 class="login-title mb-1"><?= t('Acesso') ?></h2>
                    <span class="text-grey"><?= t('Conecte-se com a melhor do mercado!') ?></span>
                </div>

                <!-- Erro -->
                <div class="alert alert-danger text-center d-none" role="alert" id="error-message">
                    <?= t('Usuário não localizado') ?>
                </div>

                <!-- Formulário -->
                <form id="loginForm">
                    <div class="field">
                        <label for="user_email"><?= t('Usuário, E-mail ou Telefone') ?></label>
                        <span class="icon-left"><i class="material-icons-outlined">person</i></span>
                        <input type="text" class="form-control" id="user_email" name="user_email"
                               placeholder="<?= t('Digite seu usuário, e-mail ou telefone') ?>"
                               autocomplete="username" required>
                    </div>

                    <div class="field">
                        <label for="password"><?= t('Senha') ?></label>
                        <span class="icon-left"><i class="material-icons-outlined">lock</i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="<?= t('Digite sua senha') ?>"
                               autocomplete="current-password" required>
                        <span class="icon-right" id="togglePassword">
                            <i class="material-icons-outlined">visibility</i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                            <label class="form-check-label" for="remember_me"><?= t('Lembrar de mim') ?></label>
                        </div>
                        <a href="forgot_password.php" id="forgotPassword" class="text-decoration-none" style="font-size:13px;"><?= t('Esqueci minha senha') ?></a>
                    </div>

                    <button id="btnAcessar" class="btn-login"><?= t('Acessar') ?></button>
                </form>

                <div class="login-divider"><?= t('ou') ?></div>

                <!-- Google login -->
                <div class="w-100 text-center">
                    <a href="loginGoogle/loginGoogle.php" class="d-flex justify-content-center align-items-center">
                        <button type="button" class="gsi-material-button">
                            <div class="gsi-material-button-state"></div>
                            <div class="gsi-material-button-content-wrapper">
                                <div class="gsi-material-button-icon">
                                    <!-- Ícone Google -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="display: block;">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                        <path fill="none" d="M0 0h48v48H0z"></path>
                                    </svg>
                                </div>
                                <span class="gsi-material-button-contents"><?= t('Continue com Google') ?></span>
                            </div>
                        </button>
                    </a>
                </div>

                <!-- Cadastro -->
                <div class="text-center mt-4">
                    <p class="mb-0"><?= t('Não tem uma conta?') ?> <a href="register.php" class="login-link"><?= t('Cadastre-se') ?></a></p>
                </div>
            </div>

            <!-- Header Azul Mobile -->
            <div class="d-block d-md-none w-100" style="background: url('assets/img/fundo-login-vermelho.png') center/cover no-repeat; padding: 2rem 0;">
                <div class="text-center">
                    <img src="assets/img/logo/BXpert-Branca.png" alt="BXpert Logo" style="max-height: 8rem; ">
                </div>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 d-none d-md-flex justify-content-center align-items-center vh-100" id="bgLogin">
        <div style="position:absolute;z-index:100;right:2rem;top:3rem">
            <?= gerarDropdownPaises($paises, $paisSelecionado); ?>
        </div>
        <div class="text-center">
            <img src="assets/img/logo/BXpert-Branca.png" alt="BXpert Logo" style="max-height: 20rem;">
        </div>
    </div>
</div>

<script>
    $(".country-option").on("click", function(e) {
        e.preventDefault();
        country = $(this).data("country");
        let flagUrl = $(this).data("flag");
        let countryName = $(this).text().trim();

        $("#selectedFlag").attr("src", flagUrl);
        $("#selectedCountry").text(countryName);
        $("#country").val(country);

        $.post("../app/helpers/translation.php", {
            lang: country
        }, function(response) {
            location.reload();
        });
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            icon.textContent = 'visibility';
        }
    });
</script>

<script src="./assets/js/jsencrypt.min.js"></script>
<script src="login/login.js?v=0.2"></script>
<?php require_once '../app/views/footer.php'; ?>