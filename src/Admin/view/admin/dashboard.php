<?php
/**
 * DuckPhp Admin System - Dashboard
 */
$title = '仪表盘';
$current_route = '';
?>

<div class="page-header">
    <h4>仪表盘</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
            <li class="breadcrumb-item active">仪表盘</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5>欢迎回来，<?= __h($current_user['realname'] ?? $current_user['username'] ?? '') ?>！</h5>
                <p class="text-muted mb-0">DuckPhp 通用后台管理系统</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people" style="font-size: 36px; color: #1890ff;"></i>
                <h5 class="mt-2">用户管理</h5>
                <p class="text-muted small">管理系统用户</p>
                <a href="<?= __url('user/index') ?>" class="btn btn-outline-primary btn-sm">进入</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-shield" style="font-size: 36px; color: #52c41a;"></i>
                <h5 class="mt-2">角色管理</h5>
                <p class="text-muted small">管理角色及其权限</p>
                <a href="<?= __url('role/index') ?>" class="btn btn-outline-success btn-sm">进入</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-lock" style="font-size: 36px; color: #faad14;"></i>
                <h5 class="mt-2">权限管理</h5>
                <p class="text-muted small">管理系统权限</p>
                <a href="<?= __url('system/index') ?>" class="btn btn-outline-warning btn-sm">进入</a>
            </div>
        </div>
    </div>
</div>
