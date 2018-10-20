<?php
/**
 * Desc: xxx
 * Author: wfs
 * Date: 2018/9/5 0005
 * 莱特币
 * 详细文档在 /ReadMe/Ltc.php
 */
namespace App\Api;

use PhalApi\Api;
use App\Model\user_address;
use App\Model\user_hash;
use App\Common\Bitcoin;
class Ltc extends Api{
    static $symbol = 'ltc';
    static function createAddress($username){
        $result = array('status'=>false,'msg'=>'','info'=>'');
        //user_address 用于保存用户钱包地址的数据表
        $model = new user_address();
        //用户地址唯一性验证
        $is_exist = $model->where(array('username'=>$username,'symbol'=>static::$symbol));
        if($is_exist){
            $result['status'] = true;
            $result['info'] = $is_exist[0]['address'];
            return $result;
        }
        //读取配置
        $config = require "../config/coin.php";
        $lite = $config[static::$symbol];
        $coin = new Bitcoin($lite['rpc_user'],$lite['rpc_pass'],$lite['rpc_ip'],$lite['rpc_port']);
        $response = $coin->getaddressesbyaccount($username);
        if(!$response){
            $response = $coin->getnewaddress($username);
            if(!$response){
                $result['msg'] = '地址生成失败';
                return $result;
            }
            //把新生成地址的账户设置到主账户下面，可节省货币汇总的步骤
            if(isset($lite['account']) && $lite['account']){
                $coin->setaccount($response,$lite['account']);
            }
            $result['info'] = $response;
        }else{
            //把新生成地址的账户设置到主账户下面，可节省货币汇总的步骤
            if(isset($lite['account']) && $lite['account']){
                $coin->setaccount($response[0],$lite['account']);
            }
            $result['info'] = $response[0];
        }
        //保存地址到数据库
        $sql_data = [];
        $sql_data = [
            'username' => $username,
            'address' => $result['info'],
            'symbol' => static::$symbol,
            'w_time' => time(),
        ];
        $model->insert($sql_data);
        $result['status'] = true;
        return $result;
    }

    /**
     * 转账方法
     * @param
     * array(
     *      'username'=>'用户编号（平台会员唯一标识）',
     *      'address'=>'',钱包地址
     *      'amount'=>'',金额
     * )
     */
    static function sendFrom($param){
        $result = array('status'=>false,'msg'=>'','data'=>'');
        $config = require "../config/coin.php";
        $lite = $config[static::$symbol];
        $coin = new Bitcoin($lite['rpc_user'],$lite['rpc_pass'],$lite['rpc_ip'],$lite['rpc_port']);
        if(!isset($lite['account']) || !$lite['account']){
            $result['msg'] = "配置信息缺失";
            return $result;
        }
        $total_account = $lite['account'];
        $response = $coin->sendfrom($total_account,$param['address'],$param['amount']);
        if(!$response){
            $result['msg'] = "转账失败";
            return $result;
        }
        //user_hash 用于保存用户钱包充值、提币记录的数据表
        $hash = new user_hash();
        //交易信息保存数据库
        $hash_info = $response;
        $tx_data = array(
            'type'          => 2,
            'hash'          => $hash_info,
            'username'      => $param['username'],
            'status'        => 1,
            'amount'        => $param['amount'],
            'address'       => $lite['address'],
            'w_time'        => time(),
            'symbol'        => static::$symbol,
            'currency'      => 'RMB',
            'destination'   => $param['address'],
        );
        $hash->insert($tx_data);

        $result['status'] = true;
        $result['data'] = array('hash'=>$hash_info);
        return $result;
    }

    /**
     * 拉取充币程序，保存到数据库，执行充值程序
     */
    static function autoGet(){
        set_time_limit(0);
        $result = array('status'=>false,'msg'=>'','content'=>[]);
        //user_address 用于保存用户钱包地址的数据表
        //user_hash 用于保存用户钱包充值、提币记录的数据表
        $model = new user_address();
        $hash = new user_hash();
        //查询当前虚拟币的钱包地址列表，并构造索引为钱包地址，值为用户编号的数组，供后面使用
        $user_address = $model->where(array('symbol'=>static::$symbol));
        $bankAddress = array();
        if($user_address){
            foreach ($user_address as $user){
                $bankAddress[strtolower($user['address'])] = $user['username'];
            }
        }else{
            $result['msg'] = "没有用户地址";
            return $result;//还没有生成钱包地址，没有充值记录
        }
        //读取配置
        $config = require "../config/coin.php";
        $lite = $config[static::$symbol];
        //获取最新的一条hash记录
        $last_hash = $hash->where(['type'=>1],1,"w_time desc");
        if($last_hash){
            $last_time = isset($last_hash[0]['w_time'])?$last_hash[0]['w_time']:1536235360;//最晚的记录时间默认现在（避免进入死循环）
        }
        //通过ripple网站接口获取最新纪录并处理保存，limit在根目录下config/coin.php里配置
        //定义充值数组
        $recharge = [];
        $coin = new Bitcoin($lite['rpc_user'],$lite['rpc_pass'],$lite['rpc_ip'],$lite['rpc_port']);
        $page = 1;
        while(true){
            $json = $coin->listtransactions('*',1000,$page);
            if(!$json)
            {
                $result['msg'] = "没有服务器记录";
                return $result;
            }
            //将数组反转排序
            $json = array_reverse($json, true);
            //循环拉取的数据进行数据库保存
            if(is_array($json)){
                if(!empty($json[0])){
                    foreach($json as $tx)
                    {
                        //判断是否跳出
                        if(empty($tx['timereceived'])){
                            continue;
                        }
                        $time = $tx['timereceived'];
                        if($time<$last_time){
                            break 2;//拉取到最新数据
                        }
                        if($tx['category']!='receive'){
                            continue;//类型错误
                        }
                        //检测用户是否存在
                        if(!isset($bankAddress[strtolower($tx['address'])])){
                            continue;//用户不存在
                        }
                        $currency = "RMB";
                        $num = $tx['amount'];
                        $hash_list = $hash->where(array('hash'=>$tx['txid'],"symbol"=>static::$symbol,'type'=>1));
                        if($hash_list)
                        {
                            //echo '发现重复';
                            continue;
                        }
                        $username = $bankAddress[strtolower($tx['address'])];
                        //getaddressesbyaccount(获取匹配label的address数组)
                        $address_info = $coin->getaddressesbyaccount($tx['account']);
                        $address = $address_info[0];
                        $tx_data = array(
                            'type'          => 1,
                            'hash'          => $tx['txid'],
                            'username'      => $username,
                            'status'        => $tx['confirmations']>6?1:0,
                            'amount'        => $num,
                            'address'       => $address,
                            'w_time'        => $time,
                            'symbol'        => static::$symbol,
                            'currency'      => $currency,
                            'destination'   => $tx['address'],
                        );
                        $hash->insert($tx_data);
                        $recharge[] = $tx_data;
                    }
                }
            }
            $page++;
        }
        $result['status'] = true;
        $result['info'] = $recharge;
        return $result;
    }
}
