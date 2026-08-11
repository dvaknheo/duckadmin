<?php
/**
 * User List
 * @var array $list
 * @var int $total
 * @var int $page
 * @var int $pageSize
 * @var string $search
 */
$title = '人员管理';
$current_route = 'user';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>人员管理</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= __url('') ?>">首页</a></li>
                <li class="breadcrumb-item active">人员管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= __url('user/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 新增人员
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= __url('user/index') ?>" class="row g-3 mb-4">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="搜索人员名、姓名、邮箱..." value="<?= __h($search) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> 搜索</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= __url('user/index') ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> 清空</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>登录账号</th>
                        <th>姓名</th>
                        <th>所属职位</th>
                        <th>邮箱</th>
                        <th>状态</th>
                        <th>最后登录</th>
                        <th>创建时间</th>
                        <th style="width:160px">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">暂无数据</td></tr>
                    <?php else: ?>
                        <?php foreach ($list as $item): ?>
                            <tr>
                                <td><?= (int)$item['id'] ?></td>
                                <td><strong><?= __h($item['username']) ?></strong></td>
                                <td><?= __h($item['realname'] ?? '') ?></td>
                                <td><?= __h($item['role_names'] ?? '-') ?></td>
                                <td><?= __h($item['email'] ?? '') ?></td>
                                <td>
                                    <?php if ($item['status'] == 1): ?>
                                        <span class="badge bg-success">启用</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">禁用</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= __h($item['last_login_at'] ?? '-') ?></td>
                                <td class="text-muted small"><?= __h($item['created_at'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= __url('user/edit?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= __url('user/delete?id=' . $item['id']) ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('确定删除人员「<?= __h($item['username']) ?>」？')">
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
                            <a class="page-link" href="<?= __url('user/index?page=' . (int)$i . $searchParam) ?>"><?= (int)$i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

