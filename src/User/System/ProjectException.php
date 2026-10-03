<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\System;

/**
 * 工程异常基类
 *
 * 注意:上游 duckphp 已移除 DuckPhp\Foundation\ExceptionTrait
 * (连更早的 SimpleExceptionTrait / ThrowOnTrait 也一并删了)，现在的约定是普通类直接继承 \Exception,
 * 见 vendor/dvaknheo/duckphp/skeleton/src/System/ProjectException.php。
 * 原来这里 use 了不存在的 trait,导致本类一被加载(反射、覆盖率扫描、class_exists)就致命错误。
 */
class ProjectException extends \Exception
{
}
