# Auditoria completa do projecto e mapa de migração

## 1. Visão geral

O projecto actual é um ERP/CRM interno em PHP com layout em server-rendered pages e um conjunto significativo de endpoints AJAX (`public/**/ajax/*.php`) para operações CRUD, filtros, exportação, geração de PDFs, RH, stock, facturas/proformas e subscrição.

A arquitetura existente é composta por:

- Front-end PHP renderizado em `public/*.php`
- Layout compartilhado em `app/views/layout_creation.php`
- BLL/serviços auxiliares em `app/helpers/*.php`
- Modelos/entidades em `app/models/*.php`
- Configuração em `app/config/*.php`
- Autenticação e sessão em `app/helpers/authentication.php`
- AJAX endpoints em `public/**/ajax/*.php`
- Dados e integrações em MySQL e bibliotecas PHP (PHPMailer, Google APIs, Dompdf, PhpSpreadsheet)

## 2. Páginas/rotas existentes

### Autenticação
- `public/login.php`
- `public/register.php`
- `public/logout.php`
- `public/loginGoogle/processLoginGoogle.php`

### Dashboard / home
- `public/index.php`
- `public/dashboard.php`
- `public/insights.php`

### Clientes / contactos
- `public/contacts.php`
- `public/register_contact.php`
- `public/list_companies.php`
- `public/edit_company.php`

### Produtos / itens
- `public/items.php`
- `public/stock.php`
- `public/stock_view.php`
- `public/stock-depot.php`

### Vendas e documentos
- `public/create_invoices.php`
- `public/list_invoices.php`
- `public/invoice.php`
- `public/invoice_public.php`
- `public/create_proform.php`
- `public/list_proforms.php`
- `public/proform.php`
- `public/credit_note.php`
- `public/view_guide.php`
- `public/guides.php`
- `public/list_guides.php`

### RH / recursos humanos
- `public/employees.php`
- `public/ponto.php`
- `public/vacations.php`
- `public/positions.php`
- `public/payroll.php`

### Configuração / gestão
- `public/manage_users.php`
- `public/perfil.php`
- `public/subscription.php`
- `public/help.php`

## 3. Módulos e responsabilidades principais

### 1) Autenticação
- `app/helpers/authentication.php`: middleware de sessão, expiração e renovação de token
- `public/login/ajax/process_login.php`: autenticação via login/password + decrypt da chave privada
- `public/logout.php`: logout e destruição de sessão
- `app/config/db.php`: conexão PDO

### 2) Clientes / contactos / empresa
- `public/contacts/*`, `public/register_contact.php`, `public/edit_company.php`
- CRUD de contact/company, exportação, geonames, fetch/list, status, update

### 3) Produtos / itens / stock
- `public/items/*` e `public/stock/*`
- CRUD de produtos, validação de código, move stock, delete, get items, company logo

### 4) Faturação / proformas / guias
- `public/create_invoices/*`, `public/create_proform/*`, `public/guides/*`, `public/credit_notes/*`
- Geração de documento, listagem, exportação, PDF, clone, status

### 5) RH
- `public/rh/*` e `public/ponto.php`, `public/vacations.php`, `public/payroll.php`
- Funcionários, férias, cargos, salários, exportação PDF, ponto, folha de pagamento

### 6) Subscrição / plano / billing
- `public/subscription.php` + `public/subscription/ajax/*`
- criação de charge/order, sincronização, planos, pagamento, limites da empresa

### 7) Dashboard / insights / relatórios
- `public/index.php` e `public/index/ajax/*`
- indicadores, KPI, notificações, stock, relatórios mensais, insights IA, exportação de dados

### 8) Permissões / gestão de utilizadores
- `public/manage_users.php` / `public/manage_users/ajax/*`
- criação, ativação, roles, unlink users, permission checks

## 4. Rotas e endpoints principais

### Autenticação
- `POST /public/login/ajax/process_login.php`
  - Payload: `user_email`, `password`, `remember_me`
  - Resposta: JSON `success`, `message`, `data`
  - Autorização: pública
  - Validações: campos obrigatórios, descriptografia com chave privada, `password_verify`

### Clientes
- `public/contacts/ajax/fetch_contacts.php` – listagem
- `public/contacts/ajax/save_contact.php` – criar
- `public/contacts/ajax/update_contact.php` – atualizar
- `public/contacts/ajax/details_contact.php` – detalhes
- `public/contacts/ajax/delete_contact.php` – eliminar
- `public/contacts/ajax/export_contacts.php` – exportação

### Produtos
- `public/items/ajax/get_items.php`
- `public/items/ajax/check_code.php`
- `public/items/ajax/generate_code.php`
- `public/items/ajax/update_item_inline.php`
- `public/items/ajax/delete_item.php`
- `public/items/ajax/delete_items_bulk.php`

