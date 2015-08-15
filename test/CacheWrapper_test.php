<?php

require FRAMEWORK_PATH.'/CacheWrapper.php';
echo '<pre>';


$mc = new CacheWrapper();

// 设置haha为项目空间，user表下的空间，wangdk key
var_dump($mc->setPS('haha')->setNS('user')->setCache('wangdk', "wangdekang88888111", 180));
var_dump($mc->setPS('haha')->setNS('user')->setCache('wangdk1', "wangdekang88888111", 180));
var_dump($mc->setPS('haha')->setNS('goods')->setCache('wangdk', "wangdekang88888", 180));


$mc->setPS('haha')->setNs('user')->deleteCache('wangdk');

var_dump($mc->setPS('haha')->setNS('goods')->getCache('wangdk'));
var_dump($mc->setPS('haha')->setNS('user')->getCache('wangdk1'));
var_dump($mc->setPS('haha')->setNS('user')->getCache('wangdk'));


print_r(CacheWrapper::$debug);

// 设置项目命名空间,表空间，sql
// var_dump($mc->setPS()->setNS('user')->setCache('wangdk', "wangdekang", 180));
// var_dump($mc->setPS()->setNS('user')->setCache('shenxj', "shenxiaoning", 180));
// var_dump($mc->setPS()->setNS('goods')->setCache('wangdk', "wangdekang", 180));

// var_dump($mc->setPS()->setNS('goods')->getCache('wangdk'));
// var_dump($mc->setPS()->setNS('user')->getCache('wangdk'));
// var_dump($mc->setPS()->setNS('user')->getCache('shenxj'));


// var_dump($mc->setPS()->delNS('user'));
// var_dump($mc->setPS()->delPS());


// var_dump($mc->setPS()->setNS('goods')->deleteCache('wangdk'));
// var_dump($mc->setPS()->setNS('user')->deleteCache('wangdk'));

// var_dump($mc->setPS()->setNS('goods'));
// var_dump($mc->setPS()->setNS('user')->getCache('wangdk'));
// var_dump($mc->setPS()->setNS('user')->getCache('shenxj'));

// var_dump($mc->setPS('haha')->setNS('user')->getCache('wangdk'));



