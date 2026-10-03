<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 */
namespace DuckAdmin\Admin\System;

use DuckPhp\Core\App;

/**
 * 异常报告器
 *
 * 用法:在应用选项里设置
 *   'exception_reporter'      => [ExceptionReporter::class, 'report'],   // 必须是 is_callable 的
 *   'exception_for_project'   => ProjectException::class,
 *
 * 注意:上游 duckphp 已移除 \DuckPhp\DuckPhp::Global(),原来这里调用它会致命错误(该方法已不存在)。
 */
class ExceptionReporter
{
    public static function report(\Throwable $ex): void
    {
        $log_file = App::_()->getRuntimePath() . 'exception.log';
        $msg = '[' . date('Y-m-d H:i:s') . '] ' . get_class($ex) . ': ' . $ex->getMessage()
             . ' in ' . $ex->getFile() . ':' . $ex->getLine() . "\n" . $ex->getTraceAsString() . "\n";
        @file_put_contents($log_file, $msg, FILE_APPEND);
    }
}
