<?php
namespace DuckAdmin\Business;
use DuckAdmin\Model\OptionModel;

/**
 * 个人资料业务
 */
class ConfigBusiness extends Base
{
    public function getDefaultConfig()
    {
        $config = OptionModel::_()->getSystemConfig();
        if (empty($config)) {
            $config = Helper::Config('pear_config',null,[]);
            $config = $this->updateConfig($config);
        }
        return $config;
    }
    public function updateConfig($post)
    {
        $config = Helper::Config('pear_config',null,[]); //$this->getDefaultConfig();
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
        
        OptionModel::_()->setSystemConfig($config);
        return $config;
    }
    /**
     * 颜色检查
     * @param string $color
     * @return string
     * @throws BusinessException
     */
    protected function filterColor(string $color): string
    {
        $flag = preg_match('/\#[a-zA-Z]{0,6}/', $color);
        Helper::BusinessThrowOn(!$flag, '颜色参数错误');
        return $color;
    }
    /**
     * 变量或数组中的元素只能是字母数字
     * @param $var
     * @return mixed
     * @throws BusinessException
     */
    protected static function filterNum($var)
    {
        $vars = (array)$var;
        array_walk_recursive($vars, function ($item) {
            $flag = (is_string($item) && !preg_match('/^[0-9]+$/', $item)) ? true: false;
            Helper::BusinessThrowOn($flag, '数字参数不合法'); //TODO 改成 PHP自己的验证
        });
        return $var;
    }

    /**
     * 检测是否是合法URL Path
     * @param $var
     * @return string
     * @throws BusinessException
     */
    protected static function filterUrlPath($var): string
    {
        $flag = (!is_string($var) || !preg_match('/^[@\~a-zA-Z0-9_\-\/&?.]+$/', $var)) ? true :false;
        Helper::BusinessThrowOn($flag, 'URL参数不合法'); //TODO 改成 PHP自己的验证
        
        
        ////[[[[ 这里加上相对路径的处理
        if(substr($var,0,2)==='@/'){
            $var = __res(substr($var,2));
        }
        if(preg_match('/^(https?:\/)?\//', $var)){
            return $var;
        }
        return $var;
        //return $var;
        ////]]]]
    }
}