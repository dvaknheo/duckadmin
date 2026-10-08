# DuckAdmin

**English** | [中文](README.zh_CN.md)

## Security (must read before deployment)

**DuckAdmin serves nothing by default.** You must explicitly turn it on in each environment's `config/DuckPhpSettings.config.php`:

```php
return [
    // ...
    'duckadmin_enable' => true,
];
```

- Without this key, or with it set to `false`: every web request is blocked and only a notice page is shown (HTTP **503**),
  telling you to enable it here. **Even the install page is blocked**, so there is no "I forgot to turn it on and someone else installed it" scenario.
- Being off by default is deliberate: this is the switch that says "I mean to expose the admin backend", and one less config item is one less risk.
- The command line is **not** exempt any more: `checkEnabled()` used to return early for CLI, and no longer does,
  so with the switch off `php bin/cli.php` (including `cover`) stops at the same notice page. Turn the switch on
  in the settings before running CLI commands.
- If the notice page is still there after your change, wait a second or two and refresh (PHP's `opcache.revalidate_freq` defaults to 2 seconds,
  and this config file gets cached); restart php-fpm if necessary.

## Introduction

You have surely seen many admin systems that are built by doing secondary development on top of a baseline project. But that does not match the thinking of `DuckPhp`.

`DuckAdmin` is a library that uses the `DuckPhp` framework; your project uses `DuckPhp`'s features, no hacky rewriting needed.

`DuckAdmin` is a baseline library for secondary development — note, a baseline *library*, not a baseline *project*.

`DuckAdmin` only provides the basic code for a company's employee structure and generic business logic. You can add more features that fit your company's business on top of it. It even comes with a coverage testing library.

## What problems does DuckAdmin solve
1. Today's admin backends are customized by changing code in someone else's admin backend, but what makes DuckAdmin stand out is that it is a baseline library, not a baseline project
2. Static resources and code are separated, so they can be deployed on different machines
3. This composer library includes not only DuckAdmin but also four independent projects — DuckUser, DuckUserManager, SimpleBlog — plus a standalone demo project, DuckAdminDemo.
  1. DuckUser is a user baseline library, and you can modify it from the outside in the same way as DuckAdmin
  2. DuckUserManger is the user management system
  3. SimpleBlog SimpleBlog demonstrates a blog system built on the management system and user system above.
  4. It even includes a coverage testing library. DuckCoverage, for doing coverage testing

## Demo

Extra requirement: sqlite

`DuckAdmin` supports mysql too; you only need to change the config, as demonstrated later

```
composer require dvaknheo/duckadmin
cd ./vendor/dvaknheo/duckadmin/demo
php cli.php run
```

Visit `http://127.0.0.1:8080/` to open the Demo

### Reinstalling

In the demo

Your data is saved in `vendor/dvaknheo/duckadmin/demo/runtime/database.db`

The dynamic config file is saved in `vendor/dvaknheo/duckadmin/demo/config/DuckPhpApps.config.php`

Delete these two, and you can run the installer below again.

```
php cli.php install ## --force forces it --help shows the parameters
```

### Reinstalling via the Web installer (changing the database config)

Once the sub-application is installed, visiting `<prefix>/install` directly will 302 back to the home page (framework behavior, to stop accidental visits to the install page).
There is **no** `force=1` escape hatch any more: `DuckAdmin\System\WebInstaller` was removed and the
framework's own installer class is used, so the only way back is to make the application "not installed" again.

To reinstall (for example to switch the database file or the admin/user provider): delete that application's data
config (`runtime/DuckPhpData*.config.json`) and its database file, then visit `<prefix>/install` again. (In the demo
the CLI installer `php cli.php install --force` also works.)

Admin / user providers are switched with the radio buttons on the root application's home page (written to `runtime/DuckPhpData*.config.json`);
after switching, run the reinstall above once for the corresponding sub-application, so its database config is initialized again.

## Normal usage modes
### Mode one: a quick duckphp project plus application.
When you want to copy the demo example and do nothing else, run the following commands

