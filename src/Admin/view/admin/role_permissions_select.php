<?php
/**
 * 分配权限 - 选择职位
 */
$title = '分配权限';
?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>分配权限</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
                <li class="breadcrumb-item"><a href="<?= __url('Role/index') ?>">职位管理</a></li>
                <li class="breadcrumb-item active">分配权限</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted">选择要分配权限的职位(仅显示你管理范围内的职位):</p>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th>职位名称</th>
                    <th>描述</th>
                    <th style="width:120px">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($roles)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">没有可管理的职位</td></tr>
                <?php else: ?>
                    <?php foreach ($roles as $item): ?>
                        <tr>
                            <td><?= (int)$item['id'] ?></td>
                            <td><strong><?= __h($item['name']) ?></strong></td>
                            <td class="text-muted"><?= __h($item['description'] ?? '') ?></td>
                            <td>
                                <a href="<?= __url('Permission/index?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-shield"></i> 分配权限
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
