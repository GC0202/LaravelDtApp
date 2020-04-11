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
class SystemService extends Service
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
     * @return static
     */
    public function set($name, $value = '')
    {
        list($type, $field) = $this->parse($name);
        if (is_array($value)) {
            foreach ($value as $k => $v) $this->set("{$field}.{$k}", $v);
        } else {
            $this->data = [];
            $data = ['name' => $field, 'value' => $value, 'type' => $type];
            $this->save('system_config', $data, 'name', ['type' => $type]);
        }
        return $this;
    }

    /**
     * 读取配置数据
     * @param string $name
     * @return array|mixed|string
     */
    public function get($name)
    {
        list($type, $field, $outer) = $this->parse($name);
        if (empty($this->data)) foreach (DB::table('system_config')->select() as $vo) {
            $this->data[$vo['type']][$vo['name']] = $vo['value'];
        }
        if (empty($name)) {
            return empty($this->data[$type]) ? [] : ($outer === 'raw' ? $this->data[$type] : array_map(function ($value) {
                return htmlspecialchars($value);
            }, $this->data[$type]));
        } else {
            if (isset($this->data[$type]) && isset($this->data[$type][$field])) {
                return $outer === 'raw' ? $this->data[$type][$field] : htmlspecialchars($this->data[$type][$field]);
            } else return '';
        }
    }

    /**
     * 数据增量保存
     * @param $dbQuery string 数据查询对象
     * @param $data array 需要保存或更新的数据
     * @param string $key 条件主键限制
     * @param array $where 其它的where条件
     * @return bool|int
     */
    public function save($dbQuery, $data, $key = 'id', $where = [])
    {
        $db = is_string($dbQuery) ? DB::table($dbQuery) : $dbQuery;
        list($table, $value) = [$dbQuery, isset($data[$key]) ? $data[$key] : null];
        $map = isset($where[$key]) ? [] : (is_string($value) ? [[$key, 'in', explode(',', $value)]] : [$key => $value]);
        if (is_array($info = DB::table($table)->where($where)->where($map)->find(1)) && !empty($info)) {
            if (DB::table($table)->where($where)->where($map)->update($data) !== false) {
                return isset($info[$key]) ? $info[$key] : true;
            } else {
                return false;
            }
        } else {
            return DB::table($table)->insertGetId($data);
        }
    }

    /**
     * 解析缓存名称
     * @param string $rule 配置名称
     * @param string $type 配置类型
     * @return array
     */
    private function parse($rule, $type = 'base')
    {
        if (stripos($rule, '.') !== false) {
            list($type, $rule) = explode('.', $rule);
        }
        list($field, $outer) = explode('|', "{$rule}|");
        return [$type, $field, strtolower($outer)];
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
        return $this->save('system_data', $data, 'name');
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
