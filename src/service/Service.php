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

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;

/**
 * 自定义服务基类
 * Class Service
 * @package DtApp\LaravelLibrary\service
 */
abstract class Service
{
    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * Service constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app =$app;
        $this->initialize();
    }

    /**
     * 初始化服务
     * @return $this
     */
    protected function initialize(){
        return $this;
    }

    /**
     * 静态实例对象
     * @param mixed ...$args
     * @return mixed
     * @throws BindingResolutionException
     */
    public function instance(...$args){
        return Container::getInstance()->make(static::class, $args);
    }
}
