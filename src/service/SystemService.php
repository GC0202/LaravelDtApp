<?php

// +----------------------------------------------------------------------
// | LaravelLibrary 7.0 for Laravel 7.0
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 [ https://www.dtapp.net ]
// +----------------------------------------------------------------------
// | 官方网站: https://gitee.com/liguangchun/LaravelLibrary
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 仓库地址 ：https://gitee.com/liguangchun/LaravelLibrary
// | github 仓库地址 ：https://github.com/GC0202/LaravelLibrary
// | Packagist 地址 ：https://packagist.org/packages/liguangchun/laravel-library
// +----------------------------------------------------------------------

namespace DtApp\LaravelLibrary\service;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * 系统参数管理服务
 * Class SystemService
 * @package DtApp\LaravelLibrary\service
 */
class SystemService
{

    /**
     * 配置数据缓存
     * @var array
     */
    protected $data = [];

    /**
     * 设置配置数据
     * @param string $name 配置名称
     * @param string $value 配置内容
     * @param string $type
     * @return string
     */
    public function set($name, $value = '', $type = '')
    {
        $data = ['name' => $name, 'value' => $value, 'type' => $type];
        return Db::table('system_config')->insert($data);
    }

    /**
     * 读取配置数据
     * @param string $name
     * @param string $default
     * @return array|mixed|string
     */
    public function get($name, $default = '')
    {
        try {
            $value = DB::table('system_config')->where(['name' => $name])->value('value');
            return is_null($value)?$default:$value;
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * 保存数据内容
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public function setData($name, $value)
    {
        $data = ['name' => $name, 'value' => serialize($value)];
        return Db::table('system_data')->insert($data);
    }

    /**
     * 读取数据内容
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getData($name, $default = [])
    {
        try {
            $value = DB::table('system_data')->where(['name' => $name])->value('value');
            return is_null($value) ? $default : unserialize($value);
        } catch (Exception $e) {
            return $default;
        }
    }
}