### Facturas/Proformas
- `public/create_invoices/ajax/save_invoices.php`
- `public/create_invoices/ajax/update_invoice.php`
- `public/create_invoices/ajax/get_items.php`
- `public/proform/ajax/fetch_proforms.php`
- `public/proform/ajax/proformas_export.php`
- `public/invoices/ajax/fetch_invoices.php`
- `public/invoices/ajax/delete_invoice.php`

### RH
- `public/rh/ajax/list_employees.php`
- `public/rh/ajax/save_employee.php`
- `public/rh/ajax/search_employees.php`
- `public/rh/ajax/list_payroll.php`
- `public/rh/ajax/save_payroll.php`
- `public/rh/ajax/list_vacations.php`
- `public/rh/ajax/save_vacation.php`
- `public/rh/ajax/update_vacation_status.php`

### Subscrição
- `public/subscription/ajax/create_order.php`
- `public/subscription/ajax/list_orders.php`
- `public/subscription/ajax/mark_paid.php`
- `public/subscription/ajax/set_plan.php`
- `public/subscription/ajax/sync_orders.php`
- `public/assets/ajax/get_company_limits.php`

## 5. Base de dados e regras SQL

O sistema usa um banco MySQL com uma base de dados principal: `u523793545_sistema_fatura`.

Tabelas e entidades observadas:

- `users`
- `companies`
- `company_has_user`
- `sessions`
- `contacts`
- `items`
- `invoices`
- `proformas`
- `guides`
- `credit_notes`
- `stock`
- `employees`
- `vacations`
- `positions`
- `payroll`
- `subscriptions`
- `subscription_orders`
- `notifications`
- `currencies`
- `countries`

Regras observadas diretamente em PHP/SQL:

- sessão validada por token em `sessions` com expiração em UTC
- login cria sessão com `expires_at` e cookie `session_token`
- `subscription_get_company()` e `get_company_limits` calculam validade/limites
- `notifications` tem `notify_key` para garantir idempotência de envio
- `invoice_helper.php` e `document_helper.php` geram numeração e documentos
- exportações PDF/Excel usam bibliotecas oficiais do PHP

## 6. Autenticação e autorização

A autenticação actual usa:

- `session_start()`
- `$_SESSION['user']`
- `$_SESSION['token']`
- `setcookie('session_token', ...)`
- validação de sessão em banco com tabela `sessions`
- `password_verify` com hash do password
- regras de empresa e papel em `company_has_user` (`role`)

A autorização é implementada principalmente na UI por guardas e por middleware em `app/helpers/authentication.php`.

## 7. UI / layout

O layout principal original é claramente definido em:

- `app/views/side.php`: sidebar com navegação por módulos e submenu
- `app/views/nav.php`: header/navbar com notificações e ações globais
- `app/views/layout_creation.php`: app shell, SPA-like loading bar, layout wrapper

Identidade visual principal:

- azul principal `#007abd`
- side bar fixa e unilateral
- top navbar resolvendo `main`/`header`
- cards, modais e Gantt/insights com visual de dashboard administrativo

## 8. Integrações externas

- Google APIs / OAuth login (`loginGoogle`)
- PHPMailer / SMTP para emails
- Dompdf para PDF
- PhpSpreadsheet para exportações
- QRCode (`vendor/chillerlan/php-qrcode`)
- geonames + país/cidade endpoints para endereços
- Appypay / gateway de pagamento e subscrição
- GPO gateway integration

## 9. Efeito prático para a migração Next.js

O sistema original é funcional e bastante completo, mas está disperso em PHP e AJAX. Para preservar tudo sem perder funcionalidade, a migração deve seguir esta estrutura conceptual:

- `Login`
- `Dashboard`
- `Clientes`
- `Produtos`
- `Facturas`
- `Proformas`
- `Guias`
- `RH`
- `Stock`
- `Subscrição`
- `Configurações`

Um modelo de arquitetura recomendado para o Next.js:

- `src/app/(auth)/login`
- `src/app/(dashboard)/dashboard`
- `src/app/(crm)/contacts`
- `src/app/(crm)/items`
- `src/app/(sales)/invoices`
- `src/app/(sales)/proforms`
- `src/app/(hr)/employees`
- `src/app/(settings)/settings`
- `src/components/layout/*`
- `src/components/modules/*`
- `src/lib/api/*`
- `src/lib/auth/*`
- `src/types/*`
- `src/schemas/*`

## 10. Conclusão da auditoria

O projecto já contém todas as bases funcionais: autenticação, gestão de usuários, empresas, clientes, produtos, faturação, RH, stock, dashboards, relatórios, notificações, exportações, PDF e pagamentos. A migração para Next.js deve preservar esta camada funcional real, não substituir por mocks.

A partir desta auditoria, a implementação da nova app deve seguir uma abordagem incremental:

1. scaffold do Next.js
2. layout compartilhado
3. autenticação e sessão
4. dashboard
5. módulos por funcionalidade
6. tratamento de erro e loading states
7. validação e segurança
8. build/test final