```
composer require dvaknheo/duckadmin
composer exec duckphp new
php cli.php require DuckAdmin/System/DuckAdminApp
# press Enter all the way
#php cli.php require DuckUser/System/DuckUserApp
#php cli.php require SimpleBlog/System/SimpleBlogApp
#php cli.php require DuckUserMangager/System/DuckUserMangagerApp

php cli.php run

```
Visit http://127.0.0.1:8080/duck-admin/ to open the admin backend.


### Mode two: embedding into a standalone project

Take this route when you want to use it in an existing php-fpm project that has no admin backend.
A complete runnable example is in the section "How code from other non-DuckPhp frameworks integrates DuckAdmin's admin backend system" below
(it used to live in `public/main.php`, and that file has been deleted).

 `composer require dvaknheo/duckadmin`

The core feature code is as follows
```
<?php
//We demonstrate embedding duckadmin in another framework system
$options=[
    'path' => __DIR__.'/../', // specify the path
    'skip_404' => true,      // skip the internal 404 handling.
    'app' => [
        DuckAdminApp::class => [      // the admin backend system
            'controller_url_prefix' => 'app/admin/',    // the access path
            'installed' => '2024-06-10T22:12:01+08:00', // the installed flag
            'database_list' =>  //specify the database
            [
                'dsn' => 'sqlite:demodb.db',
                'username' => '',
                'password' => '',
            ],
        ],
    ],    
];

$flag = DuckPhp::RunQuickly($options);
if ($flag) {
    return; // the admin backend system is done
}

//////////////////////////

// you code here.
$url_admin = __url('app/admin/index'); // the admin address, the same as the controller_url_prefix you set

$admin_id = Helper::AdminId(false);     // the admin ID
$admin_name = $admin_id ? Helper::AdminName() : ''; // the admin name
$url_logout = Helper::Admin()->urlForLogout();

```
As you can see, there is an extra skip_404 option. When a 404 is returned, handle your other business yourself
The admin_id you get here is the id you get after logging in.


### Mode three: manually modifying a duckphp project to embed it

Zero code is comfortable, but it is not the final solution

Once you are familiar with the duckphp project structure.

Modify the relevant config of your System/App.php in your duckphp project

```php
    public $options = [
        //...

        'app' => [
                \DuckAdmin\System\DuckAdminApp::class => [      // the admin backend system
                    'controller_url_prefix' => 'app/admin/',    // the access path
                    // 'database_driver' =>'mysql',  // if you want to switch to the mysql driver
                    //... other config
                    
                ],
            ],
        ];
```

Run
```
php cli.php run
```
Visit http://127.0.0.1:8080/app/admin/index to open the admin backend.

This is only the DuckAdmin backend; it does not include the user system, user management or blog example from the demo


## Secondary development

### Self-checks after an upstream upgrade (important)

`DuckPhp`'s upstream interface changed signatures (for example `canAccess()` now takes `$url` as the second parameter),
and quite a few classes in this project **do not write `implements`** (they rely on the `local_service` / `login_service` /
`login_session` bindings in the App options to become upstream services), so PHP will not check it for us — such problems only blow up when execution reaches that line.

After an upstream update, run these two scripts:

```
php .ai/audit_signatures.php      # contract signature audit: compare this project's implementations with the upstream interfaces parameter by parameter
php .ai/audit_upstream_refs.php   # upstream reference audit: do the classes/methods referenced in src still exist
```

Both report their result through the exit code (0 pass / 1 problem), so you can hang them directly on CI or a git hook.

