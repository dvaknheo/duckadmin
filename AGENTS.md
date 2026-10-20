# AGENTS.md — using DuckAdmin in your own project

**Scope.** This file is for AI agents (and humans) *consuming* the `dvaknheo/duckadmin` **library** in their
own PHP project. It is **not** about developing DuckAdmin itself; if you are editing files inside this
repository, read `README.md` instead.

DuckAdmin is a **baseline library**, not a project skeleton. You `composer require` it, mount its applications
on your own `DuckPhp` app, and build on top.

---

## 1. Install

```bash
composer require dvaknheo/duckadmin
```

PHP >= 7.4 (measured on 8.2); `dvaknheo/duckphp` comes with it. `sqlite` is enough for a first run.

## 2. Minimal embedding

Run `DuckPhp::RunQuickly()` **once** at the top of your front controller. It returns `true` when one of the
mounted applications handled the request — then just return; when it returns `false`, run your own code.

Two things are easy to get wrong:

- the **root application must enable `data_file_enable`** — the install state and the selected
  admin/user providers are persisted through it; without it nothing is stored;
- the `app` list is **exactly the one `DuckAdmin\System\DuckAdminApp` itself uses** (see
  `src/System/DuckAdminApp.php` → `options['app']`): every child application you want, with its own
  `controller_url_prefix`, and `admin_provider_enable` / `user_provider_enable => false` for the providers
  you are *not* selecting right now. Do not invent your own shape.

```php
<?php
use DuckPhp\DuckPhp;

$options = [
    'path'             => __DIR__ . '/../vendor/dvaknheo/duckadmin',  // the library's root dir
    'skip_404'         => true,          // routes DuckAdmin did not catch fall through to you
    'data_file_enable' => true,          // ★ the root application must enable this

    'app' => [
        // the root application
        \DuckAdmin\System\DuckAdminApp::class => [],

        // the child applications — same list/shape as DuckAdminApp's own options['app']
        \DuckAdmin\Admin\System\AdminApp::class => [
            'controller_url_prefix' => 'admin/',
            'admin_provider_enable' => false,     // only one admin provider may be enabled
        ],
        \DuckAdmin\User\System\UserApp::class => [
            'controller_url_prefix' => 'user/',
            'user_provider_enable'  => false,     // only one user provider may be enabled
        ],
        \DuckAdmin\SimpleBlog\System\SimpleBlogApp::class => [
            'controller_url_prefix' => 'blog/',
        ],
    ],
];

if (DuckPhp::RunQuickly($options)) {
    return;                      // handled by DuckAdmin
}

// ... your own code ...
$admin_id   = \DuckPhp\Foundation\Helper::AdminId(false);   // false when not logged in
$admin_name = $admin_id ? \DuckPhp\Foundation\Helper::AdminName() : '';
$url_logout = \DuckPhp\Foundation\Helper::Admin()->urlForLogout();
```

**Security gate.** Nothing is served unless the environment config explicitly enables it: put
`'duckadmin_enable' => true` in `config/DuckPhpSettings.config.php` (copy the `.sample`). Without it every
request — including the installer — gets a 503 notice page. The CLI is **not** exempt.

## 3. The applications you can mount

One root application plus seven child applications, each mounted under its own `controller_url_prefix`:

| Class | Prefix (default) | What it is |
|---|---|---|
| `DuckAdmin\Admin\System\AdminApp` | `admin/` | The admin backend. Database-backed, the default admin provider. |
| `DuckAdmin\Admin_LaravelModel\System\LaravelModelApp` | `admin_laravel/` | Same admin, Model layer replaced with Eloquent (see §6). |
| `DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp` | `admin_thinkphp/` | Same admin, Model layer replaced with ThinkORM (see §7). |
| `DuckAdmin\SingleAdmin\System\SingleAdminApp` | `single_admin/` | Config-based single admin provider (no database). |
| `DuckAdmin\DemoUsers\System\DemoUsersApp` | `demo_users/` | Preset-account user provider (no database). |
| `DuckAdmin\User\System\UserApp` | `user/` | The user system: register / login / password / user management. |
| `DuckAdmin\SimpleBlog\System\SimpleBlogApp` | `blog/` | Sample business app built on the admin + user systems. |

