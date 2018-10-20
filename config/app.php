<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),
    /**
     * 钱包服务器配置
     */
    'walletseervers' => array(
        'getmenero' => array(
            'host' => '',
            'user' => '',
            'password'=>'',
            'port'=>''
        ),
    ),
    /**
     * 支持钱包列表
     * 钱包服务器配置
     */
    'walletlist' => array(
        'monero',
        'monero' => array(
            'host' => '47.98.168.8',
            'user' => 'monero',
            'password'=>'pig520!@#asdaa',
            'port'=>'18082',
            'path'=>'json_rpc',
            'auth'=>CURLAUTH_DIGEST,
        ),
        'bitcoin',
        'bitcoin' => array(
            'host' => '47.98.168.8',
            'user' => 'btcuser',
            'password'=>'btcuser',
            'port'=>'8332',
            'path'=>'',
            'auth'=>CURLAUTH_BASIC,
        ),
    ),
    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*         通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index     全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        'Site.Index',
    ),
);
