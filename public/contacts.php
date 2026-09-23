<?php
require_once '../app/views/layout_creation.php';

?>

<style>
    #contactModal .modal-dialog {
        max-width: 1080px;
    }

    #contactModal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 22px;
        background: #f8fafc !important;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
    }

    #contactModal .modal-header {
        padding: 22px 28px;
        background: linear-gradient(135deg, #007abd 0%, #2563eb 100%);
    }

    #contactModal .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    #contactModal .modal-body {
        padding: 28px;
    }

    #contactModal .card-clean {
        height: 100%;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #007abd !important;
        border-radius: 16px;
        background: #fff !important;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
    }

    #contactModal .section-title-modal {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eef2f7;
        color: #172033;
        font-size: 1rem;
        font-weight: 700;
    }

    #contactModal .section-title-modal i {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 10px;
        background: #eaf3ff;
        color: #1671c9;
    }

    #contactModal .label {
        margin-bottom: 3px;
        color: #64748b;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    #contactModal .value {
        min-height: 20px;
        margin-bottom: 12px;
        color: #172033;
        font-size: .94rem;
        font-weight: 500;
        overflow-wrap: anywhere;
    }

    #contactModal .phone-badge {
        border: 1px solid #dbeafe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    #contactModal .btn-close {
        filter: brightness(0) invert(1);
        opacity: .9;
    }

    .contact-status-filters {
        display: inline-flex;
        gap: 6px;
        padding: 5px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
    }

    .contact-status-filter {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 0 !important;
        border-radius: 10px !important;
        padding: 8px 14px !important;
        color: #64748b !important;
        background: transparent !important;
        font-size: .86rem;
        font-weight: 600;
        transition: .2s ease;
    }

    .contact-status-filter:hover {
        color: #1d4ed8 !important;
        background: #eaf3ff !important;
    }

    .contact-status-filter.is-selected {
        color: #fff !important;
        background: #2563eb !important;
        box-shadow: 0 5px 12px rgba(37, 99, 235, .22);
    }

    .contact-status-filter.is-selected[data-filter="archived"] {
        background: #64748b !important;
        box-shadow: 0 5px 12px rgba(100, 116, 139, .22);
    }

    /* ===== HEADER ===== */

    .container {
        margin-top: 80px !important;
    }

    .container h2 {
        font-weight: 600;
    }

    .container .btn-primary {
        background: #007abd;
        border: none;
        border-radius: 999px;
        padding: 8px 18px;
        transition: all 0.2s ease;
    }

    .container .btn-primary:hover {
        background: #025d8e;
        transform: translateY(-1px);
    }

    /* ===== CARD ===== */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    /* HEADER DO CARD */
    .card-header {
        background: transparent !important;
        border-bottom: none;
        padding: 20px;
    }

    /* BOTÕES EXPORT */
    .card-header .btn {
        border-radius: 999px;
        font-weight: 500;
    }

    /* ===== TABELA ESTILO ===== */
    #contactTable {
        border-collapse: separate;
        border-spacing: 0 12px;
        width: 100%;
    }

    /* HEADER */
    #contactTable thead th {
        border: none;
        font-size: 12px;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 16px;
    }

    /* ROW */
    #contactTable tbody tr {
        background: #fff !important;
        border-radius: 14px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    /* HOVER PRO */
    #contactTable tbody tr:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    /* CELLS */
    #contactTable tbody td {
        border: none;
        padding: 18px 16px;
        vertical-align: middle;
        font-size: 0.95rem;
        background: #fff !important;
    }

    /* BORDAS ARREDONDADAS */
    #contactTable tbody td:first-child {
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        background: #fff !important;
    }

    #contactTable tbody th {
        text-align: right !important;
    }

    #contactTable tbody td:last-child {
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
        text-align: right;
        padding-right: 24px;
    }

    /* ===== NOME (PRINCIPAL) ===== */
    #contactTable tbody td:first-child {
        font-weight: 600;
        color: #111;
    }

    /* SUBINFO */
    #contactTable tbody td small {
        display: block;
        color: #6b7280;
    }

    /* ===== ÍCONES ===== */
    .table-icon {
        font-size: 1.2rem;
        color: #9ca3af;
        transition: all 0.2s;
    }

    .table-icon:hover {
        color: #111;
        transform: scale(1.1);
    }

    /* ===== AÇÕES ===== */
    .edit-contact .material-icons-round,
    .delete-contact .material-icons-round {
        transition: all 0.2s ease;
    }

    .edit-contact:hover .material-icons-round {
        color: #2563eb;
        transform: scale(1.2);
    }

    .delete-contact:hover .material-icons-round {
        color: #dc2626;
        transform: scale(1.2);
    }

    /* ===== MODAL MAIS PREMIUM ===== */
    .contacts-page .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    /* ===== CARDS DO MODAL ===== */
    .card-header.bg-primary,
    .card-header.bg-info,
    .card-header.bg-secondary,
    .card-header.bg-success,
    .card-header.bg-warning {
        border-radius: 12px 12px 0 0;
        font-size: 0.95rem;
    }

    /* ===== PHONE CARDS ===== */
    .phone-card {
        display: inline-flex;
        align-items: center;
        background: #f3f4f6;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .phone-card:hover {
        background: #e5e7eb;
    }

    /* ===== MOBILE ===== */
    @media (max-width: 768px) {

        #contactTable thead {
            display: none;
        }

        #contactTable,
        #contactTable tbody,
        #contactTable tr,
        #contactTable td {
            display: block;
            width: 100%;
        }

        #contactTable tbody tr {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 14px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        #contactTable tbody td {
            padding: 6px 0;
            text-align: left;
        }

        #contactTable tbody td:first-child {
            font-size: 1.1rem;
            font-weight: 600;
        }

        #contactTable tbody td[data-label="Ações"] {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 10px;
        }
    }

    #dt-length-0 {
        background: #fff !important;
        border-radius: 8px;
        padding: 5px;
        border: 0.5px solid #e5e7eb;
    }

    #dt-search {
        position: relative;
        margin-bottom: 15px;
    }

    /* ÍCONE */
    #dt-search-0 .search-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    /* INPUT */
    #dt-search-0 {
        padding-left: 35px !important;
        border-radius: 12px !important;
        border: 1px solid #e5e7eb !important;
    }

    /* FOCUS */
    #dt-search-0:focus {
        border-color: #16a34a !important;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15) !important;
    }

    /* ===== MODAL HEADER ===== */
    .modal-header {
        background: linear-gradient(135deg, #007abd, #00c6ff);
        color: #fff;
        border: none;
    }

    .modal-title {
        font-weight: 600;
    }

    .modal-content {
        background: #ffff !important;
    }

    /* ===== CARD CLEAN ===== */
    .card-clean {
        background: none !important;
        border-radius: 14px;
        padding: 18px;
        /* box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05); */
        transition: 0.2s;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #007abd !important;
    }

    .card-clean:hover {
        transform: translateY(-2px);
    }

    /* ===== CORES POR CARD ===== */
    .card-clean.primary {
        border-left-color: #6a5cff;
    }

    .card-clean.info {
        border-left-color: #00c6ff;
    }

    .card-clean.success {
        border-left-color: #10b981;
    }

    .card-clean.danger {
        border-left-color: #f43f5e;
    }

    /* ===== TITULO ===== */
    .section-title-modal {
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #111827;
        margin-left: -10px;
    }

    .section-title-modal i {
        background: #eef2ff;
        color: #6a5cff;
        padding: 8px;
        border-radius: 10px;
        font-size: 18px;
    }

    /* ===== LABEL / VALUE ===== */
    .label {
        font-size: 12px;
        color: #6b7280;
    }

    .value {
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        margin-bottom: 8px;
    }

    /* ===== PHONE BADGE ===== */
    .phone-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #eef6ff;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        color: #1d4ed8;
        font-weight: 500;
    }

    .phone-badge i {
        font-size: 16px;
    }

    /* ===== RESPONSIVO ===== */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 10px;
        }
    }

    #downloadCSV {
        border-radius: 999px;
        font-weight: 500;
    }

    #downloadCSV:hover,
    #downloadExcel:hover,
    #downloadPDF:hover {
        transform: translateY(-1px);
    }

    #downloadCSV:hover {
        background: #16a34a;
        color: #fff;
    }

    #downloadExcel:hover {
        background: #2563eb;
        color: #fff;
    }

    #downloadPDF:hover {
        background: #dc2626;
        color: #fff;
    }

    .edit-contact:hover i {
        color: #2563eb;
        transform: scale(1.2);
    }

    #downloadExcel {
        border-radius: 999px;
        font-weight: 500;
    }

    #downloadCSV:hover,
    #downloadExcel:hover,
    #downloadPDF:hover {
        transform: translateY(-1px);
    }

    #downloadCSV:hover {
        background: #16a34a;
        color: #fff;
    }

    #downloadExcel:hover {
        background: #2563eb;
        color: #fff;
    }

    #downloadPDF:hover {
        background: #dc2626;
        color: #fff;
    }

    .edit-contact:hover i {
        color: #2563eb;
        transform: scale(1.2);
    }

    #downloadPDF {
        border-radius: 999px;
        font-weight: 500;
    }

    #downloadCSV,
    #downloadExcel,
    #downloadPDF,
    #newContact {
        transform: translateY(-1px);
        font-size: 0.9rem;
        padding: 5px 18px !important;
        height: 35px !important;
    }

    #downloadCSV:hover {
        background: #16a34a;
        color: #fff;
    }

    #downloadExcel:hover {
        background: #2563eb;
        color: #fff;
    }

    #downloadPDF:hover {
        background: #dc2626;
        color: #fff;
    }

    .edit-contact:hover i {
        color: #2563eb;
        transform: scale(1.2);
    }

    .dt-paging-button .page-link {
        border-radius: 10px !important;
        margin: 0 2px;
        border: none;
        background: #f3f6fb;
        color: #333;
        font-size: 13px;
    }

    .dt-paging-button .page-item.active .page-link {
        background: #2f6bff;
        color: #fff;
    }

    .dt-paging-button .page-item.disabled .page-link {
        opacity: 0.5;
    }

    /* Modal de contacto: cartão compacto e minimalista */
    #contactModal .modal-dialog {
        max-width: 760px;
    }

    #contactModal .modal-content {
        border-radius: 18px;
        background: #fff !important;
        box-shadow: 0 20px 55px rgba(15, 23, 42, .2);
    }

    #contactModal .modal-header {
        min-height: 64px;
        padding: 16px 22px;
        background: linear-gradient(135deg, #007abd, #2563eb);
    }

    #contactModal .modal-title {
        font-size: 1.05rem;
        letter-spacing: -.01em;
    }

    #contactModal .modal-body {
        max-height: min(620px, calc(100vh - 150px));
        padding: 18px;
        background: #f8fafc;
    }

    #contactModal .modal-body > .container-fluid {
        padding: 0;
    }

    #contactModal .modal-body .row {
        --bs-gutter-x: 10px;
        --bs-gutter-y: 10px;
    }

    #contactModal .card-clean {
        height: auto;
        margin-bottom: 0 !important;
        padding: 14px 16px;
        border: 1px solid #e8edf4;
        border-left: 3px solid #60a5fa !important;
        border-radius: 13px;
        box-shadow: none;
    }

    #contactModal .section-title-modal {
        gap: 8px;
        margin: 0 0 12px;
        padding: 0 0 9px;
        border-bottom: 1px solid #eef2f7;
        font-size: .86rem;
    }

    #contactModal .section-title-modal i {
        width: 26px;
        height: 26px;
        font-size: .82rem;
        border-radius: 8px;
    }

    #contactModal .card-clean .mb-2 {
        margin-bottom: 8px !important;
    }

    #contactModal .label {
        margin-bottom: 1px;
        font-size: .64rem;
        letter-spacing: .06em;
    }

    #contactModal .value {
        min-height: 17px;
        margin-bottom: 0;
        font-size: .84rem;
        line-height: 1.35;
    }

    #contactModal .phone-badge {
        padding: 6px 10px;
        font-size: .78rem;
    }

    #contactModal .phone-badge i {
        font-size: .82rem;
    }

    @media (max-width: 768px) {
        #contactModal .modal-dialog {
            margin: .5rem;
        }

        #contactModal .modal-body {
            padding: 12px;
        }
    }
