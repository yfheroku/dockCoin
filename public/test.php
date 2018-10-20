<?php
function http_request_xml($url,$data = null,$arr_header = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if(!empty($arr_header)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $arr_header);
    }
    curl_setopt($curl,CURLOPT_HEADER,1);
   // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
   // echo $output;
    curl_close($curl);
    unset($curl);
    return $output;
}


$url = "47.98.168.8:18082/json_rpc";
$arr_header[] = "Content-Type:application/json";
$arr_header[] = "Authorization: Basic ".base64_encode("monero:monero"); //添加头，在name和pass处填写对应账号密码
$request = json_encode(array(
    'method' => 'create_account',
    'params' => array('label'=>'asddd'),
    'id'     => 0
));
$res = http_request_xml($url,$request, $arr_header);
?>