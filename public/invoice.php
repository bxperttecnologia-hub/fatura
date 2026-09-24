<?php
require_once '../app/views/layout_creation.php';
?>
<style>
    .invoice-header {
        background: #f6f6f6;
        border: 1px solid #e1e1e1;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        padding: 18px 20px 10px 20px;
    }

    .pagea4 {
        width: 190mm;
        max-width: 100%;
        margin: 0 auto;
    }

    .invoice-header .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .invoice-header span {
        font-size: 1.25rem;
        font-weight: 500;
        color: #232323;
    }

    .invoice-header #fatura-id {
        font-weight: 600;
    }

    .invoice-header .subtitle {
        display: block;
        font-size: .75rem;
        color: #666;
        margin-top: -3px;
        letter-spacing: .5px;
    }

    #status-invoice {
        border: 1px solid #267fa8;
        color: #267fa8;
        border-radius: 4px;
        padding: 2px 16px;
        font-size: 1em;
        font-weight: 500;
        background: #fff;
        min-width: 64px;
        text-align: center;
    }

    /* ② –– painel lateral */
    .action-panel {
        position: sticky;
        top: 60px;
        /* ou 16px, ajusta pra não grudar total no topo */
        align-self: flex-start;
        /* mantém os outros estilos */
        width: 240px;
        background: #fff;
        /* border: 1px solid #dee2e6; */
        border-radius: .5rem;
        /* box-shadow: 0 0 .75rem rgba(0, 0, 0, .08); */
        padding: 1rem;
        font-size: .925rem;
        z-index: 10;
        /* pra ficar acima de conteúdo se preciso */
    }

    .action-panel .btn {
        display: flex;
        align-items: center;
        /* ícone + texto centralizados */
        gap: .35rem;
    }

    .action-panel .section-title {
        font-weight: 600;
        font-size: .75rem;
        letter-spacing: .02em;
        text-transform: uppercase;
        margin: .75rem 0 .25rem;
        border-bottom: 1px solid #ced4da;
        padding-bottom: 2px;
        color: #6c757d;
    }

    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: #fff;
    }

    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
        color: #fff;
    }

    .btn-purple:focus,
    .btn-purple:active {
        background-color: #512d91;
        border-color: #512d91;
        color: #fff;
    }

    /* ===== Pré-visualizar e imprimir ===== */
    .pp-label {
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: .5rem;
        display: block;
    }

    .pp-format {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem .9rem;
        margin-bottom: .6rem;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }

    .pp-format input {
        display: none;
    }

    .pp-format small {
        display: block;
        color: #6b7280;
        font-size: .75rem;
    }

    .pp-format:has(input:checked) {
        border-color: #007abd;
        background: #eef7fc;
    }

    .pp-format:has(input:checked) .material-icons-outlined {
        color: #007abd;
    }

    .pp-stage {
        position: relative;
        height: 70vh;
        background: #525659;
    }

    .pp-stage iframe {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    .pp-overlay {
        position: absolute;
        inset: 0;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .75rem;
        padding: 1.5rem;
        text-align: center;
        color: #fff;
        background: rgba(60, 63, 65, .92);
    }

    @media (max-width: 991.98px) {
        .pp-stage {
            height: 60vh;
        }
    }
</style>

<main>
    <!-- ===== CONTAINER LADO‑A‑LADO ===== -->
    <div class="d-flex gap-4 mt-5 no-print justify-content-center align-items-center">
        <!-- ==== FATURA (cresce até encher) ==== -->
        <div class=" flex-column d-flex justify-content-center">

            <div class="invoice-header pagea4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="mb-0">Fatura nº <span id="fatura-id"></span></span>
                        <span class="subtitle" id="subtitle-client"></span>
                    </div>
                    <div>
                        <span id="status-invoice" class="d-none"></span>
                    </div>
                </div>
            </div>
            <div id="preloader" style="display:none;">Carregando...</div>
            <div id="fatura-container"
                class="invoiceContainer shadow-sm bg-white ">
                <!-- aqui dentro já está todo o HTML da fatura -->
            </div>
        </div>

        <!-- ③ –– Painel -->
        <aside class="action-panel shadow-sm">

            <!-- topo verde -->
            <button class="btn text-center align-items-center align-content-center btn-success w-100 mb-2 fw-semibold" id="btnRecibo">
                <span class="material-icons-outlined">paid</span>
                Pagamento / Recibo
            </button>

            <button class="d-none btn btn-warning w-100 mb-2" id="btnFinalizar">
                <span class="material-icons-outlined">check_circle</span>
                Finalizar 
            </button>

            <button class="d-none btn text-center align-items-center align-content-center btn-secondary w-100 mb-2" id="btnEditar">
                <span class="material-icons-outlined">edit</span>
                Editar 
            </button>

            <!-- grupo Documento -->

            <div class="d-none w-100 mb-2" id="generatePdf">
                <button class="btn btn-primary w-100 text-center" type="button" id="btnFormatoImpressao"
                    data-bs-toggle="modal" data-bs-target="#modalPrintPreview">
                    <span class="material-icons-outlined align-middle">print</span>
                    Imprimir / Baixar
                </button>
            </div>

            <button class="d-none btn text-center d-none align-items-center align-content-center btn-info text-white w-100 mb-2" id="btnEnviar"
                data-bs-toggle="modal" data-bs-target="#modalEnviarEmail">
                <span class="material-icons-outlined">send</span>
                Enviar fatura
            </button>

            <button class="d-none btn btn-purple w-100 mb-2" id="btnCloneToInvoice">
                <span class="material-icons-outlined">copy</span>
                Clonar Fatura
            </button>

            <!-- <h6 class="section-title">Documento</h6> -->

            <button class="d-none btn text-center align-items-center align-content-center btn-danger w-100 mb-2" id="btnDeleteInvoice">
                <span class="material-icons-outlined">close</span>
                Apagar
            </button>
            <!-- <h6 class="section-title">Documento</h6> -->

            <button class="d-none btn text-center align-items-center align-content-center btn-dark w-100 mb-2" id="btnNotaCredito">
                <span class="material-icons-outlined">assignment_return</span>
                Nota de Crédito
            </button>


        </aside>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalPagamento" tabindex="-1">
        <div class="modal-dialog">
            <form id="formPagamento" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pagamento / Recibo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Valor -->
                    <div class="p-3 bg-white rounded shadow-sm mb-2">
                        <div class="mb-3">
                            <label class="form-label">Valor</label>
                            <div class="input-group d-flex gap-0">
                                <input type="number" step="0.01" min="0" id="pg_valor"
                                    name="amount" class="form-control" required>
                                <span class="input-group-text" id="pg_saldo"></span>
                            </div>
                            <!-- o .invalid-feedback será inserido aqui quando necessário -->
                        </div>


                    </div>

                    <!-- Série -->
                    <div class="p-3 bg-white rounded shadow-sm mb-2 d-flex gap-2">
                        <!-- Data -->
                        <div class="mb-3 col-6">
                            <label class="form-label">Data</label>
                            <input type="date" id="pg_data" name="pay_date"
                                class="form-control" required>
                        </div>

                        <div class="mb-3 col-6">
                            <label class="form-label">Série</label>
                            <input id="pg_serie" name="serie" class="form-control" required placeholder="EX: 12/2026">
                        </div>

                        <!-- Meio de pagamento -->
                    </div>

                    <div class="mb-3 p-3 bg-white rounded shadow-sm mb-2">
                        <label class="form-label">Meio de pagamento</label>
                        <select id="pg_meio" name="payment_method" class="form-select" required>
                            <option>Transferência bancária</option>
                            <option>Dinheiro</option>
                            <option>Cheque</option>
                            <option>TPA / Cartão</option>
                        </select>
                    </div>

                    <!-- Observações -->
                    <div class="p-3 bg-white rounded shadow-sm mb-2">

                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea id="pg_obs" name="notes" rows="2"
                                class="form-control"></textarea>
                        </div>

                        <!-- campo oculto com ID da fatura -->
                        <input type="hidden" name="invoice_id" value="">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">
                        Registar pagamento e criar recibo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalReceipts" tabindex="-1" aria-labelledby="modalReceiptsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow border-0">

                <!-- Header -->
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="modalReceiptsLabel">
                        <i class="bi bi-receipt me-2"></i>Recibos
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar">
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- Actions -->
                    <div class="d-flex justify-content-end mb-3">
                        <button
                            type="button"
                            class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#modalPagamento">

                            <i class="bi bi-plus-circle me-1"></i>
                            Novo Recibo
                        </button>
                    </div>

                    <!-- Receipts List -->
                    <div id="receiptsList" class="receipts-list">
                        <div class="text-center text-muted py-4">
                            Nenhum recibo encontrado.
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Fechar
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNotes" tabindex="-1" aria-labelledby="modalReceiptsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow border-0">

                <!-- Header -->
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="modalReceiptsLabel">
                        <i class="bi bi-receipt me-2"></i>Recibos
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar">
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- Actions -->
                    <div class="d-flex justify-content-end mb-3">
                        <button
                            type="button"
                            class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#modalNotes">

                            <i class="bi bi-plus-circle me-1"></i>
                            Nova nota credito
                        </button>
                    </div>

                    <!-- Receipts List -->
                    <div id="receiptsList" class="receipts-list">
                        <div class="text-center text-muted py-4">
                            Nenhuma nota de credito encontrado.
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Fechar
                    </button>
                </div>

            </div>
        </div>
    </div>


    <!-- Modal :: Enviar fatura por e‑mail -->
    <div class="modal fade" id="modalEnviarEmail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formEnviarEmail" class="modal-content needs-validation" novalidate>

                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="material-icons-outlined me-1">mail</span>
                        Enviar fatura por e‑mail
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- --- Destinatários --- -->
                    <div class="row g-2">
                        <div class="col-md-8">
                            <label class="form-label">Para</label>
                            <input type="email" class="form-control" name="to" required>
                            <div class="invalid-feedback">E‑mail inválido.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cc (opcional)</label>
                            <input type="email" class="form-control" name="cc">
                        </div>
                    </div>

                    <!-- --- Assunto --- -->
                    <div class="mt-3">
                        <label class="form-label">Assunto</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>

                    <!-- --- Editor Quill --- -->
                    <div class="mt-3">
                        <label class="form-label">Mensagem</label>

                        <!-- toolbar -->
                        <div id="editor-toolbar">
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                            </span>
                        </div>

                        <!-- área de edição -->
                        <div id="editor-container" style="height:280px"></div>

                        <!-- texto plano/HTML que realmente será enviado -->
                        <textarea name="body" id="body-hidden" class="d-none"></textarea>
                    </div>

                    <!-- Anexar PDF -->
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="chkAnexar" name="attach" checked>
                        <label class="form-check-label" for="chkAnexar">
                            <span class="material-icons-outlined align-middle">picture_as_pdf</span>
                            Anexar PDF da fatura
                        </label>
                    </div>

                    <input type="hidden" name="invoice_id" id="email_invoice_id">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="material-icons-outlined align-middle me-1">send</span>
                        Enviar e-mail
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- ===== PRÉ-VISUALIZAR E IMPRIMIR ===== -->
    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="printPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printPreviewTitle">
                        <span class="material-icons-outlined align-middle me-1">print</span>
                        Pré-visualizar e imprimir
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Escolha do formato -->
                        <div class="col-lg-4 p-3 border-end">
                            <p class="pp-label">Formato</p>

                            <label class="pp-format">
                                <input type="radio" name="pp_format" value="a4" checked>
                                <span class="material-icons-outlined">description</span>
                                <span>
                                    <strong>Fatura A4</strong>
                                    <small>PDF em folha A4, com Original e Duplicado</small>
                                </span>
                            </label>

                            <label class="pp-format">
                                <input type="radio" name="pp_format" value="thermal">
                                <span class="material-icons-outlined">receipt_long</span>
                                <span>
                                    <strong>Talão térmico</strong>
                                    <small>Rolo de 80mm (impressora POS)</small>
                                </span>
                            </label>

                            <div id="ppCopiesBox" class="mt-3">
                                <label class="pp-label" for="ppCopies">Nº de vias</label>
                                <select id="ppCopies" class="form-select">
                                    <option value="1">1 via</option>
                                    <option value="2" selected>2 vias (Original + Duplicado)</option>
                                    <option value="3">3 vias</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pré-visualização -->
                        <div class="col-lg-8 pp-stage">
                            <div id="ppLoading" class="pp-overlay">
                                <div class="spinner-border text-light" role="status" aria-hidden="true"></div>
                                <span>A preparar a pré-visualização...</span>
                            </div>
                            <div id="ppError" class="pp-overlay d-none">
                                <span class="material-icons-outlined">error_outline</span>
                                <span id="ppErrorText"></span>
                                <button type="button" class="btn btn-light btn-sm" id="ppRetry">Tentar novamente</button>
                            </div>
                            <iframe id="ppFrame" title="Pré-visualização do documento"></iframe>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-outline-primary" id="ppDownload" disabled>
                        <span class="material-icons-outlined align-middle" style="font-size:18px;">download</span>
                        Baixar PDF
                    </button>
                    <button type="button" class="btn btn-primary" id="ppPrint" disabled>
                        <span class="material-icons-outlined align-middle" style="font-size:18px;">print</span>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="invoices/invoice.js?v=4.8"></script>

<?php require_once '../app/views/footer.php'; ?>