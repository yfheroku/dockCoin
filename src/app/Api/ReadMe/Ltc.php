<?php
/**
 * Desc: xxx
 * Author: wfs
 * Date: 2018/9/4 0004
 * @@文档文件 莱特币说明文档
 *
 * 参考网站 http://blog.csdn.net/u013695144/article/details/37498785
 *
 * @@生成地址
 * 1、getaddressesbyaccount
 * 判断是否已经生成过地址，如果生成就输出，没有就2
 * 2、getnewaddress
 * 生成新地址
 *
 * @@发起交易
 * 莱特币的交易
 * 1、sendfrom 转账方 收账方 金额（命令行执行顺序）
 *
 *
 * @@充值
 * 1、listtransactions * 1000（每页显示数量） 0（页码）（命令行执行顺序）
 * 拉取最新的交易记录
 * 2、匹配用户充值记录
 * 3、写入钱包数据库
 * 4、返回交易信息数组供平台操作（充值，保存）
 */