**Exactly one admin provider and one user provider can be active at a time.** The pair lives in the root
application's data (`duckadmin_admin_provider` / `duckadmin_user_provider`) and is switched from the root
application's home page form. Mounted-but-disabled is normal; two enabled providers is not.

A typical full setup mounts `AdminApp` + `UserApp` + `SimpleBlogApp` (what the demo does); a zero-database
demo mounts `SingleAdminApp` + `DemoUsersApp`.

## 4. Overriding the default views

A view can be overridden by dropping a file with the **same view name** into a directory named after the
application's `options['name']` under your project's `view/` root:

```
<project_root>/view/<AppName>/<same view name>.php
```

`AppName` is the application's `options['name']` — for the admin backend that is `DuckAdmin`
(`AdminApp`'s options), so for example:

```
<project_root>/view/DuckAdmin/MainController/index.php
<project_root>/view/DuckAdmin/AdminNew/user_list.php
<project_root>/view/DuckAdmin/_sys/error_404.php
```

Only provide the files you actually want to change; everything else keeps using the shipped view. The same
rule applies to every application (use *its* `options['name']`). Language files are separate — they live in
each application's `config/lang-*.php` and are handled by the Lang component, not by this view override.

## 5. Static resources

CSS/JS/images ship in `src/<App>/res/` (e.g. `src/Admin/res/`). Their public location is the
`controller_resource_prefix` option:

```php
$options['controller_resource_prefix'] = 'res/';                                 // a dir under your web root (default)
$options['controller_resource_prefix'] = 'https://cdn.example.com/duckadmin/';  // absolute URL
$options['controller_resource_prefix'] = '/assets/duckadmin/';                  // absolute path
```

The web installer copies the shipped files there for you; with a CDN, copy `res/` once and point the option
at the URL. DuckAdmin never hard-codes the resource path.

## 6. Use Laravel ORM (Eloquent)

`DuckAdmin\Ext\LaravelOrmExt` replaces the admin's Model/Adapter layer with **Eloquent** models over the same
tables (`admin_admins`, `admin_roles`, `admin_permissions`, …). You do not register it yourself — it is wired
in by `DuckAdmin\Admin_LaravelModel\System\LaravelModelApp`. To use it, mount that application instead of
`AdminApp`:

```php
\DuckAdmin\Admin_LaravelModel\System\LaravelModelApp::class => [
    'controller_url_prefix' => 'admin_laravel/',
    'admin_provider_enable' => false,
],
```

`illuminate/database` is already in `require`. Do not enable two admin providers to "try both" — pick one.

## 7. Use ThinkPHP ORM

`DuckAdmin\Ext\ThinkPHPOrmExt` is the same idea with **ThinkORM**. It is wired in by
`DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp`; mount that application instead of `AdminApp`:

```php
\DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp::class => [
    'controller_url_prefix' => 'admin_thinkphp/',
    'admin_provider_enable' => false,
],
```

`topthink/think-orm` is already in `require`. Adding a third ORM means copying the pattern of one of these
two — inside a fork of DuckAdmin, not by patching `vendor/`.

## 8. Rules for AI agents working on a project that uses DuckAdmin

- **Never edit `vendor/`.** Do it in *your* project: options, a view override, a subclass, or your own
  controllers/Business layer. Upstream patches belong in the duckadmin repo.
- **Keep the layering**: `System (app entry) → Controller → Business → Model`; views are plain PHP files.
- **One admin + one user provider** at a time — do not "fix" a 302 by enabling two.
- **The coverage tooling** (`src/**/System/TestLister.php`, `.ai/cover_*.sh`, `DuckCoverage`) is development
  only and is guarded by `duckcoverage_enable`; it must never run in production.
- **`duckadmin_enable` is a security switch**: keep it off unless the backend is intentionally exposed, and
  never turn it on from code a request can reach.
- **Reinstalling**: once an application is installed, its `<prefix>/install` 302s back to the home page.
  Delete that application's data config and its database, then visit the install page again.
- After changing views or resources, re-run the application's install once if it copies resources.

## 9. Where to read more

- `README.md` in this package — full feature list, embedding recipes, secondary-development notes.
- `README.zh_CN.md` — the same in Chinese.
- `vendor/dvaknheo/duckphp/docs/` — the framework guide and the per-class API reference.
- `DuckPhp\Foundation\Helper` (`Admin()`, `AdminId()`, `AdminName()`, `User()`, …) is the supported way to
  reach the admin/user systems from your own code.
