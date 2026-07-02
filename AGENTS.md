# RackTables - Agent Guide

## What is this

RackTables is a datacenter and server room management web application written in PHP 7.0+ (target: 7.3) with a MySQL/MariaDB backend. Current version: **0.22.0**.

## Directory structure

```
wwwroot/          # Web application root
  index.php       # Single entry point — module dispatcher
  inc/            # Core PHP library (loaded via require_once)
  css/            # Stylesheets
  js/             # Client-side JS (jQuery 1.4, RackTables custom)
  pix/            # Images/icons
gateways/         # Device connection gateways (ssh, telnet, ucssdk, etc.)
plugins/          # Local plugin directory (*.php)
scripts/          # CLI helper scripts
tests/            # PHPUnit tests
```

## Entry point & request flow

All requests go through `wwwroot/index.php`. A `module` request parameter selects the handler:

| Module | Purpose |
|---|---|
| `interface` | Main UI — page/tab rendering (default) |
| `redirect` | Form submissions / operations (ophandlers) |
| `ajax` | AJAX endpoints |
| `popup` | Popup helper dialogs |
| `chrome` | Proxy static assets (CSS/JS/images) |
| `image` | Dynamic images (rack thumbnails, progress bars) |
| `svg` | SVG rendering |
| `upgrade` | Schema upgrade wizard |
| `installer` | Initial install wizard |
| `download` | File download |

**`interface` module flow** (`index.php` case):
1. Load `interface.php` → `init.php` (must be after interface.php)
2. `prepareNavigation()` → `requireExtraFiles()` → `fixContext()` → `assertPermission()`
3. Call `$tabhandler[$pageno][$tabno]` or `$page[$pageno]['handler']`
4. Capture output buffer → `renderInterfaceHTML()`

**`redirect` module flow**: validates `$ophandler[$pageno][$tabno][$op]`, calls the handler function, redirects user on success.

## Core library loading order (`init.php`)

```
pre-init.php → config.php → functions.php → database.php → auth.php →
navigation.php → triggers.php → remote.php → caching.php → slb.php → slbv2.php
```

Then loads `secret.php` (from `$path_to_secret_php`), connects DB, loads config, parses RackCode permissions, authenticates user, loads v1 plugins (`plugins/*.php`) and v2 plugins (via `plugin_*_init` hooks).

## Key globals

- `$pdo_dsn`, `$db_username`, `$db_password` — DB credentials (from `secret.php`)
- `$configCache` — cached config vars
- `$pageno`, `$tabno` — current page/tab context (used for security)
- `$page`, `$tab`, `$ophandler`, `$tabhandler` — navigation/operation routing (defined in `navigation.php`)
- `$remote_username` — current authenticated user
- `$op` — current operation name

## Security & permissions

RackTables uses a **tag-based permission system**:
- Tags can be assigned to users, objects, pages, tabs, attributes
- Permissions are evaluated via `permitted()` / `assertPermission()`
- RackCode is a custom DSL for permission rules, parsed and cached
- `fixContext()` builds security context from `$pageno`/`$tabno`
- `getBypassValue()` extracts bypass entity ID from request args

## Adding pages/operations

**1. Define in `navigation.php`:**
```php
$page['mynewpage']['title'] = 'My Page';
$page['mynewpage']['parent'] = 'index';
$tab['mynewpage']['default'] = 'Browse';
$tabhandler['mynewpage']['default'] = 'renderMyNewPage';
```

**2. Add ophandlers:**
```php
$ophandler['mynewpage']['default']['doAction'] = 'doActionHandler';
```

**3. Use `tableHandler` in `ophandlers.php` for simple CRUD:**
```php
$opspec_list['mynewpage-action'] = [
    'table' => 'MyTable',
    'action' => 'INSERT',
    'arglist' => [...],
];
```

**4. Load extra files via `$interface_requires`:**
```php
$interface_requires['mynewpage-default'] = 'my-module.php';
```

## Database layer

