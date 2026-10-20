# AGENTS.md — using DuckAdmin in your own project

**Scope.** This file is for AI agents (and humans) *consuming* the `dvaknheo/duckadmin` **library** in their
own PHP project. It is **not** about developing DuckAdmin itself; if you are editing files inside this
repository, read `README.md` instead (and keep the coverage numbers there honest).

DuckAdmin is a **baseline library**, not a project skeleton. You `composer require` it, mount one or more of
its applications on your own `DuckPhp` app, and build on top.

---

## 1. Install

```bash
composer require dvaknheo/duckadmin
```

Requirements: PHP >= 7.4 (measured on 8.2), `dvaknheo/duckphp` (installed automatically).
`sqlite` is enough for a first run; `mysql` works by changing `database_driver`.

## 2. Minimal embedding

Call `RunQuickly()` **once**, at the top of your front controller. It returns `true` when the request was
fully handled by one of the mounted applications — then just return. When it returns `false`, run your own code.

```php
<?php
use DuckPhp\DuckPhp;
use DuckAdmin\System\DuckAdminApp;

$options = [
    'path'     => __DIR__ . '/../vendor/dvaknheo/duckadmin',  // the library's root dir
    'skip_404' => true,          // routes DuckAdmin did not catch fall through to you
    'app' => [
        DuckAdminApp::class => [
            'controller_url_prefix' => 'app/admin/',   // where the admin backend is mounted
            // 'installed'      => '2024-06-10T22:12:01+08:00',   // pretend it is installed
            // 'database_list'  => [['dsn' => 'sqlite:demodb.db', 'username' => '', 'password' => '']],
        ],
    ],
];

if (DuckPhp::RunQuickly($options)) {
    return;                      // handled by DuckAdmin
}

// ... your own framework's code ...
$admin_id   = \DuckPhp\Foundation\Helper::AdminId(false);   // false when not logged in
$admin_name = $admin_id ? \DuckPhp\Foundation\Helper::AdminName() : '';
$url_logout = \DuckPhp\Foundation\Helper::Admin()->urlForLogout();
```

**Security gate.** Nothing is served unless the environment config explicitly enables it. Put
`'duckadmin_enable' => true` in your `config/DuckPhpSettings.config.php` (copied from the
`.sample`). Without it every request — including the installer — gets a 503 notice page. This is deliberate:
the switch means "I intend to expose an admin backend". The CLI is **not** exempt.

## 3. The applications you can mount

DuckAdmin ships one root application plus seven child applications. Mount the child ones you need in
`options['app']`, each with its own `controller_url_prefix`:

| Class | Prefix (default) | What it is |
|---|---|---|
| `DuckAdmin\Admin\System\AdminApp` | `admin/` | The admin backend. **Database-backed**, ships migrations/seeds, the default admin provider. |
| `DuckAdmin\Admin_LaravelModel\System\LaravelModelApp` | `admin_laravel/` | Same admin, but the Model layer is overridden with Eloquent (`illuminate/database`). |
| `DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp` | `admin_thinkphp/` | Same admin, but the Model layer is overridden with ThinkORM (`topthink/think-orm`). |
| `DuckAdmin\SingleAdmin\System\SingleAdminApp` | `single_admin/` | Config-based **single admin** provider (no database). Good for tiny deployments. |
| `DuckAdmin\DemoUsers\System\DemoUsersApp` | `demo_users/` | Preset-account **user** provider (no database). Good for demos. |
| `DuckAdmin\User\System\UserApp` | `user/` | The user system: register / login / password / user management. Database-backed. |
| `DuckAdmin\SimpleBlog\System\SimpleBlogApp` | `blog/` | Sample business app showing how to build on top of the admin + user systems. |

**Exactly one admin provider and one user provider can be active at a time.** The pair is stored in the
root application's data (`duckadmin_admin_provider` / `duckadmin_user_provider`) and switched from the root
application's home page form. Disabled providers simply do not answer; mounted-but-disabled is fine.

A typical full setup mounts `AdminApp` + `UserApp` + `SimpleBlogApp` (that is what the demo does), or
`SingleAdminApp` + `DemoUsersApp` for a zero-database demo.

## 4. The two extensions: `DuckAdmin\Ext`

`src/Ext/` holds the ORM override layers:

