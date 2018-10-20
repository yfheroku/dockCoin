<?php
/**
 * Desc: xxx
 * Author: wfs
 * Date: 2018/9/4 0004
 * 有关表在 /ReadMe/coin.sql
 * 对接虚拟币主文件，对外接口有：
 * create 生成钱包地址
 * send 发送交易（转账）
 * getRecharge 获取交易记录
 */
namespace App\Api;

use PhalApi\Api;
class dockCoin extends Api{
    private $param = [];
    private $config = [];
    public function __construct(){
        $result = array('status'=>false,'msg'=>'','address'=>'');
        //var_dump($_GET);exit;
        $this->param = $_GET;
        if(!isset($this->param['symbol'])){
            $result['msg'] = '参数错误';
            return $result;
        }
        $this->config = require "../config/coin.php";
        //var_dump($config);exit;
        if(!isset($this->config[$this->param['symbol']])){
            $result['msg'] = '配置信息错误';
            return $result;
        }
    }
    /** 生成钱包地址
     * @return array
     */
    public function create(){
        $result = array('status'=>false,'msg'=>'','address'=>'');
        $symbol = $this->param['symbol'];
        $action = $this->config[$symbol]['create'];
        if(!isset($this->param['username'])){
            $result['msg'] = '参数错误';
            return $result;
        }
        $username = $this->param['username'];//用户标识
        $response = call_user_func(array($action,'createAddress'),$username);
        if(!$response['status']){
            $result['msg'] = $response['msg'];
            return $result;
        }
        $result['status'] = true;
        $result['address'] = $response['info'];
        return $result;
    }

    /** 发送交易操作
     * @return array
     */
    public function send(){
        $result  = array('status'=>false,'msg'=>'','data'=>[]);
        $symbol  = $this->param['symbol'];
        $action  = $this->config[$symbol]['send'];
        //参数验证
        if(!isset($this->param['address']) || !isset($this->param['amount']) || !isset($this->param['username'])){
            $result['msg'] = '参数错误';
            return $result;
        }
        $data = [];
        $data['address'] = $this->param['address'];
        $data['amount'] = $this->param['amount'];
        $data['username'] = $this->param['username'];
        $response = call_user_func(array($action,'sendFrom'),$data);
        if(!$response['status']){
            $result['msg'] = $response['msg'];
            return $result;
        }
        $result['status'] = true;
        $result['data'] = $response['data'];
        return $result;
    }

    /** 拉取服务器充值记录
     * @return array
     */
    public function getRecharge(){
        $result  = array('status'=>false,'msg'=>'');
        $symbol  = $this->param['symbol'];
        $action  = $this->config[$symbol]['recharge'];
        $response = call_user_func(array($action,'autoGet'));
        if(!$response['status']){
            $result['msg'] = $response['msg'];
            return $result;
        }
        $result['status'] = true;
        $result['data'] = $response['info'];
        return $result;
    }
}