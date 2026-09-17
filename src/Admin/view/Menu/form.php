<?php
/**
 * Menu Form (Create / Edit) - Menu/
 * @var array $perm (edit mode)
 * @var array $input (create mode with validation errors)
 * @var array $perm_tree 权限树(供上级选择,仅目录可选)
 * @var string $error
 * @var array $urls (save/update, list)
 * @var bool $is_edit
 * @var string $show ('all'|'menu')
 */
$data = $is_edit ? $perm : ($input ?? []);
$title = $is_edit ? '编辑权限' : '创建权限';
$form_url = $is_edit ? ($urls['update'] ?? '') : ($urls['save'] ?? '');

if (!function_exists('renderParentOptions')) {
    /**
     * 递归渲染上级选项(仅 type=0 目录可选;排除被编辑节点自身及其子树防循环)
     */
    function renderParentOptions($nodes, $depth, $selectedId, $excludeId = 0) {
        foreach ($nodes as $n) {
            if ((int)$n['type'] !== 0) continue;
            if ((int)$n['id'] === $excludeId) continue; // 跳过自身(子树一并跳过)
            $sel = ((int)$selectedId === (int)$n['id']) ? ' selected' : '';
            echo '<option value="' . (int)$n['id'] . '"' . $sel . '>'
                . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth) . ($depth > 0 ? '├─ ' : '')
                . htmlspecialchars($n['name']) . '</option>';
            renderParentOptions($n['children'] ?? [], $depth + 1, $selectedId, $excludeId);
        }
    }
}
?>
<div class="page-header">
    <h4><?= __h($title) ?></h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $urls['list'] ?>">首页</a></li>
            <li class="breadcrumb-item active"><?= __h($title) ?></li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= __h($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= $form_url ?>">
            <input type="hidden" name="show" value="<?= __h($show ?? 'all') ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">权限名称 <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                       value="<?= __h($data['name'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">URL</label>
                <input type="text" name="url" class="form-control"
                       value="<?= __h($data['url'] ?? '') ?>"
                       placeholder="如: user/index(目录类型留空)">
                <small class="text-muted">菜单/操作填写,与请求地址精确匹配;目录留空</small>
            </div>

            <div class="mb-3">
                <label class="form-label">类型</label>
                <select name="type" class="form-select">
                    <?php $types = [0 => '目录', 1 => '菜单', 2 => '操作']; ?>
                    <?php foreach ($types as $tv => $tn): ?>
                        <option value="<?= $tv ?>" <?= ((int)($data['type'] ?? 1) === $tv) ? 'selected' : '' ?>>
                            <?= $tn ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">上级</label>
                <select name="parent_id" class="form-select">
                    <option value="0">顶级</option>
                    <?php renderParentOptions($perm_tree ?? [], 0, $data['parent_id'] ?? 0, $is_edit ? (int)$data['id'] : 0); ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">排序</label>
                <input type="number" name="weight" class="form-control"
                       value="<?= (int)($data['weight'] ?? 0) ?>">
            </div>

            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?= $is_edit ? '保存修改' : '创建权限' ?>
                </button>
                <a href="<?= $urls['list'] ?>" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
