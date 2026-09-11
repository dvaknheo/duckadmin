<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin Business
 * 人员管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminModel;

class AdminBusiness extends Base
{
    /**
     * 获取人员列表
     */
    public function getList(int $page, int $pageSize, array $search = []): array
    {
        return AdminModel::_()->getPageList($page, $pageSize, $search);
    }

    /**
     * 创建用户
     */
    public function create(array $input): array
    {
        if (empty($input['username'])) {
            return ['success' => false, 'message' => '用户名不能为空'];
        }
        if (empty($input['password'])) {
            return ['success' => false, 'message' => '密码不能为空'];
        }
        if (strlen($input['password']) < 6) {
            return ['success' => false, 'message' => '密码长度至少6位'];
        }

        $existing = AdminModel::_()->getByUsername($input['username']);
        if ($existing) {
            return ['success' => false, 'message' => '用户名已存在'];
        }

        AdminModel::_()->create($input);

        return ['success' => true, 'message' => '创建成功'];
    }

    /**
     * 更新用户
     */
    public function update(int $id, array $input): array
    {
        if (empty($input['username'])) {
            return ['success' => false, 'message' => '用户名不能为空'];
        }

        AdminModel::_()->edit($id, $input);

        return ['success' => true, 'message' => '更新成功'];
    }

    /**
     * 删除用户
     */
    public function delete(int $id): bool
    {
        return AdminModel::_()->delete($id);
    }

    /**
     * 获取单个用户
     */
    public function getById(int $id): ?array
    {
        return AdminModel::_()->getById($id);
    }
}
