<?php
namespace DuckAdmin\Business;
use DuckAdmin\Model\OptionModel;

/**
 * 个人资料业务
 */
class ConfigBusiness extends BaseBusiness 
{
    public function getDefaultConfig()
    {
        $config = OptionModel::G()->GetSystemConfig(); 
        if (empty($config)) {
            $config = static::Config(null,[],'pear_config');
            OptionModel::G()->setSystemConfig($config);
        }
        return $config;
    }
    public function updateConfig($post)
    {
        $config = $this->getDefaultConfig();
        $data = [];
        foreach ($post as $section => $items) {
            if (!isset($config[$section])) {
                continue;
            }
            switch ($section) {
                case 'logo':
                    $data[$section]['title'] = htmlspecialchars($items['title'] ?? '');
                    $data[$section]['image'] = static::filterUrlPath($items['image'] ?? '');
                    break;
                case 'menu':
                    $data[$section]['data'] = static::filterUrlPath($items['data'] ?? '');
                    $data[$section]['accordion'] = !empty($items['accordion']);
                    $data[$section]['collapse'] = !empty($items['collapse']);
                    $data[$section]['control'] = !empty($items['control']);
                    $data[$section]['controlWidth'] = (int)$items['controlWidth'] ?? 500;
                    $data[$section]['select'] = (int)$items['select'] ?? 0;
                    $data[$section]['async'] = true;
                    break;
                case 'tab':
                    $data[$section]['enable'] = true;
                    $data[$section]['keepState'] = !empty($items['keepState']);
                    $data[$section]['preload'] = !empty($items['preload']);
                    $data[$section]['session'] = !empty($items['session']);
                    $data[$section]['max'] = static::filterNum($items['max'] ?? '30');
                    $data[$section]['index']['id'] = static::filterNum($items['index']['id'] ?? '0');
                    $data[$section]['index']['href'] = static::filterUrlPath($items['index']['href'] ?? '');
                    $data[$section]['index']['title'] = htmlspecialchars($items['index']['title'] ?? '首页');
                    break;
                case 'theme':
                    $data[$section]['defaultColor'] = static::filterNum($items['defaultColor'] ?? '2');
                    $data[$section]['defaultMenu'] = $items['defaultMenu'] ?? '' == 'dark-theme' ?  'dark-theme' : 'light-theme';
                    $data[$section]['defaultHeader'] = $items['defaultHeader'] ?? '' == 'dark-theme' ?  'dark-theme' : 'light-theme';
                    $data[$section]['allowCustom'] = !empty($items['allowCustom']);
                    $data[$section]['banner'] = !empty($items['banner']);
                    break;
                case 'colors':
                    foreach ($config['colors'] as $index => $item) {
                        if (!isset($items[$index])) {
                            $config['colors'][$index] = $item;
                            continue;
                        }
                        $data_item = $items[$index];
                        $data[$section][$index]['id'] = $index + 1;
                        $data[$section][$index]['color'] = $this->filterColor($data_item['color'] ?? '');
                        $data[$section][$index]['second'] = $this->filterColor($data_item['second'] ?? '');
                    }
                    break;

            }
        }
        $config = array_merge($config, $data);
        
        OptionModel::G()->setSystemConfig($config);
    }
    /**
     * 颜色检查
     * @param string $color
     * @return string
     * @throws BusinessException
     */
    protected function filterColor(string $color): string
    {
        if (!preg_match('/\#[a-zA-Z]6/', $color)) {
            throw new BusinessException('参数错误');
        }
        return $color;
    }
    /**
     * 变量或数组中的元素只能是字母数字
     * @param $var
     * @return mixed
     * @throws BusinessException
     */
    public static function filterNum($var)
    {
        $vars = (array)$var;
        array_walk_recursive($vars, function ($item) {
            if (is_string($item) && !preg_match('/^[0-9]+$/', $item)) {
                throw new BusinessException('参数不合法');
            }
        });
        return $var;
    }

    /**
     * 检测是否是合法URL Path
     * @param $var
     * @return string
     * @throws BusinessException
     */
    public static function filterUrlPath($var): string
    {
        if (!is_string($var) || !preg_match('/^[a-zA-Z0-9_\-\/&?.]+$/', $var)) {
            throw new BusinessException('参数不合法');
        }
        return $var;
    }
}