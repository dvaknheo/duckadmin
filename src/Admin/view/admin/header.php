<?php
/**
 * DuckPhp Admin System - Header
 * @var array $current_user
 * @var array $menus
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? '仪表盘' ?> - <?= __h($app_name ?? 'Admin System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<?php
foreach($css_files ?? [] as $css_file){
    echo '<link href="'.__h($css_file).'" rel="stylesheet">'."\n";
}
?>
    <style>
        :root {
            --sidebar-width: 240px;
            --topbar-height: 56px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans SC", sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--topbar-height);
            background: #001529;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        }
        .topbar .brand {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .topbar .brand i { font-size: 24px; color: #1890ff; }
        .topbar .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .topbar .user-info span { color: rgba(255,255,255,0.85); font-size: 14px; }
        .topbar .user-info a {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .topbar .user-info a:hover { color: #fff; }
        
        .sidebar {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: #001529;
            overflow-y: auto;
            z-index: 999;
            transition: transform 0.3s;
        }
        .sidebar .nav { padding: 8px 0; }
        .sidebar .nav-item { width: 100%; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 10px 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            border-radius: 0;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: #1890ff;
        }
        .sidebar .nav-link i { font-size: 16px; }
        .sidebar .nav-section {
            color: rgba(255,255,255,0.35);
            font-size: 12px;
            padding: 16px 24px 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar .nav-link .arrow {
            margin-left: auto;
            transition: transform 0.2s;
        }
        .sidebar .nav-link .arrow.open { transform: rotate(90deg); }
        .sidebar .sub-menu { display: none; padding-left: 12px; }
        .sidebar .sub-menu.open { display: block; }
        
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 24px;
            min-height: calc(100vh - var(--topbar-height));
        }
        
        .page-header { margin-bottom: 24px; }
        .page-header h4 { font-weight: 600; color: #1a1a2e; }
        .page-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 4px 0 0;
        }
        
        .card {
            border-radius: 8px;
            border: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 16px 20px;
            font-weight: 600;
        }
        .card-body { padding: 20px; }
        
        .table th {
            border-top: none;
            background: #fafafa;
            font-weight: 600;
            color: #555;
        }
        .table td { vertical-align: middle; }
        .badge-status { padding: 4px 12px; border-radius: 12px; font-weight: 500; }
        .pagination { margin-bottom: 0; }
        
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        .toast-container { position: fixed; top: 70px; right: 20px; z-index: 9999; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: block !important; }
        }
        .mobile-toggle { display: none; background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; }
    </style>
<?php
foreach($__html['style'] ?? [] as $style){
    echo '<style>' . $style . '</style>' . "\n";
}
?>
<?php
foreach($__html['script_file'] ?? [] as $js_file){
    echo '<script src="'.__h($js_file).'"></script>'."\n";
}
?>
<?php
foreach($__html['script'] ?? [] as $script){
    echo '<script>' . __h($script) . '</script>' . "\n";
}
?>

</head>
<body>
    <header class="topbar">
        <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
            <i class="bi bi-list"></i>
        </button>
        <a href="<?= __url('') ?>" class="brand">
            <i class="bi bi-shield-check"></i>
            <?= __h($app_name ?? 'Admin System') ?>
        </a>
        <div class="user-info">
            <span><i class="bi bi-person-circle"></i> <?= __h($current_user['realname'] ?? $current_user['username'] ?? '') ?></span>
            <a href="<?= __url('login/logout') ?>" onclick="return confirm('确定退出登录？')">
                <i class="bi bi-box-arrow-right"></i> 退出
            </a>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <ul class="nav flex-column">
            <?php foreach ($menus as $menu): ?>
                <?php if (!empty($menu['children'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:;" onclick="toggleSubMenu(this)">
                            <?= __h($menu['name']) ?>
                            <i class="bi bi-chevron-right arrow"></i>
                        </a>
                        <ul class="sub-menu nav flex-column">
                            <?php foreach ($menu['children'] as $child): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= __url($child['url'] ?? '#') ?>">
                                        <?= __h($child['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route ?? '') === ($menu['url'] ?? '') ? 'active' : '' ?>" 
                           href="<?= __url($menu['url'] ?? '#') ?>">
                            <?= __h($menu['name']) ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main class="main-content">
