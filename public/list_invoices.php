<?php
require_once '../app/views/layout_creation.php';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>

<style>
    #invoicesTable {
        border-collapse: separate;
        border-spacing: 0 12px;
        width: 100%;
    }

    #invoicesTable thead th {
        border: none;
        font-size: 12px;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 16px;
    }

    #invoicesTable thead th[data-key] {
        cursor: pointer;
        user-select: none;
    }

    #invoicesTable tbody tr {
        background: #fff !important;
        border-radius: 14px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        text-align: left !important;
        cursor: pointer;
    }

    #invoicesTable tbody tr:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    #invoicesTable tbody td {
        border: none;
        padding: 18px 16px;
        vertical-align: middle;
        font-size: 0.95rem;
        background: #fff !important;
        text-align: left;
    }

    #invoicesTable tbody td:first-child {
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        background: #fff !important;
        font-weight: 600;
        color: #111;
    }

    #invoicesTable tbody td:last-child {
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
        text-align: right;
        padding-right: 24px;
    }

    .table-icon {
        font-size: 1.2rem;
        color: #9ca3af;
        transition: all 0.2s;
    }

    .table-icon:hover {
        color: #111;
        transform: scale(1.1);
    }

    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .modal-dialog {
        display: flex;
        align-items: center;
        min-height: 100vh;
    }

    #filterClient,
    #filterStatus,
    #filterStartDate,
    #filterEndDate {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        min-height: 44px;
    }

    #filterClient:focus,
    #filterStatus:focus,
    #filterStartDate:focus,
    #filterEndDate:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
    }

    #invoiceInsights {
        --insight-ink: #172033;
        --insight-muted: #6b7280;
        --insight-line: #e8edf3;
        margin-bottom: 28px;
    }

    #invoiceInsights .insight-section-title {
        color: var(--insight-ink);
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .08em;
        margin: 0 0 12px;
        text-transform: uppercase;
    }

    #invoiceInsights .insight-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    #invoiceInsights .insight-card {
        background: #fff;
        border: 1px solid var(--insight-line);
        border-radius: 14px;
        min-height: 112px;
        padding: 16px;
        transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    #invoiceInsights .insight-card:hover {
        border-color: #d4dce7;
        box-shadow: 0 8px 22px rgba(23, 32, 51, .06);
        transform: translateY(-2px);
    }

    #invoiceInsights .insight-card__top {
        align-items: center;
        color: var(--insight-muted);
        display: flex;
        font-size: .8rem;
        font-weight: 600;
        gap: 8px;
        justify-content: space-between;
    }

    #invoiceInsights .insight-card__icon {
        align-items: center;
        background: #f1f5f9;
        border-radius: 9px;
        color: #475569;
        display: inline-flex;
        height: 30px;
        justify-content: center;
        width: 30px;
    }

    #invoiceInsights .insight-card__value {
        color: var(--insight-ink);
        font-size: 1.65rem;
        font-weight: 750;
        line-height: 1.1;
        margin-top: 16px;
    }

    #invoiceInsights .insight-card__hint {
        color: var(--insight-muted);
        font-size: .74rem;
        margin-top: 5px;
    }

    #invoiceInsights .status-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    #invoiceInsights .status-card {
        border-left: 3px solid #94a3b8;
        min-height: 92px;
        padding: 13px 14px;
    }

    #invoiceInsights .status-card .insight-card__value {
        font-size: 1.35rem;
        margin-top: 12px;
    }

    #invoiceInsights .status-card--pending { border-left-color: #f59e0b; }
    #invoiceInsights .status-card--cancelled { border-left-color: #ef4444; }
    #invoiceInsights .status-card--paid { border-left-color: #16a34a; }
    #invoiceInsights .status-card--partial { border-left-color: #3b82f6; }
    #invoiceInsights .status-card--draft { border-left-color: #94a3b8; }
    #invoiceInsights .status-card--overdue { border-left-color: #dc2626; }
    #invoiceInsights .status-card--expected {
        background: #172033;
        border-color: #172033;
        color: #fff;
    }

    #invoiceInsights .status-card--expected .insight-card__top,
    #invoiceInsights .status-card--expected .insight-card__value,
    #invoiceInsights .status-card--expected .insight-card__hint {
        color: #fff;
    }

    #invoiceInsights .status-card--expected .insight-card__icon {
        background: rgba(255, 255, 255, .12);
        color: #fff;
    }

    #invoiceCollectionFeature {
        align-items: center;
        background: linear-gradient(135deg, #172033, #24456f);
        border-radius: 16px;
        color: #fff;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        margin: 0 0 18px;
        padding: 18px 20px;
    }

    #invoiceCollectionFeature h3 { font-size: 1rem; margin: 0 0 5px; }
    #invoiceCollectionFeature p { color: rgba(255,255,255,.75); font-size: .82rem; margin: 0; }
    #invoiceCollectionFeature .collection-summary { color: rgba(255,255,255,.75); font-size: .76rem; margin-top: 8px; }
    #invoiceCollectionFeature .btn { white-space: nowrap; }

    #collectionModal .modal-content { border: 0; border-radius: 18px; }
    #collectionModal .modal-header { background: #172033; color: #fff; }
    #collectionModal .modal-header .btn-close { filter: invert(1); }
    #collectionModal .collection-selected {
        background: #f6f8fb;
        border: 1px solid #e7ebf1;
        border-radius: 10px;
        font-size: .82rem;
        max-height: 120px;
        overflow-y: auto;
        padding: 9px 12px;
    }
    #collectionModal .collection-tip {
        background: #eef5ff;
        border-radius: 10px;
        color: #315985;
        font-size: .78rem;
        padding: 10px 12px;
    }

    @media (max-width: 1100px) {
        #invoiceInsights .status-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    @media (max-width: 767px) {
        #invoiceInsights .insight-grid,
        #invoiceInsights .status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    /* ===================================================
       MENU "EXPORTAR" — 100% CSS/JS nativo (sem Bootstrap)
    =================================================== */
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }

    .custom-dropdown-btn {
        background: #16a34a;
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
    }

    .custom-dropdown-btn:hover {
        background: #15803d;
    }

    .custom-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 8px;
        min-width: 230px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .14);
        padding: 6px;
        z-index: 1000;
    }

    .custom-dropdown-menu.is-open {
        display: block;
    }

    .custom-dropdown-item {
        padding: 9px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-size: .95rem;
        color: #111;
        display: flex;
        align-items: center;
        justify-content: space-between;
        white-space: nowrap;
    }

    .custom-dropdown-item:hover {
        background: #f3f4f6;
    }

    .custom-dropdown-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 6px 4px;
    }

    .has-submenu {
        position: relative;
    }

    .has-submenu::after {
        content: "\25B6";
        /* ▶ */
        font-size: 9px;
        color: #9ca3af;
        margin-left: 12px;
    }

    .custom-submenu {
        display: none;
        position: absolute;
        top: -6px;
        left: 100%;
        margin-left: 4px;
        min-width: 140px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .14);
        padding: 6px;
        z-index: 1001;
    }

    .has-submenu.is-open>.custom-submenu {
        display: block;
    }

    @media (max-width: 767px) {
        .custom-submenu {
            position: static;
            box-shadow: none;
            margin-left: 0;
            margin-top: 4px;
            padding-left: 14px;
        }
    }
