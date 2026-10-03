<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Core\App;
use DuckPhp\Core\CoreHelper;
use DuckPhp\Core\SuperGlobal;
use DuckPhp\Ext\RouteHookWebInstaller;

/**
 * 支持"强制重装"的 Web 安装器
 *
 * 框架的 RouteHookWebInstaller 只要已安装就一律 302 回首页，
 * 于是切换管理员/用户提供者之后，无法再从页面上重装(也就改不了数据库配置)。
 *
 * 这里保留原行为，只额外允许显式带 force 的请求进入安装流程：
 *   GET  /xxx/install?force=1       显示安装表单
 *   POST /xxx/install  force=1&...  先清掉 installed，再按正常流程重装
 *
 * 不带 force 时行为与框架完全一致。
 */
class WebInstaller extends RouteHookWebInstaller
{
    //@override
    public function installAction()
    {
        $installed = !empty(App::_()->options['installed']);
        if (!$installed) {
            parent::installAction();
            return;
        }

        $post = SuperGlobal::_()->_POST();
        $post = is_array($post) ? $post : [];
        $force = !empty($post['force']) || !empty(SuperGlobal::_()->_GET('force'));
        if (!$force) {
            // 保持框架原行为
            CoreHelper::Show302('');
            return;
        }

        // 强制重装：临时清掉 installed，让父类走完整安装流程
        // (doSchema 里 force 会先执行 {driver}.clean.sql 再建表)
        App::_()->options['installed'] = null;
        try {
            parent::installAction();
        } finally {
            // 安装成功时父类已写入新的 installed；
            // 中途失败(校验/连库失败)则恢复原值，保持"已安装"状态可重试
            if (empty(App::_()->options['installed'])) {
                App::_()->options['installed'] = $installed;
            }
        }
    }
}
