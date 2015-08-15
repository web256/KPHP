<?php
echo '<pre>';
global $mc;

$mc->flushAll();

// 设置haha为项目空间，user表下的空间，wangdk key
var_dump($mc->setPS('haha')->setNs('user')->setCache('wangdk', "wangdekang88888111", 180));
var_dump($mc->setPS('haha')->setNS('user')->setCache('wangdk1', "wangdekang88888111", 180));
var_dump($mc->setPS('haha')->setNS('goods')->setCache('wangdk', "wangdekang88888", 180));

$mc->setPS('haha')->delNS('user');
$mc->setPS('haha')->setNs('goods')->deleteCache('wangdk');



var_dump($mc->setPS('haha')->setNS('goods')->getCache('wangdk'));
var_dump($mc->setPS('haha')->setNS('user')->getCache('wangdk1'));
var_dump($mc->setPS('haha')->setNS('user')->getCache('wangdk'));


print_r(CacheWrapper::$debug);




