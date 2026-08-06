<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 */
namespace DuckAdmin\Admin\Business;

use DuckPhp\Component\ExtOptionsLoader;
use DuckPhp\Helper\BusinessHelperTrait;

class Helper
{
    use BusinessHelperTrait;

    /**
     * 保存扩展选项（如 DuckAdmin 的 installed 标记）到 runtime/DuckPhpData.config.json
     * 按当前 phase（DuckAdmin）存储，由 ExtOptionsLoader bump 到 DuckAdminApp options
     *
     * @param array<string, mixed> $options
     */
    public function saveOptions(array $options): void
    {
        ExtOptionsLoader::_()->saveData($options);
    }
}