- The **role table** in `audit_signatures.php` (which class takes on which contract) needs to be maintained in sync when you add a sub-application or change a binding.
- "Loosening" a parameter type (`?string` against the interface's `string`) is allowed by LSP and reports a warn rather than a fail.
- The coverage DSL (`CALL Class@method k=v`) passes parameters **by parameter name**, so parameter names must match the contract too.



### Prerequisites

Some basic knowledge of the duckphp framework is not repeated here

### Adjusting options

DuckAdmin supports two database modes, sqlite / mysql; you can switch `database_driver` in the options

In a few versions we hope to be able to switch it in the installer (for now it can only be switched manually

### Static resources

In the native era, we just unzipped the files into the web directory.

But we want to separate dynamic and static resources. To ease the load on the server.

So we have the `controller_resource_prefix` option.

So where are the resource files? Under the `src/DuckAdmin/res` directory.

You can set this option to a full http path, instead of using the default

You can also set it to an absolute path starting with /. The default is 'res/', and the installer will copy them to the corresponding directory.

Key points for installing the resource files

Because it involves installation, we set `controller_resource_prefix`, the necessity of cloning the files

### Taking over views

The `demo/view/DuckAdmin` directory is the demo project's adjusted views

### Using the API

In your project

Use `DuckPhp\Foundation\Helper::Admin()` to get the Admin object,
`AdminId($check_login)` , `AdminName($check_login)` get the current Id and the admin name
See the DuckPhp documentation for details, the class introduction of `DuckPhp\GlobalAdmin\GlobalAdmin`.

Use `DuckPhp\Foundation\Helper::User()` to get the generic User object,
See the DuckPhp documentation for details, the class introduction of `DuckPhp\GlobalUser\GlobalUser`.

### Modifying the existing implementation

Basic knowledge of the duckphp framework is not explained again.

### Advanced

Menus and permissions. How to add a menu in my controller. See the implementation of the user management system in the demo.


### Coverage testing

Depends on xdebug's coverage mode (must be on for both CLI and FPM): `xdebug.mode=develop,coverage`.
The switch and the site base address are in `config/DuckPhpSettings.config.php.sample`.

**Archiving by provider group** (only one admin provider + one user provider can be in effect at a time, so one provider is one group):

```bash
# Recommended: --flag mode. The provider is chosen by a collection flag (request header), no disk config is touched, no race conditions
bash .ai/cover_flags.sh                # run all groups and merge the reports
bash .ai/cover_flags.sh e_user f_blog  # run only the specified groups
REPORT_ARGS="--jsonl=runtime/DuckCoverage/report.jsonl --jsonl-no-timestamp" \
    bash .ai/cover_flags.sh            # also produce a machine-readable report.jsonl

# Old mode (kept for rollback/comparison): each group chooses its provider by "POSTing to the home page to rewrite this group's config"
bash .ai/cover_groups.sh
```

The reports are in `runtime/DuckCoverage/AAAAA.report/` (multiple groups merged) or `<group name>.report/` (single group);
next to each report there is a `report.json` from the same source.

`--flag` mode is `cover --watch <group name> --flag=<flag>` → `cover --play --flag=<flag>` → `cover --report group...`.
The flag value is an **application's `controller_url_prefix`** without the trailing slash:
`admin` / `admin_laravel` / `admin_thinkphp` / `single_admin` / `demo_users` / `user` / `blog`.
`System\TestLister` matches the flag against `options['app'][*]['controller_url_prefix']` and plays exactly that
application's child list; for `user` / `blog` it first calls `toDefaultAdmin()` / `toDefaultUser()`, which write the
dynamic provider config (`saveProviders()`) — the same path the admin homepage form uses. No flag, or a flag that
matches nothing, falls back to the full list (all seven applications in one play).
**Note**: the old "the flag only takes effect when `duckcoverage_enable` is true" lock disappeared together with
`applyProviderFlag()`; the flag no longer influences authentication, it only selects which list to play.
**Do not use `cover --go`**, its behavior differs from doing the steps manually.

#### Testing one child application on its own

`--flag` takes an **application's `controller_url_prefix`** (without the trailing slash), and
`System\TestLister` then plays only that application's child list:

```bash
bash .ai/cover_one.sh blog          # only SimpleBlog (group name defaults to one_blog)
bash .ai/cover_one.sh user e_solo   # only User, with a custom group name
```

Equivalent to the steps by hand:

```bash
php bin/cli.php cover --watch <group> --flag=<prefix>
php bin/cli.php cover --play  --flag=<prefix>
php bin/cli.php cover --stop
php bin/cli.php cover --report <group>
```

The prefixes are `admin` / `admin_laravel` / `admin_thinkphp` / `single_admin` / `demo_users` / `user` / `blog`.
The play log shows what actually ran: every `#INCLUDE_CHILD` expands into a `COMMENT APP <class>` line,
so a single-application run has exactly one.

Two things bite: do not put a trailing slash on the flag (`--flag=blog/` matches nothing and silently
falls back to the full list), and only the `user` / `blog` flags write the provider config for you
(`toDefaultAdmin()` = SingleAdmin, `toDefaultUser()` = DemoUsers) — the three `admin*` flags do not, so
switch the admin provider to that application in the home page form first; otherwise `Admin::_()` points
at another system and the numbers mislead you.

#### There is only one way to count lines, and "signature lines" have been removed upstream

- `runtime/DuckCoverage/*.report/index.html`, the `report.json` in the same directory, and
  the `report.jsonl` produced by `--jsonl=` are the same data (after this upstream patch all three agree).
- `php .ai/coverage_gaps.php` — reads `report.jsonl` and lists "lines still worth covering",
  sorted by `todo` (= `unc - sig`) by default; it also lists separately the files that are "already at 100%, but had signature lines removed".

**Signature lines (a historical problem, now solved upstream)**: when a method declaration's parameters have default values (especially
array defaults, for example `public function log($user_id, string $string, ?string $type = null, array $ext = [])`),
that declaration line is counted as an executable line by static analysis, but the coverage driver will never record it as executed —
`src/DemoUsers/Business/UserBusiness.php` therefore long showed a fixed 29/30.

Upstream has now **removed such lines from the counting** (an `unset` in the data layer, consistent across
HTML / `report.json` / `report.jsonl`), so:

- that file is now **29/29 = 100%**, and `unc` is empty;
- the removed lines leave a trace in the `sig` field (`sig` on the `report.json` side, also `sig` on the `jsonl` side);
- to decide "is there anything left to cover", just look at whether `todo` (or `unc`) is empty, **no need to subtract `sig` any more**.

> When an older `report.jsonl` has no `todo` field, `.ai/coverage_gaps.php` automatically degrades to
> `unc - sig`, so the tool works with both generations of output.

#### Ways to add coverage

1. Add `WEB` steps for anything expressible over HTTP. Write uris "relative to the application" (the framework adds `controller_url_prefix`),
   and use absolute uris only across applications.
2. For things HTTP cannot express (pure business/model methods, exception branches) use macros, which expand into
   `CALL <phase>!<namespace>\<layer>\<class>@<method> parameters`:
   - `#BUSINESS <class>@<method> parameters` → `Business\<class>` of the current application
   - `#MODEL <class>@<method> parameters` → `Model\<class>`
   - `#ACTION <class>@<method> parameters` → `Controller\<class>`
   - `#INCLUDE_CALL <call>` → expands directly into `CALL`

   ⚠️ A `CALL` that throws an exception aborts the whole play: to hit error branches use `WEB` (an HTTP request will not abort the play),
   or wrap each step in its own try like `moreTest2()` in `src/DemoUsers/System/TestLister.php`.

> **These numbers are stale.** They were measured before the test-list refactor (seven applications,
> `PHASE` lines written by the listers themselves, `--flag` = an application prefix), and the group table in
> `.ai/cover_flags.sh` still carries the old short-name flags — a full run today therefore plays the full list
> for every group. Re-measure before quoting them.

Current progress (6 groups, 532 HTTP requests, 0 application error logs): Total 77.92%
(lines 2819/3618, functions 249/380 = 65.53%) —
Admin 77.88%, Admin_LaravelModel 63.38%, Admin_ThinkPHPModel 63.78%, DemoUsers 100.00%,
SimpleBlog 98.33%, SingleAdmin 100.00%, System 30.20%, User 90.77%.

How to read these percentages (all three movements have a reason — do not misread them):
- User 45.03% → 90.77%: the User application got its own test list;
- SimpleBlog 90.20% → 98.33%: fixed the real culprit — when the group name could not be resolved the
  lists built `runtime/DuckCoverage/.db`, so "install" went into one database while "register/login"
  went into another and the whole user side looked uncovered (that resolver, `src/System/CoverageGroup.php`, has since been deleted:
    upstream now guards the group name, and the DB name moved into `DuckAdminApp::getTestDbFile()`);
- System 65.15% → 30.20%: this module is only 3 files; `DuckAdminApp.php` gained the `--flag`
  provider-switching logic and the coverage-only helper only runs on the CLI side (the web collector never
  sees it), so the numerator barely moved while the denominator grew — this is not a code regression.

Commands: `bash .ai/cover_flags.sh` (run every group and merge the reports), `php .ai/coverage_gaps.php` (list the lines still worth covering).

### Multilingual (installer wizard and other texts)

The texts go through DuckPhp's Lang component, and each sub-application's language files live in its own `config/`:

```
src/Admin/config/lang-zh_CN.php        # Admin (shared by Admin_LaravelModel / Admin_ThinkPHPModel)
src/Admin/config/lang-en_US.php
src/User/config/lang-zh_CN.php         src/User/config/lang-en_US.php
src/SimpleBlog/config/lang-zh_CN.php   src/SimpleBlog/config/lang-en_US.php
```

- The default language is the root application `DuckAdminApp`'s `lang_default` (`en-US`); sub-applications' Lang
  follows the root application through `lang_follow_root`, so you only set it in one place, the root application.
- How to switch: `?lang=en-US`, the `lang` cookie, or the browser's `Accept-Language`.
  - **The file-name shape matters**: browsers send `en-US` (hyphen); the framework normalises it to `en_US` and then looks for `lang-en_US.php`. There is **no** "strip the region and retry" fallback — `en-GB` will not hit `en_US`, and a bare `en` only matches `lang-en.php`.
- **The main language file wins over frag** (`Lang`'s comment: the main language file always wins,
  a frag only fills in the missing keys) — to change the install page texts, change `lang-*.php`,
  do not create a separate `lang-*-for_webinstaller.php`, or your change will not take effect.
- Keys are flat (a dot is just part of the name); a missing key falls back to the framework's built-in English and is not shown as a bare key.
- The installer's keys are `webinstaller.*`; Admin additionally has the custom fields
  `Admin Name` / `Admin Password` / `Admin Password Confirm`.

### How code from other non-DuckPhp frameworks integrates DuckAdmin's admin backend system

DuckAdmin is a **library** and can be embedded into any framework (Laravel / ThinkPHP / an in-house framework all work): call
`RunQuickly()` once; it returns `true` to mean this request has been fully handled by the admin backend, so just `return` and hand
control back; if it returns `false`, keep running your own code.

(This code used to be `public/main.php` as a runnable demonstration; that file has been deleted, and the example is kept here.)

```php
<?php
use DuckPhp\DuckPhp;
use DuckAdmin\System\DuckAdminApp;
use DuckPhp\Foundation\Helper;

$options = [
    'path'     => __DIR__ . '/../',   // DuckAdmin's root directory
    'skip_404' => true,               // routes not caught are handed back to your own framework
    'app' => [
        DuckAdminApp::class => [
            'controller_url_prefix' => 'app/admin/',          // the backend's access path
            'installed'   => '2024-06-10T22:12:01+08:00',     // the installed flag
            'database_list' => [
                'dsn'      => 'sqlite:demodb.db',
                'username' => '',
                'password' => '',
            ],
        ],
    ],
];

$flag = DuckPhp::RunQuickly($options);
if ($flag) {
    return;   // the admin backend is done, this request ends here
}

//////////////////////////
// your own code starts here

$url_admin  = __url('app/admin/index');   // the admin address (corresponding to controller_url_prefix above)
$admin_id   = Helper::AdminId(false);     // the admin ID; false when not logged in
$admin_name = $admin_id ? Helper::AdminName() : '';
$url_logout = Helper::Admin()->urlForLogout();

// ... render your own page, the variables above can be used directly in the view
```

A few notes:

- **autoload**: when installed as a library, what you `require` is your project's `vendor/autoload.php`.
  The original `main.php` also had a fallback that "searches up four levels if not found"; with a normal composer install you do not need it.
- **`path`**: points at DuckAdmin's root directory — it uses that to find `config/`, `view/`, `runtime/`.
- **`skip_404 => true`**: routes the backend did not catch are given back to you, so it does not take over the whole site's 404s.
- **`installed` + `database_list`**: make the library's application run "as if it were installed"; in a real project fill in your own
  database, or you can keep only `controller_url_prefix` and let it go through the Web installer once (see "Demo" above).
- **Login state**: `Helper::AdminId()` / `AdminName()` / `Helper::Admin()->urlForLogout()`
  are for your pages; for view overrides and language overrides see the "Taking over views" and "Multilingual" sections above.
- **Security gate**: when embedded in another framework it is still bound by `duckadmin_enable` — if it is not on, only the notice page is output
  (see the "Security" section at the beginning).

### Login event management
TBD



## Repository layout

```
.
├── public/                  The only entry point
│   └── index.php            Boots the root application
├── src/                     Code: one root application + 7 child applications
│   ├── System/              Root app: DuckAdminApp, main controller, root TestLister, WebInstaller
│   ├── Admin/               Admin backend (database-backed; the default admin provider)
│   ├── Admin_LaravelModel/  Same, but the Model layer is overridden with Eloquent
│   ├── Admin_ThinkPHPModel/ Same, but the Model layer is overridden with ThinkORM
│   ├── SingleAdmin/         Single-admin provider (no database)
│   ├── DemoUsers/           Demo user provider (preset accounts, no database)
│   ├── User/                User app: front-end register/login + user management
│   └── SimpleBlog/          Sample business app (blog) showing how to build on the baseline
├── view/                    Views of the root application (home page: pick the providers)
│   └── main.php
├── config/                  Per-environment config + root-app sentences
│   ├── DuckPhpSettings.config.php.sample   Copy to .config.php and edit per environment
│   ├── lang-zh_CN.php
│   └── lang-en_US.php
├── bin/                     Command-line entry (only one)
│   └── cli.php              Command entry (run / call / cover ... provided by duckphp)
├── .ai/                     Scripts used by AI tooling (not part of the app runtime)
│   ├── cover_flags.sh       Coverage runs: --flag mode (recommended)
│   ├── cover_groups.sh      Coverage runs: legacy mode (kept for rollback/comparison)
│   ├── cover_one.sh         Coverage runs: a single child application (flag = its url prefix)
│   ├── coverage_gaps.php    Reads report.jsonl and lists lines still worth covering
│   ├── provider_probe.php   Shows which pair of providers a coverage flag resolves to
│   ├── audit_signatures.php Contract-signature audit (parameter-by-parameter vs upstream)
│   └── audit_upstream_refs.php Upstream-reference audit (do referenced classes/methods still exist)
├── runtime/                 Runtime data: databases, coverage dumps and reports (not in git)
├── database/                sqlite and other data files
├── sync_to_wsl.sh           Syncs the code to the WSL test machine (excludes runtime/vendor/env config)
└── README.md / README.zh_CN.md
```

Every child application has the same one-way layering (`System → Controller → Business → Model`):

```
src/<app>/
├── System/        Application entry class + that app's TestLister (coverage test list)
├── Controller/    Controllers
├── Business/      Business layer
├── Model/         Data layer
├── view/          Views
└── config/        Language packs and other config for that app
```

There is exactly one entry point: `public/index.php` boots the root application
`src/System/DuckAdminApp.php`, which mounts the 7 child applications under their own
`controller_url_prefix` (`/admin/`, `/user/`, `/blog/`, ...). So "adding a child application"
means dropping a directory into `src/` and adding one line to the root app's `app` option —
not creating a new entry point.


## TODO


Missing a validor validation system; using thinkphp's should have worked, but it is too complex, and later versions became more specialized

Missing a form generator. This is actually not important.
