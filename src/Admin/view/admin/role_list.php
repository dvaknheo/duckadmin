<?php
/**
 * Role List
 */
$title = '色色管理';
$current_route = 'role';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>色色管理</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
                <li class="breadcrumb-item active">色色管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= __url('role/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 新增色色
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= __url('role/index') ?>" class="row g-3 mb-4">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="搜索色色名称..." value="<?= __h($search) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> 搜索</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= __url('role/index') ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> 清空</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>色色名称</th>
                        <th>描述</th>
                        <th>创建时间</th>
                        <th style="width:200px">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">暂无数据</td></tr>
                    <?php else: ?>
                        <?php foreach ($list as $item): ?>
                            <tr>
                                <td><?= (int)$item['id'] ?></td>
                                <td><strong><?= __h($item['name']) ?></strong></td>
                                <td class="text-muted"><?= __h($item['description'] ?? '-') ?></td>
                                <td class="text-muted small"><?= __h($item['created_at'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= __url('role/edit?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> 编辑
                                    </a>
                                    <a href="<?= __url('role/permissions?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-shield"></i> 权限
                                    </a>
                                    <a href="<?= __url('role/delete?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('确定删除色色「<?= __h($item['name']) ?>」？')">
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
                            <a class="page-link" href="<?= __url('role/index?page=' . (int)$i . $searchParam) ?>"><?= (int)$i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

