<?php
/**
 * User List - AdminNew
 * @var array $list
 * @var int $total
 * @var int $page
 * @var int $pageSize
 * @var array $search
 * @var array $urls  (list, create, edit, delete)
 */
$title = '人员管理';
?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>人员管理</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
                <li class="breadcrumb-item active">人员管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= $urls['create'] ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> 新增人员
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= $urls['list'] ?>" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="username" class="form-control" placeholder="用户名" value="<?= __h($search['username'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="realname" class="form-control" placeholder="姓名" value="<?= __h($search['realname'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="email" class="form-control" placeholder="邮箱" value="<?= __h($search['email'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> 搜索</button>
                <a href="<?= $urls['list'] ?>" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> 清空</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>登录账号</th>
                        <th>姓名</th>
                        <th>职位</th>
                        <th>邮箱</th>
                        <th>状态</th>
                        <th>最后登录</th>
                        <th>创建时间</th>
                        <th style="width:120px">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">暂无数据</td></tr>
                    <?php else: ?>
                        <?php foreach ($list as $item): ?>
                            <tr>
                                <td><?= (int)$item['id'] ?></td>
                                <td><strong><?= __h($item['username']) ?></strong></td>
                                <td><?= __h($item['realname'] ?? '') ?></td>
                                <td><?= __h($user_roles[$item['id']] ?? '-') ?></td>
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
                                    <a href="<?= $urls['edit'] ?>?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= $urls['delete'] ?>?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-danger"
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
                    $searchParams = '';
                    foreach (['username', 'realname', 'email'] as $k) {
                        if (!empty($search[$k])) {
                            $searchParams .= '&' . $k . '=' . urlencode($search[$k]);
                        }
                    }
                    for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $urls['list'] ?>?page=<?= (int)$i ?><?= $searchParams ?>"><?= (int)$i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
