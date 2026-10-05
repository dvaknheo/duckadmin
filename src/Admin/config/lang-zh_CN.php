<?php declare(strict_types=1);
/**
 * DuckAdmin\Admin - 中文句子（与 lang-en_US.php 同一套 key）。
 *
 * key 是扁平的：点号只是名字的一部分，Lang 不做路径查找。
 *
 * 语言默认 zh_CN（根应用的 lang_default），也可用 ?lang=en-US、lang cookie
 * 或 Accept-Language 切换。本文件缺的 key 会回落到框架内置英文。
 *
 * 文件里的 webinstaller.* 是安装向导的 key（见 view/install.php 与
 * view/install_custom.php），由 Admin_LaravelModel / Admin_ThinkPHPModel
 * 共用（这两个应用继承 AdminApp，路径同为 src/Admin/）。
 *
 * 注意：**主语言文件优先于 frag**（Lang 的注释：the main language file always
 * wins, a frag only fills in the missing keys），所以安装页要改文案就改这里，
 * 不要再另建 lang-zh_CN-for_webinstaller.php。
 */
return [
    'webinstaller.h1'                     => '后台管理系统',
    'webinstaller.install_complete'       => '安装完成',
    'webinstaller.congratulations'        => '恭喜，应用已安装成功。',
    'webinstaller.install_redirect'       => '安装成功，页面将在 5 秒后自动跳转到首页。',
    'webinstaller.manual_redirect'        => '立即前往首页',
    'webinstaller.env_check'              => '环境检查',
    'webinstaller.current_controller_prefix' => '当前 controller_resource_prefix',
    'webinstaller.item'                   => '项目',
    'webinstaller.status'                 => '状态',
    'webinstaller.redis_config'           => 'Redis 配置',
    'webinstaller.follow_main_application' => '跟随主应用',
    'webinstaller.no_redis_in_root'       => '主应用未配置 Redis。',
    'webinstaller.no_database_in_root'    => '主应用未配置数据库。',
    'webinstaller.host'                   => '主机',
    'webinstaller.port'                   => '端口',
    'webinstaller.auth'                   => '认证密码',
    'webinstaller.select'                 => '库编号',
    'webinstaller.multi_redis_hint'       => '支持多个 Redis 配置：安装完成后在配置文件中手动添加更多条目。',
    'webinstaller.database_config'        => '数据库配置',
    'webinstaller.driver'                 => '驱动',
    'webinstaller.file'                   => '文件',
    'webinstaller.dbname'                 => '数据库名',
    'webinstaller.username'               => '用户名',
    'webinstaller.password'               => '密码',
    'webinstaller.multi_db_hint'          => '支持多个数据库配置（主/从）：安装完成后在配置文件中手动添加更多条目。',
    'webinstaller.force_reinstall'        => '强制重装（会删除已有数据表）',
    'webinstaller.customer_setting'       => '自定义设置',
    'webinstaller.install'                => '安装',

    // Admin 安装器的自定义字段（view/install_custom.php 用的 key）
    'Admin Name'                          => '管理员账号',
    'Admin Password'                      => '管理员密码',
    'Admin Password Confirm'              => '确认密码',
];