| Class | Used by | What it does |
|---|---|---|
| `DuckAdmin\Ext\LaravelOrmExt` | `Admin_LaravelModel` | Replaces the admin's Model/Adapter layer with **Eloquent** models (same tables: `admin_admins`, `admin_roles`, `admin_permissions`, …). |
| `DuckAdmin\Ext\ThinkPHPOrmExt` | `Admin_ThinkPHPModel` | Same, with **ThinkORM** models. |

You do **not** register these by hand: they are wired in by `LaravelModelApp` / `ThinkPHPModelApp`. To use
them, mount **those** applications instead of `AdminApp` (and `composer require` the matching ORM, which is
already in `require`, so it is present). Adding a third ORM means copying the pattern of one of them —
do that inside a fork of DuckAdmin, not by monkey-patching `vendor/`.

## 5. Overriding the default views

Every application renders its views from its own `view/` directory (`src/<App>/view/...`). To replace them,
point that application's **`path_view`** option at **your own absolute directory**:

```php
$options['app'][\DuckAdmin\Admin\System\AdminApp::class]['path_view'] = '/srv/myapp/views/duckadmin/';
```

The framework's view resolver accepts an absolute path for `path_view` (see `DuckPhp\Core\App::getOverrideableFile()`),
and the view *name* passed in is the same one the shipped view uses, so you only need to provide the files
you actually want to change, e.g.:

```
/srv/myapp/views/duckadmin/MainController/index.php
/srv/myapp/views/duckadmin/AdminNew/user_list.php
/srv/myapp/views/duckadmin/_sys/error_404.php     # 404 page for that app
```

The root application's own view (`view/main.php`, the "pick your admin/user provider" page) is overridden
the same way via `DuckAdmin\System\DuckAdminApp`.

Two related options live on the applications, not on DuckAdmin:

- `error_404` / `error_500` — the view name used for the 404/500 pages. If a child application has no such
  file, give the option an **absolute path** to one shared file (`View::getViewFile()` → `isAbsPath()` shortcut).
- `view_header` / `view_footer` — not used by DuckAdmin's applications (each view renders its own layout).

Also note: **language files** are per application (`src/<App>/config/lang-{zh_CN,en_US}.php`) and are
overridden through the Lang component's own options, not through `path_view`.

## 6. Static resources

CSS/JS/images ship in `src/<App>/res/` (e.g. `src/Admin/res/`). Their public location is controlled by the
`controller_resource_prefix` option:

```php
$options['controller_resource_prefix'] = 'res/';            // default: a directory under your web root
// or an absolute http(s) URL, e.g. a CDN or a statically served host:
$options['controller_resource_prefix'] = 'https://cdn.example.com/duckadmin/';
// or an absolute path starting with "/":
$options['controller_resource_prefix'] = '/assets/duckadmin/';
```

The web installer copies the shipped files into that directory for you. If you serve them from a CDN or a
separate host, copy `res/` there once and set the option to the URL — DuckAdmin never hard-codes the path.

## 7. Rules for AI agents working on a project that uses DuckAdmin

- **Never edit `vendor/`.** If you need a behaviour change, do it in *your* project: options, a view override,
  a subclass, or your own controller/Business layer. Upstream patches belong in the duckadmin repo.
- **Keep the layering**: `System (app entry) → Controller → Business → Model`. Views are plain PHP files.
- **One admin + one user provider** at a time — do not "fix" a `302` by enabling two providers.
- **The coverage tooling** (`src/**/System/TestLister.php`, `.ai/cover_*.sh`, `DuckCoverage`) is **development
  only**. It must never run in production; the `duckcoverage_enable` switch guards it.
- **`duckadmin_enable` is a security switch**: leave it off in production environments unless the backend is
  intentionally exposed, and never enable it from code that a request can reach.
- **The installer is idempotent-ish but not magic**: once an application is installed, visiting its
  `<prefix>/install` 302s back to the home page. To reinstall, delete that application's data config and its
  database, then visit the install page again.
- If you change an application's views or resources, re-run its install once so the resource files are copied.

## 8. Where to read more

- `README.md` in this package: the full feature list, embedding recipes, secondary-development notes.
- `README.zh_CN.md`: the same in Chinese.
- `vendor/dvaknheo/duckphp/docs/`: the framework guide (52 chapters) and the per-class API reference.
- `DuckPhp\Foundation\Helper` (`Admin()`, `AdminId()`, `AdminName()`, `User()`, …) is the supported way to
  reach the admin/user systems from your own controllers and views.
