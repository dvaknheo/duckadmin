<?php
/**
 * Permission List
 */
$title = '权限和菜单管理';
$current_route = 'system';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>权限和菜单管理</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
                <li class="breadcrumb-item active">权限和菜单管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= __url('system/scan') ?>" class="btn btn-outline-success">
            <i class="bi bi-search"></i> 一键扫描
        </a>
        <a href="<?= __url('system/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 新增菜单
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= __url('system/index') ?>" class="row g-3 mb-4">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="搜索权限名称或 URL..." value="<?= __h($search) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> 搜索</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= __url('system/index') ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> 清空</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>权限名称</th>
                        <th>URL</th>
                        <th>类型</th>
                        <th>排序</th>
                        <th>创建时间</th>
                        <th style="width:120px">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">暂无数据</td></tr>
                    <?php else: ?>
                        <?php foreach ($list as $item): ?>
                            <tr>
                                <td><?= (int)$item['id'] ?></td>
                                <td>
                                    <?php if ($item['parent_id'] > 0): ?>
                                        <span class="ms-3">└─ </span>
                                    <?php endif; ?>
                                    <strong><?= __h($item['name']) ?></strong>
                                </td>
                                <td><code><?= __h($item['url'] ?? '-') ?></code></td>
                                <td><?php $types = [0 => '目录', 1 => '菜单', 2 => '操作']; echo $types[(int)($item['type'] ?? 1)] ?? '未知'; ?></td>
                                <td><?= (int)($item['weight'] ?? 0) ?></td>
                                <td class="text-muted small"><?= __h($item['created_at'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= __url('system/edit?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= __url('system/delete?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('确定删除权限「<?= __h($item['name']) ?>」？')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total > $pageSize): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php
                    $totalPages = max(1, (int)ceil($total / $pageSize));
                    $searchParam = $search !== '' ? '&search=' . urlencode($search) : '';
                    for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= __url('system/index?page=' . (int)$i . $searchParam) ?>"><?= (int)$i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

