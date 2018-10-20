<?php
namespace App\Libs;
use PhalApi\Exception\BadRequestException;

class CoinClient
{
    private $url;
    private $timeout;
    private $username;
    private $password;
    public $is_batch = false;
    public $batch = array();
    public $debug = false;
    public $jsonformat = false;
    public $res = '';
    private $headers = array('User-Agent: Movesay.com Rpc', 'Content-Type: application/json', 'Accept: application/json', 'Connection: close');
    public $ssl_verify_peer = true;
    private $walletlist = array();
    private $coinobj = null;
    private $coinname="";
    private $methodname=array(
        'getaccountaddress'=>array('monero'=>'get_accounts'),
        'getbalance'=>array('monero'=>'get_balance'),
        'listtransactions'=>array('monero'=>'listtransactions'),

    );
    private function checkwallet($coinname)
    {

        if (in_array($coinname, $this->walletlist)) {
            return true;
        }
        throw new BadRequestException('请求钱包不存在', 1);
        return false;
    }

    private function getcoinobj($coinname)
    {
        $this->checkwallet($coinname);
        $seting = $this->walletlist[$coinname];
        if (empty($coinobj)) {
            $this->coinobj = new MeneroCoinClient($seting['user'], $seting['password'], $seting['host'], $seting['port'], $seting['path'], $seting['auth']);
        }
        return $this->coinobj;
    }

    public function __construct($coinname)
    {
        $this->coinname=$coinname;
        $this->walletlist = \PhalApi\DI()->config->get('app.walletlist');
        $this->getcoinobj($coinname);
    }

    public function getaddress($username)
    {
        $methodname=$this->getmethodname('getaccountaddress');
        return $this->coinobj->$methodname($username);
    }
    public function getbalance($username)
    {
        $methodname=$this->getmethodname('getbalance');
        return $this->coinobj->$methodname($username);
    }
    public function listtransactions()
    {
        $methodname=$this->getmethodname('listtransactions');
        return $this->coinobj->$methodname();
    }
    private function getmethodname($method){
        if(isset($this->methodname[$method][$this->coinname])){
            return $this->methodname[$method][$this->coinname];
        }
        return $method;
    }

    public function __call($method, $params){
        return $this->coinobj->$method($params);
    }
}