</style>

<body>

    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard"></i>
                        Detalhes do Contato
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row g-4">

                            <!-- ESQUERDA -->
                            <div class="col-lg-6">

                                <!-- Empresa -->
                                <div class="card-clean mb-3">
                                    <div class="section-title-modal">
                                        <i class="bi bi-building"></i>
                                        Empresa
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Nome</div>
                                        <div class="value" id="contactName"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Tipo</div>
                                        <div class="value" id="contactType"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">NIF</div>
                                        <div class="value" id="contactContributor"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Email</div>
                                        <div class="value" id="contactEmail"></div>
                                    </div>
                                </div>

                                <!-- Contatos -->
                                <div class="card-clean mb-3">
                                    <div class="section-title-modal">
                                        <i class="bi bi-telephone"></i>
                                        Contatos
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="phone-badge">
                                            <i class="bi bi-telephone"></i>
                                            <span id="contactTelephone"></span>
                                        </div>

                                        <div class="phone-badge">
                                            <i class="bi bi-phone"></i>
                                            <span id="contactCellphone"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Localização -->
                                <div class="card-clean">
                                    <div class="section-title-modal">
                                        <i class="bi bi-geo-alt"></i>
                                        Localização
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Endereço</div>
                                        <div class="value" id="contactAddress"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Cidade</div>
                                        <div class="value" id="contactLocation"></div>
                                    </div>
                                </div>

                            </div>

                            <!-- DIREITA -->
                            <div class="col-lg-6">

                                <!-- Contato principal -->
                                <div class="card-clean mb-3">
                                    <div class="section-title-modal">
                                        <i class="bi bi-person"></i>
                                        Contato Principal
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Nome</div>
                                        <div class="value" id="contactPrefName"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Email</div>
                                        <div class="value" id="contactPrefEmail"></div>
                                    </div>
                                </div>

                                <!-- Configurações -->
                                <div class="card-clean">
                                    <div class="section-title-modal">
                                        <i class="bi bi-gear"></i>
                                        Configurações
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Pagamento</div>
                                        <div class="value" id="contactPaymentMethod"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Moeda</div>
                                        <div class="value" id="contactCurrency"></div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="label">Atualizado</div>
                                        <div class="value" id="contactUpdatedAt"></div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <main class="contacts-page">
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><?= t('Meus Clientes') ?></h2>
                <div class="d-flex flex-wrap gap-2">
                    <button id="downloadCSV" class="btn btn-outline-success rounded-pill d-flex align-items-center gap-2"><i class="bi bi-download align-middle fs-6"></i> <?= t('Baixar em CSV') ?></button>
                    <button id="downloadExcel" class="btn btn-outline-primary rounded-pill d-flex align-items-center gap-2"><i class="bi bi-filetype-xls align-middle fs-6"></i> <?= t('Baixar em Excel') ?></button>
                    <button id="downloadPDF" class="btn btn-outline-danger rounded-pill d-flex align-items-center gap-2"><i class="bi bi-filetype-pdf align-middle fs-6"></i> <?= t('Baixar em PDF') ?></button>
                    <a href="register_contact.php" id="newContact" class="btn btn-primary d-flex align-items-center gap-2"><i class="bi bi-plus-circle align-middle fs-6"></i> <?= t('Novo Cliente') ?></a>
                </div>
            </div>

            <div class="col-12">
                <div class="card-body">
                    <div class="contact-status-filters mb-3" role="group" aria-label="Estado dos contatos">

                        <button
                            id="filterActive"
                            class="btn contact-status-filter is-selected"
                            data-filter="active">
                            <i class="bi bi-people"></i>
                            Ativos
                        </button>

                        <button
                            id="filterArchived"
                            class="btn contact-status-filter"
                            data-filter="archived">
                            <i class="bi bi-archive"></i>
                            Arquivados
                        </button>

                    </div>
                    <table id="contactTable" class="table nowrap w-100">
                        <thead>
                            <tr>
                                <th><?= t('Nome') ?></th>
                                <th><?= t('Telefone') ?></th>
                                <th><?= t('País') ?>/<?= t('Cidade') ?></th>
                                <th class="text-align-right"><?= t('Ações') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="contacts/contacts.js?v=0.5"></script>
    <script>
        lucide.createIcons();
    </script>
    <?php require_once '../app/views/footer.php'; ?>
</body>

</html>