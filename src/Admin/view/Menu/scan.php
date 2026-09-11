<?php
/**
 * Menu Scan - Menu/
 * @var array $added
 * @var array $urls (list)
 */
$title = '一键扫描结果';
?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>一键扫描结果</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
                <li class="breadcrumb-item active">一键扫描</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= $urls['list'] ?>" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($added)): ?>
            <p class="text-success mb-0">扫描完成,没有新增权限/菜单(所有路由均已入库)。</p>
        <?php else: ?>
            <p>扫描完成,新增 <?= count($added) ?> 条权限/菜单:</p>
            <ul>
                <?php foreach ($added as $url): ?>
                    <li><code><?= __h($url) ?></code></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
