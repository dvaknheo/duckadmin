<?php
/**
 * Role List - Role/
 * @var array $list
 * @var int $total
 * @var int $page
 * @var int $pageSize
 * @var string $search
 * @var array $urls (list, create, edit, delete, permissions)
 */
$title = '职位管理';
?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>职位管理</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
                <li class="breadcrumb-item active">职位管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= $urls['create'] ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 新增职位
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= $urls['list'] ?>" class="row g-3 mb-4">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="搜索职位名称..." value="<?= __h($search) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> 搜索</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= $urls['list'] ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> 清空</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>职位名称</th>
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
                                    <a href="<?= $urls['edit'] ?>?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> 编辑
                                    </a>
                                    <a href="<?= $urls['permissions'] ?>?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-shield"></i> 权限
                                    </a>
                                    <a href="<?= $urls['delete'] ?>?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('确定删除职位「<?= __h($item['name']) ?>」？')">
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
                            <a class="page-link" href="<?= $urls['list'] ?>?page=<?= (int)$i ?><?= $searchParam ?>"><?= (int)$i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
