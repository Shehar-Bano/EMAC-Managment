# ERP Dashboard Development Standards & Architecture
**Laravel-based ERP System — Functional, UI, Security, API & Coding Standards**  
**Version:** 1.0 | Development Baseline

---

## Purpose
This document defines the mandatory development standards for building a scalable, secure, maintainable, and consistent ERP dashboard. All modules, CRUD operations, permissions, APIs, and UI screens must follow the same architecture and reusable patterns.

---

## Table of Contents
1. [Project Objectives & Core Principles](#1-project-objectives--core-principles)
2. [Modular Project Architecture](#2-modular-project-architecture)
3. [Roles & Permission Group Architecture](#3-roles--permission-group-architecture)
4. [Permission Naming & Seeder Rules](#4-permission-naming--seeder-rules)
5. [Authentication & Authorization](#5-authentication--authorization)
6. [Error Handling & Exception Management](#6-error-handling--exception-management)
7. [Laravel Application Layers](#7-laravel-application-layers)
8. [Controllers & Route Standards](#8-controllers--route-standards)
9. [Models, Relationships, Accessors & Mutators](#9-models-relationships-accessors--mutators)
10. [Database Standards](#10-database-standards)
11. [CRUD & Index Page Standards](#11-crud--index-page-standards)
12. [Filters, Search, Pagination & Bulk Actions](#12-filters-search-pagination--bulk-actions)
13. [Blade, Components & UI Standards](#13-blade-components--ui-standards)
14. [Global Variables, JavaScript & SweetAlert](#14-global-variables-javascript--sweetalert)
15. [API Architecture & Sanctum](#15-api-architecture--sanctum)
16. [Swagger / OpenAPI Documentation](#16-swagger--openapi-documentation)
17. [Performance & Scalability](#17-performance--scalability)
18. [Security Standards](#18-security-standards)
19. [Observer & Event Rules](#19-observer--event-rules)
20. [Validation & Form Request Rules](#20-validation--form-request-rules)
21. [Code Reuse & Duplication Rules](#21-code-reuse--duplication-rules)
22. [Standard Module Example](#22-standard-module-example)
23. [Development Checklist / Definition of Done](#23-development-checklist--definition-of-done)

---

## 1. Project Objectives & Core Principles
- **Modular & Scalable**: The ERP must be modular, scalable, secure, and easy to maintain.
- **DRY (Don't Repeat Yourself)**: Code should be minimized through reusable services, actions, components, helpers, and common UI patterns.
- **Thin Controllers**: Controllers must remain thin and should never contain business-heavy logic.
- **Strict Authorization & Error Handling**: Every sensitive operation must have proper authorization and robust error handling.
- **Consistency**: All modules must follow the same folder structure, naming conventions, CRUD layout, and UI behavior.
- **Future-Proof**: The system must support future modules without requiring core architectural changes.
- **No Business Logic Duplication**: Business logic must not be duplicated across controllers, APIs, jobs, or views.
- **Explicit Eloquent Relationships**: Database relationships must be explicitly defined and utilized via Eloquent relationships wherever appropriate.
- **User-Friendly Error Messages**: All user-facing errors must be converted into clear, understandable messages; technical details must be logged securely.
- **Server-Side Security**: Security checks must be strictly enforced on the server side even when UI elements are conditionally hidden.

---

## 2. Modular Project Architecture
The project must be organized module-wise. A module should contain its own controllers, requests, services/actions, resources, and views where practical.

```
app/
├── Actions/
├── Exceptions/
├── Helpers/
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   │   └── Admin/
│   │   │       └── User/
│   │   └── Api/
│   │       └── V1/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Observers/
├── Services/
└── Support/

resources/
├── views/
│   ├── layouts/
│   ├── components/
│   └── admin/
│       ├── dashboard/
│       └── modules/
└── js/

routes/
├── web.php
└── api/
    └── v1.php
```

- **Separated Routes**: Routes must be separated by purpose and API version.
- **Web/Admin Routes**: Use module prefixes and named route groups.
- **API Versioning**: API routes must be explicitly versioned: `/api/v1/...`
- **View Grouping**: Views should be grouped cleanly under `resources/views/admin/modules/{module}/`.
- **Reusable Components**: Reusable UI belongs in Blade components (`resources/views/components/`) rather than being duplicated between modules.
- **Shared Business Operations**: Common business workflows must be encapsulated into `Actions/` or `Services/`.

---

## 3. Roles & Permission Group Architecture
Authorization is maintained through **Roles** and module-wise **Permission Groups**. Permissions represent individual actions; Permission Groups organize those actions for clean administration.

```
Permission Group
      ↓
Permissions (individual actions)
      ↓
Role
      ↓
User
```

### 3.1 Permission Groups
- Each ERP section/module must have its own Permission Group (e.g., *User Management*, *Role Management*, *Department Management*, *Audit Management*, *Reports*, *Settings*).
- The Permission Group management UI must display all permissions belonging to that section.
- Permissions must be grouped visually (accordion, card, or tabbed structure) for easy administration.
- A permission must belong to exactly one logical module/section.
- Permission Groups must be reusable for both Role creation and Role editing.

### 3.2 Individual Permissions
- Every meaningful action must have its own granular permission.
- **Standard CRUD Permissions**: `view`, `create`, `edit`, `delete`.
- **Additional Action Permissions**: `bulk-delete`, `status`, `export`, `approve`, `reject`, `restore`, `assign`, `import`, etc.
- **Naming Pattern**: `module.action` (e.g., `users.view`, `users.create`, `users.edit`, `users.delete`, `users.bulk-delete`, `users.status`, `users.export`).
- Backend authorization must check the permission for the exact operation being executed.
- Hiding a button/menu is strictly a UI convenience; it is never a replacement for backend authorization.

### 3.3 Roles
- A Role is a collection of permissions required for a specific job/function.
- Role create/edit forms must present Permission Groups section-wise.
- Admins can select individual permissions or select all permissions within a group with a single toggle.
- Role edit must dynamically load existing permissions without duplicating database records.
- Users receive access through assigned roles/permissions.

### 3.4 Super Admin
- Super Admin must have unconditional system-wide access.
- Super Admin access should be handled centrally via a Gate bypass (`Gate::before`) or role check rather than manually hardcoding permissions.
- If explicit database permissions are stored for audit purposes, they must remain synchronized via the permission seeder.
- Super Admin must never be blocked by missing individual module permissions.

---

## 4. Permission Naming & Seeder Rules
- The **Permission Seeder** is the single source of truth for system permissions.
- New permissions must always be registered in the permission seeder / central permission definition file.
- **Idempotency**: Running `db:seed` repeatedly must never create duplicate records or crash.
- Permission groups must also be seeded centrally.
- Format: `module.action` (lowercase snake/kebab-case).
- Never hardcode random permission strings across multiple controllers/views.
- Human-readable labels should be displayed in the UI while internal permission names remain immutable.
- A feature/CRUD is not complete until its permissions are seeded.

| Module | Permission | Purpose |
| :--- | :--- | :--- |
| Users | `users.view` | View / list users |
| Users | `users.create` | Create user |
| Users | `users.edit` | Edit user |
| Users | `users.delete` | Delete single user |
| Users | `users.bulk-delete` | Bulk delete selected users |
| Users | `users.status` | Toggle user status (active/inactive) |
| Users | `users.export` | Export users data |

---

## 5. Authentication & Authorization

### 5.1 Web Authentication
- Standard Laravel session-based authentication & middleware.
- Unauthenticated requests must redirect to the login screen with intended URL preserved.
- Authenticated users must pass authorization gates/policies for every protected module/action.
- Session timeouts must be handled gracefully with clear flash notices.

### 5.2 API Authentication
- Use **Laravel Sanctum** for token-based API authentication.
- Protected API routes must use `auth:sanctum` middleware.
- API tokens must never be logged, printed in URLs, or exposed in error responses.
- API authentication failures must return standard JSON responses with `401 Unauthorized`.

### 5.3 Authorization
- Use Policies, Form Request `authorize()` methods, and Gates.
- Check authorization at route, controller, and service boundaries for all sensitive operations.
- Clearly differentiate between `401 Unauthorized` (unauthenticated) and `403 Forbidden` (insufficient permissions).

---

## 6. Error Handling & Exception Management
No raw database exceptions, SQL queries, stack traces, or framework crash pages should ever be exposed to end users in production.

| Error Type | Required Handling | User Response |
| :--- | :--- | :--- |
| **Validation** | Form Request / API Validation | Field-level validation messages (422) |
| **Unauthenticated** | Auth middleware / Exception Handler | Redirect to Login / 401 JSON |
| **Unauthorized** | Permission / Policy check | Access Denied / 403 JSON |
| **Not Found** | Model binding / Handler | Record not found / 404 JSON |
| **Duplicate Data** | Validation rule + DB unique index | Record already exists message |
| **Foreign Key Constraint** | Validate dependencies + Catch DB exception | Cannot delete because record is in use |
| **Database / SQL** | Central Exception Handler + Logging | "Something went wrong. Please try again." |
| **File Upload** | Validation + Storage exception handling | Specific file error message |
| **Unexpected Exception** | Central Exception Handler + Monolog | Generic safe error message (500) |
| **API Error** | Standard JSON exception response | Unified `{ "success": false, "message": "..." }` |

- Use **centralized exception handling** (`bootstrap/app.php` in Laravel 11+) rather than repetitive try/catch blocks in every controller.
- Use local try/catch only when a specific recovery or rollback action is required.
- **Database Transactions**: Multi-step write operations must be wrapped in `DB::transaction()`.
- API endpoints must **never** return HTML error pages.

---

## 7. Laravel Application Layers

| Layer | Responsibility | Must Not Become |
| :--- | :--- | :--- |
| **Controller** | Receive request, authorize, call action/service, return response/view | Business logic dump |
| **Form Request** | Validation rules + request-level authorization | Database/business workflow engine |
| **Action** | One single focused business operation | A giant god-class utility |
| **Service** | Reusable multi-step / domain workflow | A 1:1 duplicate of every controller method |
| **Model** | Data structure, relationships, casts, scopes, accessors | Application workflow controller |
| **Resource** | Transform API model data into consistent JSON output | Business logic executor |
| **Observer** | Model lifecycle side effects (auditing, cleanup) | Main application workflow driver |
| **Helper** | Small generic reusable helper utilities | Business-specific logic container |

---

## 8. Controllers & Route Standards
- Controllers must remain thin and short: **Authorize $\rightarrow$ Validate $\rightarrow$ Execute Action $\rightarrow$ Return Response**.
- Avoid duplicating business logic between web controllers and API controllers.
- Use named routes and structured route groups with prefixes.
- Separate web controllers (`App\Http\Controllers\Web\Admin\...`) and API controllers (`App\Http\Controllers\Api\V1\...`).

```php
// Route Example (routes/web.php)
Route::prefix('admin/users')->name('admin.users.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index')->middleware('can:users.view');
    Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('can:users.create');
    Route::post('/', [UserController::class, 'store'])->name('store')->middleware('can:users.create');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('can:users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update')->middleware('can:users.edit');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('can:users.delete');
    Route::delete('/bulk-delete', [UserController::class, 'bulkDelete'])->name('bulk-delete')->middleware('can:users.bulk-delete');
    Route::patch('/{user}/status', [UserController::class, 'updateStatus'])->name('status')->middleware('can:users.status');
});
```

---

## 9. Models, Relationships, Accessors & Mutators
- All relationships must be explicitly defined (`belongsTo`, `hasMany`, `belongsToMany`, etc.).
- Foreign keys must match model relationships and database schema constraints.
- Always use **eager loading** (`with(['relationship'])`) to prevent N+1 query performance issues.
- Use modern Laravel `Attribute` accessors/mutators for formatting and value normalization.
- Use **query scopes** (`scopeActive`, `scopeFilter`, `scopeSearch`) for reusable SQL constraints.
- Never place heavy business logic or database write operations inside accessors/mutators.

---

## 10. Database Standards
- Use consistent `snake_case` table and column names.
- Foreign keys must declare appropriate `onDelete` behavior (`cascade`, `restrict`, `nullOnDelete`).
- Add composite or single database indexes for columns frequently filtered, sorted, or joined.
- Enforce `unique` constraints at the database level for unique business entities.
- Use `nullable` only when null represents a valid business state.
- Use `softDeletes` where audit trails and record recovery are required.
- Large tables must be indexed and designed with pagination in mind.

---

## 11. CRUD & Index Page Standards
Every dashboard CRUD module must follow a uniform visual and functional layout:
- **Header Section**: Page title, breadcrumbs, primary action buttons (e.g., *Add New*, *Export*).
- **Filter Bar**: Search input, Status dropdown filter, Date/Date-range picker, Reset button.
- **Table Controls**: Select-all checkbox, dynamic items-per-page selector (`10`, `20`, `50`, `100`, `All`), bulk action buttons.
- **Table Columns**: High-priority business data, formatted dates, status badge, action dropdown.
- **Status Badges**: Standard color-coded component across the entire dashboard.
- **Action Dropdown**: Standardized dropdown menu with icons (View, Edit, Delete, Custom Actions).
- **Confirmation Dialogs**: SweetAlert2 confirmation required for delete and bulk actions.

---

## 12. Filters, Search, Pagination & Bulk Actions

### 12.1 Pagination
- Dynamic pagination required on every index page.
- Allowed options: **`10`**, **`20`**, **`50`**, **`100`**, and **`All`**.
- The selected `per_page` value must persist during filtering.
- Prevent loading all records by default; handle `All` with safe limits/permissions if dataset is large.

### 12.2 Bulk Delete Workflow
1. Each table row includes an individual selection checkbox (`value="{id}"`).
2. Table header checkbox provides "Select All" for the current page/result set.
3. Selected count counter displays dynamically (e.g., `Selected: 3`).
4. Clicking "Delete Selected" prompts a SweetAlert2 confirmation dialog.
5. On confirmation, selected IDs are sent to the bulk-delete endpoint.
6. Backend verifies `users.bulk-delete` permission, validates IDs array, checks dependency constraints, and deletes inside a transaction.
7. User receives clear feedback (full success or summary of skipped/restricted records).

```
[ ] Select All       Name            Status       Date Created       Actions
─────────────────────────────────────────────────────────────────────────────
[✓]                  John Doe        Active       2026-09-01         ⋮ [Edit/Delete]
[✓]                  Jane Smith      Inactive     2026-09-02         ⋮ [Edit/Delete]

Selected: 2 records  |  [🗑️ Delete Selected]
```

---

## 13. Blade, Components & UI Standards
- Use a central Blade layout (`layouts/admin.blade.php`).
- Build reusable Blade components in `resources/views/components/`:
  - `<x-card>`
  - `<x-button>`
  - `<x-input>`
  - `<x-select>`
  - `<x-status-badge :status="$status" />`
  - `<x-filter-bar>`
  - `<x-table>`
  - `<x-action-dropdown>`
  - `<x-pagination>`
- Never duplicate identical HTML structures between modules.
- Consistent color palette:
  - **Primary**: Brand Navy / Indigo
  - **Success**: Emerald Green (`Active`, `Approved`, `Completed`)
  - **Warning**: Amber / Orange (`Pending`, `In Review`)
  - **Danger**: Crimson Red (`Inactive`, `Rejected`, `Deleted`)
  - **Neutral**: Slate Gray (`Draft`, `Archived`)

---

## 14. Global Variables, JavaScript & SweetAlert
Common front-end behaviors and alerts must use a centralized JavaScript utility:

```javascript
// Window ERP Helpers (e.g., resources/js/admin/app.js)
window.ERP = {
    showSuccess: (message) => { /* SweetAlert Toast / Alert */ },
    showError: (message) => { /* SweetAlert Error Alert */ },
    showWarning: (message) => { /* SweetAlert Warning Alert */ },
    confirmDelete: (formOrCallback, options = {}) => {
        Swal.fire({
            title: options.title || 'Are you sure?',
            text: options.text || "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof formOrCallback === 'function') formOrCallback();
                else formOrCallback.submit();
            }
        });
    },
    confirmBulkDelete: (callback) => {
        Swal.fire({
            title: 'Delete Selected Records?',
            text: 'Are you sure you want to delete the selected items?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete all selected'
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    },
    handleAjaxError: (xhr) => {
        if (xhr.status === 422) {
            // Display field validation errors
        } else if (xhr.status === 403) {
            ERP.showError('Access denied: You do not have permission for this action.');
        } else {
            ERP.showError('An unexpected error occurred. Please try again.');
        }
    }
};
```

---

## 15. API Architecture & Sanctum
- All API routes live under `routes/api/v1.php` and prefix `/api/v1/`.
- Authenticate protected API requests using Laravel Sanctum (`auth:sanctum`).
- Use dedicated **Form Requests** for API validation and **API Resources** for response transformation.
- Unified JSON response format:

```json
{
    "success": true,
    "message": "Users retrieved successfully",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "last_page": 8
    }
}
```

- Standard HTTP status codes: `200 OK`, `201 Created`, `400 Bad Request`, `401 Unauthorized`, `403 Forbidden`, `404 Not Found`, `422 Unprocessable Content`, `500 Internal Error`.

---

## 16. Swagger / OpenAPI Documentation
- Every API endpoint must be documented using OpenAPI / Swagger annotations or config.
- Documentation must include:
  - Endpoint & HTTP method.
  - Required authentication headers (`Bearer {token}`).
  - Query parameters & request body schema.
  - Validation rules & field types.
  - Response samples (`200`, `201`, `401`, `403`, `404`, `422`, `500`).
- Documentation must stay synchronized with active code.

---

## 17. Performance & Scalability
- **Eager Loading**: Always eliminate N+1 queries using `with()`, `load()`, or `withCount()`.
- **Selected Columns**: Avoid `SELECT *` on wide tables when only a few columns are needed.
- **Database Indexes**: Index columns used in `WHERE`, `ORDER BY`, and `JOIN` clauses.
- **Chunking / Lazy Collections**: Use `lazy()` or `chunkById()` for processing large record sets.
- **Queues & Jobs**: Offload emails, external API calls, PDF generation, and exports to background workers.
- **Caching**: Cache static system settings and role permissions with proper cache invalidation on update.

---

## 18. Security Standards
- **Strict Server-Side Validation**: Validate all inputs via Form Requests.
- **Mass Assignment**: Maintain explicit `$fillable` arrays on all Eloquent models.
- **File Upload Security**: Validate MIME types, file extensions, and file sizes. Store files outside public webroot or via secure storage disks.
- **ID Authorization**: Never trust client IDs; check `$user->can('action', $record)` on individual entities.
- **CSRF Protection**: All web forms and AJAX mutations must send valid CSRF tokens.
- **Password Security**: Use strong password rules and Laravel default bcrypt/argon2 hashing.

---

## 19. Observer & Event Rules
- **Observers**: Use for lightweight model lifecycle events (e.g., audit logging, UUID generation, file cleanup).
- Never place heavy business logic or long synchronous loops inside Observers.
- **Events & Listeners**: Use for decoupled domain events (e.g., `UserRegistered`, `InvoiceApproved`).
- **Queued Listeners**: Heavy side effects (emails, notifications) must implement `ShouldQueue`.

---

## 20. Validation & Form Request Rules
- Dedicated Form Requests for store, update, and bulk operations.
- Update requests must properly ignore the current model ID on unique rules:
  `Rule::unique('users', 'email')->ignore($this->user)`.
- Enforce cross-field logical rules (e.g., `start_date <= end_date`).
- Bulk operation requests must validate both the array and each individual integer ID:
  ```php
  'ids' => ['required', 'array', 'min:1'],
  'ids.*' => ['required', 'integer', 'exists:users,id'],
  ```

---

## 21. Code Reuse & Duplication Rules
- **Shared Web & API Logic**: Extract shared business workflows into `App\Actions\{Module}\{ActionName}` or `App\Services\{ModuleName}Service`.
- **Reusable Filters/Queries**: Extract into Eloquent Query Scopes or Query Filter classes.
- **Reusable UI**: Encapsulate into Blade components.
- **No Redundant Helpers**: Only create generic helper functions in `App\Helpers\` if they serve multiple domains.

---

## 22. Standard Module Example: User Management

### File Organization
```
app/
├── Http/Controllers/
│   ├── Web/Admin/User/UserController.php
│   └── Api/V1/User/UserController.php
├── Http/Requests/User/
│   ├── StoreUserRequest.php
│   ├── UpdateUserRequest.php
│   └── BulkDeleteUserRequest.php
├── Actions/User/
│   ├── CreateUserAction.php
│   ├── UpdateUserAction.php
│   ├── DeleteUserAction.php
│   └── BulkDeleteUsersAction.php
├── Http/Resources/UserResource.php
├── Models/User.php
└── Observers/UserObserver.php

resources/views/admin/modules/users/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php

routes/
├── web.php
└── api/v1.php
```

### Standard Execution Flows

#### Create Operation Flow
```
Route (POST /admin/users)
  └── Middleware (auth, verified)
      └── Permission Check (can:users.create)
          └── StoreUserRequest (Validation)
              └── CreateUserAction (DB Transaction + Business Logic)
                  └── User Model + Relationships Saved
                      └── UserObserver / Event Triggered
                          └── Redirect to Index with Flash Message / Return UserResource
```

#### Delete Operation Flow
```
Route (DELETE /admin/users/{user})
  └── Middleware (auth)
      └── Permission Check (can:users.delete)
          └── Dependency Check (Does user own active contracts/records?)
              └── DeleteUserAction (Transaction + Soft/Hard Delete)
                  └── UserObserver (Audit Log / Clean Cache)
                      └── SweetAlert Success Response
```

---

## 23. Development Checklist / Definition of Done (DoD)
A module/CRUD/API is not considered complete until every applicable item in this checklist is verified:

- [ ] **Folder Structure**: Follows standard folder, namespace, and route structure.
- [ ] **Permission Group**: Module has its dedicated Permission Group.
- [ ] **Granular Permissions**: Every CRUD and custom action has its own `module.action` permission.
- [ ] **Idempotent Seeder**: Permissions and default roles are registered in the central seeder.
- [ ] **Super Admin Access**: Super Admin bypasses restrictions centrally.
- [ ] **Server Authorization**: Backend verifies permissions for all routes/actions.
- [ ] **Form Requests**: Create, update, and bulk requests use dedicated Form Requests.
- [ ] **Thin Controller**: Controller delegates business logic to Actions/Services.
- [ ] **Action/Service Layer**: Multi-step workflows and DB mutations are isolated in Actions/Services.
- [ ] **Eloquent Relationships**: Relationships, foreign keys, and casts are explicitly defined.
- [ ] **Index Page Filters**: Search, Status, and Date filters are implemented.
- [ ] **Dynamic Pagination**: Pagination supports `10`, `20`, `50`, `100`, and `All`.
- [ ] **Bulk Actions**: Row checkboxes, header select-all, and bulk delete are implemented with permission checks.
- [ ] **UI Consistency**: Standard Blade components, status badges, and action dropdowns are used.
- [ ] **SweetAlert Integration**: Deletions and confirmations use standard SweetAlert methods.
- [ ] **Error Handling**: Database foreign-key, 403, 404, 422, and 500 errors are safely handled without raw traces.
- [ ] **API Endpoint**: `/api/v1/` endpoint uses Sanctum, Form Requests, and API Resources.
- [ ] **Swagger Documentation**: Endpoints, parameters, request body, and responses are documented.
- [ ] **Performance (N+1)**: Eager loading is applied; required database indexes exist.
- [ ] **No Duplication**: Common logic is shared between Web and API via Actions.

---
*End of Development Standards & Architecture Baseline v1.0*
