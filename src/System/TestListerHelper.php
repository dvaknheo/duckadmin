<?php declare(strict_types=1);
/**
 * DuckAdmin - 覆盖测试「清单片段」的通用取法
 *
 * 向**当前正在服务的**管理员系统 / 用户系统要三类清单之一：
 *   TestList（全量，默认） / TestListForLogin（只登录） / TestListForLogout（只登出）
 *
 * 不依赖任何具体提供者类名，也不依赖 phase 名常量 —— 依据是实测出来的两条框架行为：
 *   1. \DuckPhp\GlobalAdmin\Admin::_()->phase() 与 \DuckPhp\GlobalUser\User::_()->phase()
 *      会给出"当前这套系统所在的 phase"，并且**随本次 --flag 切换而变**
 *      （--flag=admin+user 时是 :DuckAdmin/:DuckUser；--flag=single+demo 时是 :SingleAdmin/:DemoUsers）；
 *   2. App::Phase($x) 在**任何地方**都能切换 App::_()（包括在别的应用的 lister 里），
 *      切回来也干净。
 * 于是"谁在服务就问谁要清单"：宿主应用（DuckAdmin 之外的应用也一样）不需要知道对方是谁。
 *
 * 注意：切换管理员/用户系统**不是**这里的事 —— 那是 DuckAdmin 根应用按 flag 干的
 * （DuckAdminApp::applyProviderFlag()）。这里只负责"问当前那套系统要清单"。
 */
namespace DuckAdmin\System;

use DuckPhp\Core\App;

//@codeCoverageIgnoreStart
final class TestListerHelper
{
    /**
     * 取某一侧的清单片段，**已展开 phase 行**，可直接内联进调用方的清单。
     *
     * @param string $side    'admin' 或 'user'
     * @param string $variant ''（全量） | 'login' | 'logout'
     */
    public static function sideList(string $side, string $variant = ''): string
    {
        $service = ($side === 'admin')
            ? \DuckPhp\GlobalAdmin\Admin::_()
            : \DuckPhp\GlobalUser\User::_();
        $phase = (string)$service->phase();

        $last = App::Phase();
        App::Phase($phase);                     // 切到那套系统自己的 phase
        if ($variant !== '') {
            App::Root()->options['duckcoverage_test_lister_parameter'] = $variant;
        }
        $lister = App::_()->options['duckcoverage_test_lister'] ?? null;
        $body = $lister ? (string)($lister)() : '';
        // 必须在**对方 phase** 里展开：#PHASE_BEGIN → PHASE <对方>，#PHASE_END → PHASE <调用方>
        $out = \DuckCoverage\TestListerHelper::_()->explainMarco($body);
        unset(App::Root()->options['duckcoverage_test_lister_parameter']);   // 兜底再清一次
        App::Phase($last);

        return "COMMENT {$side} system @ {$phase}\n" . $out;
    }

    /** 管理员系统的登录清单（常用简写） */
    public static function adminLogin(): string
    {
        return static::sideList('admin', 'login');
    }

    /** 用户系统的登录清单（常用简写） */
    public static function userLogin(): string
    {
        return static::sideList('user', 'login');
    }
}
//@codeCoverageIgnoreEnd