</style>

<body>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirmar exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Tem certeza que deseja eliminar?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger" id="confirmDelete">Eliminar</button>
                </div>

            </div>
        </div>
    </div>

    <div id="preloader" style="display: none; position: fixed; bottom: 20px; right: 20px; width: 200px; padding: 10px; background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.2);">
        <p style="margin: 0; font-size: 14px;">Gerando arquivo...</p>
        <div style="height: 5px; width: 100%; background: #ddd; border-radius: 3px; overflow: hidden; margin-top: 5px;">
            <div id="progressBar" style="height: 100%; width: 0%; background: #007bff;"></div>
        </div>
    </div>

    <main>
        <div class="container mt-5">
            <h2 class="mb-4"><?= t('Minhas Faturas') ?></h2>

            <section id="invoiceInsights" aria-label="Insights dos documentos">
                <div class="insight-section-title">Visão geral dos documentos</div>
                <div class="insight-grid">
                    <article class="insight-card">
                        <div class="insight-card__top"><span>Faturas</span><span class="insight-card__icon"><i class="bi bi-receipt"></i></span></div>
                        <div class="insight-card__value" id="insightInvoices">0</div>
                        <div class="insight-card__hint">Documentos emitidos e rascunhos</div>
                    </article>
                    <article class="insight-card">
                        <div class="insight-card__top"><span>Notas de crédito</span><span class="insight-card__icon"><i class="bi bi-arrow-return-left"></i></span></div>
                        <div class="insight-card__value" id="insightCreditNotes">0</div>
                        <div class="insight-card__hint">Abatimentos registados</div>
                    </article>
                    <article class="insight-card">
                        <div class="insight-card__top"><span>Recibos</span><span class="insight-card__icon"><i class="bi bi-file-earmark-check"></i></span></div>
                        <div class="insight-card__value" id="insightReceipts">0</div>
                        <div class="insight-card__hint">Comprovativos de pagamento</div>
                    </article>
                    <article class="insight-card">
                        <div class="insight-card__top"><span>Notas de débito</span><span class="insight-card__icon"><i class="bi bi-file-earmark-plus"></i></span></div>
                        <div class="insight-card__value" id="insightDebitNotes">—</div>
                        <div class="insight-card__hint">Tipo ainda não disponível</div>
                    </article>
                </div>

                <div class="insight-section-title mt-4">Status das faturas</div>
                <div class="status-grid">
                    <article class="insight-card status-card status-card--pending"><div class="insight-card__top"><span>Pendentes</span><i class="bi bi-hourglass-split"></i></div><div class="insight-card__value" id="insightPending">0</div></article>
                    <article class="insight-card status-card status-card--cancelled"><div class="insight-card__top"><span>Canceladas</span><i class="bi bi-x-circle"></i></div><div class="insight-card__value" id="insightCancelled">0</div></article>
                    <article class="insight-card status-card status-card--paid"><div class="insight-card__top"><span>Pagas</span><i class="bi bi-check-circle"></i></div><div class="insight-card__value" id="insightPaid">0</div></article>
                    <article class="insight-card status-card status-card--partial"><div class="insight-card__top"><span>Parcelares</span><i class="bi bi-pie-chart"></i></div><div class="insight-card__value" id="insightPartial">0</div></article>
                    <article class="insight-card status-card status-card--draft"><div class="insight-card__top"><span>Rascunhos</span><i class="bi bi-pencil-square"></i></div><div class="insight-card__value" id="insightDraft">0</div></article>
                    <article class="insight-card status-card status-card--overdue"><div class="insight-card__top"><span>Vencidas</span><i class="bi bi-exclamation-circle"></i></div><div class="insight-card__value" id="insightOverdue">0</div></article>
                    <article class="insight-card status-card status-card--expected"><div class="insight-card__top"><span>Volume esperado</span><span class="insight-card__icon"><i class="bi bi-graph-up-arrow"></i></span></div><div class="insight-card__value" id="insightExpected">0</div><div class="insight-card__hint">Total das faturas pagas</div></article>
                </div>
            </section>

            <section id="invoiceCollectionFeature" aria-labelledby="invoiceCollectionTitle"
                data-company-id="<?= (int)($_SESSION['user']['company_id'] ?? 0) ?>">
                <div>
                    <h3 id="invoiceCollectionTitle"><i class="bi bi-stars me-2"></i>Nova cobrança inteligente BXpert</h3>
                    <p>Analise faturas pendentes e execute os alertas pelos canais definidos nas regras de cobrança.</p>
                    <div class="collection-summary" id="invoiceCollectionSummary">A carregar regras de alerta...</div>
                </div>
                <button type="button" class="btn btn-light btn-sm" id="invoiceCollectionBtn">
                    <i class="bi bi-send-check me-1"></i> Executar cobrança
                </button>
            </section>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="custom-dropdown" id="exportMenu">
                    <button type="button" class="custom-dropdown-btn" id="exportMenuBtn">
                        Exportar
                    </button>

                    <div class="custom-dropdown-menu" id="exportMenuList">

                        <div class="custom-dropdown-item has-submenu" id="exportInvoiceToggle">
                            <span>Fatura</span>
                            <div class="custom-submenu">
                                <div class="custom-dropdown-item" data-export="invoices" data-format="excel">Excel</div>
                                <div class="custom-dropdown-item" data-export="invoices" data-format="pdf">PDF</div>
                                <div class="custom-dropdown-item" data-export="invoices" data-format="csv">CSV</div>
                            </div>
                            <button type="button" class="btn btn-dark rounded-pill px-3" id="openCollectionModalBtn">
                                <i class="bi bi-send-check me-1"></i> Cobrar faturas
                            </button>
                        </div>

                        <div class="custom-dropdown-divider"></div>

                        <div class="custom-dropdown-item" data-export="credit_notes">Nota de Crédito</div>
                        <div class="custom-dropdown-item" data-export="receipts">Recibos</div>
                        <div class="custom-dropdown-item" data-export="debit_notes">Nota de Débito</div>

                        <div class="custom-dropdown-divider"></div>

                        <div class="custom-dropdown-item" data-export="sales_report">Relatório de Vendas</div>
                        <div class="custom-dropdown-item" data-export="invoices_paid">Faturas Pagas</div>
                        <div class="custom-dropdown-item" data-export="invoices_pending">Faturas Pendentes</div>

                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="card border-0 mb-4" style="background: none !important;">
                <div class="card-body">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tipo de Documento</label>
                            <select id="filterDocType" class="form-select">
                                <option value="invoices">Faturas</option>
                                <option value="receipts">Recibos</option>
                                <option value="credit_notes">Notas de Crédito</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold" id="filterClientLabel">Cliente</label>
                            <input type="text" id="filterClient" class="form-control" placeholder="Pesquisar cliente...">
                        </div>

                        <div class="col-md-2" id="filterStatusWrapper">
                            <label class="form-label fw-semibold">Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">Todos</option>
                                <option value="pendente">Pendente</option>
                                <option value="parcial">Parcial</option>
                                <option value="pago">Pago</option>
                                <option value="rascunho">Rascunho</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Data Inicial</label>
                            <input type="date" id="filterStartDate" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Data Final</label>
                            <input type="date" id="filterEndDate" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <button id="btnClearFilters" class="btn btn-secondary w-100">Limpar</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="invoicesTable" class="table-bx-standard table nowrap w-100">
                    <thead id="invoicesTableHead" style="background: none !important;">
                        <!-- Cabeçalho é gerado dinamicamente via JS conforme o Tipo de Documento selecionado -->
                    </thead>
                    <tbody>
                        <!-- Dados gerados via JS -->
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Mostrar</span>
                    <select id="pageSizeSelect" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-muted small" id="tableInfo"></span>
                </div>

                <nav>
                    <ul class="pagination pagination-sm mb-0" id="tablePagination"></ul>
                </nav>
            </div>
        </div>

        <div id="fatura-container" class="d-none"></div>
    </main>

    <div class="modal fade" id="collectionModal" tabindex="-1" aria-labelledby="collectionModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="collectionModalTitle"><i class="bi bi-stars me-2"></i>Nova cobrança</h5>
                        <small class="opacity-75">Envie agora ou agende uma cobrança personalizada.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="collectionForm">
                    <div class="modal-body">
                        <div class="collection-tip mb-3"><i class="bi bi-magic me-1"></i> O BXpert aplica as regras de alertas e usa os dados de contacto do cliente no momento do envio.</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Faturas selecionadas</label>
                                <div class="collection-selected" id="collectionSelectedList">Nenhuma fatura selecionada.</div>
                                <input type="hidden" id="collectionInvoiceIds">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Canais</label>
                                <div class="d-flex gap-3 pt-2">
                                    <label><input class="form-check-input me-1" type="checkbox" name="collectionChannels" value="email" checked> Email</label>
                                    <label><input class="form-check-input me-1" type="checkbox" name="collectionChannels" value="whatsapp"> Mensagem</label>
                                </div>
                                <small class="text-muted d-block mt-2">Canais sem contacto válido serão reportados como falha.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="collectionScheduleAt">Enviar em</label>
                                <input class="form-control" type="datetime-local" id="collectionScheduleAt" required>
                                <button type="button" class="btn btn-link btn-sm px-0" id="collectionNowBtn">Enviar imediatamente</button>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="collectionSubject">Assunto do email</label>
                                <input class="form-control" id="collectionSubject" value="Lembrete de pagamento da fatura {{fatura}}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="collectionMessage">Mensagem</label>
                                <textarea class="form-control" id="collectionMessage" rows="5" required>Olá {{cliente}}, identificámos um valor em aberto na fatura {{fatura}}, no montante de {{valor}}, com vencimento em {{vencimento}}. Agradecemos a regularização.</textarea>
                                <small class="text-muted">Variáveis disponíveis: {{cliente}}, {{fatura}}, {{valor}}, {{vencimento}}</small>
                            </div>
                        </div>
                        <div class="alert alert-danger d-none mt-3 mb-0" id="collectionError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark" id="submitCollectionBtn"><i class="bi bi-send-check me-1"></i> Confirmar cobrança</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuRoot = document.getElementById('exportMenu');
            const btn = document.getElementById('exportMenuBtn');
            const menu = document.getElementById('exportMenuList');

            // Abre/fecha o menu principal
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('is-open');

                // ao reabrir, garante que nenhum submenu fica preso aberto
                if (!menu.classList.contains('is-open')) {
                    closeAllSubmenus();
                }
            });

            // Abre/fecha o submenu "Fatura" (não fecha o menu principal)
            document.querySelectorAll('.has-submenu').forEach(function(submenuParent) {
                submenuParent.addEventListener('click', function(e) {
                    // só reage ao clique no próprio item "Fatura", não nos filhos dele
                    if (e.target.closest('.custom-submenu')) return;

                    e.stopPropagation();

                    const isOpen = submenuParent.classList.contains('is-open');
                    closeAllSubmenus();
                    if (!isOpen) submenuParent.classList.add('is-open');
                });
            });

            // Itens finais de exportação
            document.querySelectorAll('[data-export]').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const docType = this.dataset.export;
                    const format = this.dataset.format || undefined;
                    exportFile(docType, format);
                    closeMenu();
                });
            });

            // Fecha tudo ao clicar fora do menu
            document.addEventListener('click', function(e) {
                if (!menuRoot.contains(e.target)) {
                    closeMenu();
                }
            });

            // Fecha tudo com a tecla Esc
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeMenu();
            });

            function closeAllSubmenus() {
                document.querySelectorAll('.has-submenu.is-open').forEach(function(el) {
                    el.classList.remove('is-open');
                });
            }

            function closeMenu() {
                menu.classList.remove('is-open');
                closeAllSubmenus();
            }
        });
    </script>

    <script src="invoices/list_invoices.js?v=1.8"></script>

    <?php require_once '../app/views/footer.php'; ?>
</body>

</html>