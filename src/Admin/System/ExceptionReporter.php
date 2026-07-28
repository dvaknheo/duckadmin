<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 */
namespace DuckAdmin\Admin\System;

class ExceptionReporter
{
    public function report(\Throwable $ex): void
    {
        // 记录异常日志
        $log_file = \DuckPhp\DuckPhp::Global()->options['path'] . 'runtime/exception.log';
        $msg = '[' . date('Y-m-d H:i:s') . '] ' . get_class($ex) . ': ' . $ex->getMessage() . ' in ' . $ex->getFile() . ':' . $ex->getLine() . "\n" . $ex->getTraceAsString() . "\n";
        file_put_contents($log_file, $msg, FILE_APPEND);
    }
}