- Uses PDO via `database.php` functions
- `usePreparedInsertBlade()`, `usePreparedUpdateBlade()`, `usePreparedDeleteBlade()`, `usePreparedSelectBlade()` — parameterized queries
- Entity mapping via `$SQLSchema` array in `database.php` maps entity names (e.g., `'object'`, `'ipv4net'`) to table names and column names
- `spotEntity($type, $id)` — fetch an entity record by ID
- `commitAddObject()`, `commitUpdateObject()`, `commitDeleteObject()` — entity lifecycle

## Exception hierarchy

```
RackTablesError (base)
  ├─ EntityNotFoundException    — "soft" error, shown in interface
  ├─ InvalidArgException         — internal bug (argument corruption)
  ├─ InvalidRequestArgException  — user input validation failure ("soft")
  ├─ RTDatabaseError             — DB error ("soft")
  ├─ RTDBTableAccessDenied       — insufficient DB privileges
  ├─ RTGatewayError              — device gateway failure ("soft")
  ├─ RTPermissionDenied          — auth failure, shows debug tags
  ├─ RTImageError                — image generation failure
  ├─ RackCodeError              — permission DSL parse error
  └─ ERetryNeeded               — retry signal
```

**Soft errors** (`InvalidRequestArgException`, `RTDatabaseError`, `RTPermissionDenied`) are caught and shown in the UI. **Hard errors** (`InvalidArgException`, uncaught `RackTablesError`) print a full stack trace in debug mode.

## Coding conventions

- **Comments**: `#` for inline comments (not `//`)
- **Array syntax**: `array()` style, never `[]`
- **Indentation**: tabs
- **Braces**: opening brace on same line for control structures, function defs use `function name ()` (space before paren)
- **String quoting**: single quotes for literals, double quotes for interpolation
- **Global access**: always `global $var;` inside functions, not `$GLOBALS['var']`
- **Null coalescing**: manual `isset()` / `array_key_exists()` checks, not `??` operator (PHP 7.0 compatible)
- **Error handling**: try/catch around user input operations, `InvalidRequestArgException` for bad input

## Tests

```bash
# Run all small tests (fast, no DB needed):
phpunit --group small

# Run all tests (requires unit testing DB):
phpunit

# Run a specific test:
phpunit ConfigVarTest
```

**Test setup** (`tests/bootstrap.php`):
- Extends `PHPUnit\Framework\TestCase` into `RTTestCase`
- Requires `_unittest` in the DSN to prevent running against production
- Loads core RackTables modules: `ophandlers.php`, `init.php`, `interface.php`, `snmp.php`

**Test DB**: Must create a separate MySQL database with `_unittest` in the name. See `tests/README.md` for setup instructions.

**Test file naming**: `*Test.php` suffix, each class extends `RTTestCase`.

## Plugin system

- **v1 plugins**: `plugins/*.php` files, loaded during `init.php`
- **v2 plugins**: use `hook` system — register via `callHook('event_name')` and implement `plugin_<name>_init()`
- Hook events: `initFinished`, `resetUIConfig_hook`, etc.

## Gateways

The `gateways/` directory contains connection implementations for network devices:
- `ssh`, `telnet`, `netcat` — basic terminal protocols
- `ucssdk` — Cisco UCS integration
- `git-commit` — config backup

## PHP version

Minimum supported version is **PHP 7.0** (set in `config.php` as `CODE_VERSION`). Do not use PHP 7.1+ features (no `??` operator, no typed properties, no arrow functions, no `match`).

## Gotchas

1. **`init.php` must be loaded *after* `interface.php`** — local.php plugin code runs between them and may override settings
2. **`fixContext()` must be called after including `init.php`** — it sets up the security context from `$pageno`/`$tabno`
3. **`getBypassValue()`** is a special arg accessor — extracts the entity ID that the current page is operating on
4. **Message buffering** — `showError()`/`showSuccess()` queue messages, they're displayed on the *next* page load after redirect
5. **RackCode caching** — permissions are parsed once per request and cached; changes to permission scripts require a fresh request
6. **Entity types** map 1:1 to page names via `$etype_by_pageno` in `functions.php`
7. **`$interface_requires`** supports glob patterns: `'page-*'` loads files for all tabs of a page
