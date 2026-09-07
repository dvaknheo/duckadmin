<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\User\System;
interface UserLoginServiceInterface
{
    public function register($post);
    public function login($post);
    public function logout();
}