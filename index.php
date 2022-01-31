<?php
//ALL IN ONE is B E S T !!!!!!
//But I didn't.



//Cloudflare Config
$zoneId = "";   //Cloudflare 区域ID
$accountId = ""; //Cloudflare 账户ID
//$apiTokenRead = ""; //DNS 读取权限相关Token
//$apiTokenEdit = ""; //DNS 编辑权限相关Token
$globalToken = ""; //全局Token
$authEmail = ""; //登录Cloudflare的邮箱

//Others Config
$getIpUrl = "http://yourdomian/ip.php";  //公网Ip获取地址
$apiUrl = "https://api.cloudflare.com/client/v4/zones"; 
$headArrayData = array("Content-Type: application/json","X-Auth-Email: $authEmail","X-Auth-Key: $globalToken"); //请求头信息

//DNS Config
$domian = "example.com";  //用于解析的域名（必须写全，如： example.com , myhome.example.com , 114514.example.com etc.）
$dnsType = "A"; //排序 DNS类型 依据(可选： A, AAAA, CNAME, HTTPS, TXT, SRV, LOC, MX, NS, CERT, DNSKEY, DS, NAPTR, SMIMEA, SSHFP, SVCB, TLSA 等) 
$dnsContent = getPublicIp($getIpUrl); //要解析到的ip地址,自动获取
$dnsTTL = "600"; //解析缓存时间，可选：1(自动),60~86400都行
$dnsProxy = false; //是否启用Cloudflare代理，填入布尔值


// getDnsId
// 具体参见 https://api.cloudflare.com/#dns-records-for-a-zone-list-dns-records

$getUrl = $apiUrl . "/" . $zoneId . "/dns_records?name=" . $domian; //根据域名定位数据

//updateDns
//具体参见 https://api.cloudflare.com/#dns-records-for-a-zone-update-dns-record

$putUrl = $apiUrl . "/" . $zoneId . "/dns_records/" . getDnsId($getUrl,$headArrayData);


class putData {
	//定义配置类
       public $type = "";
       public $name  = "";
       public $content = "";
       public $ttl = "";
       public $proxied = "";
   }
   //新建配置并转换为 Json
   $e = new putData();
   $e->type = $dnsType;
   $e->name  = $domian;
   $e->content = $dnsContent;
   $e->ttl = $dnsTTL;
   $e->proxied = $dnsProxy;

   $putData = json_encode($e);
   

function getDnsId($getUrl,$headArrayData){
	//使用Curl来GET
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $getUrl);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headArrayData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$outPutJson = curl_exec($ch); //输出数据 
	curl_close($ch);
	$outPut = json_decode($outPutJson, true); //解析Json
	$Id = $outPut['result'][0]['id'];
	
	return $Id;
}

function updateDns($putUrl,$putData,$headArrayData) {
    $ch = curl_init(); //初始化 
    curl_setopt($ch, CURLOPT_URL, $putUrl); //设置请求的URL
    curl_setopt ($ch, CURLOPT_HTTPHEADER, $headArrayData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PUT"); //设置请求方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $putData);//设置提交的字符串
    $aoutput = curl_exec($ch);
    curl_close($ch);
    return $aoutput;
	
}

function getPublicIp($getIpUrl) {
	//获取公网Ip
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $getIpUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	$ip = curl_exec($ch);
	curl_close($ch);
	return $ip;
}

//Main
//判断ip.txt是否存在
$fileStatus = file_exists("ip.txt");
$lastFile = file_get_contents("ip.txt"); 
	 if($fileStatus){
	 	// 存在则判断当前ip是否与ip.txt中的ip地址相同
	 		    if(getPublicIp($getIpUrl) == $lastFile) {
	 		    	file_put_contents("log.txt", "IP地址未改变", LOCK_EX);	 		    	
	 		    }else {
	            file_put_contents("ip.txt", getPublicIp($getIpUrl), LOCK_EX); // 写入当前公网IP到ip.txt
    	         file_put_contents("log.txt",  updateDns($putUrl,$putData,$headArrayData), LOCK_EX); //将 更新DNS记录 的反馈记录在案（log.txt） 		    
    	         echo "请求完毕,你的请求信息为" . $putData;
	 		    }
       
    }else{
    	//不存在
    	file_put_contents("ip.txt", getPublicIp($getIpUrl), LOCK_EX); // 写入当前公网IP到ip.txt
    	file_put_contents("log.txt",  updateDns($putUrl,$putData,$headArrayData), LOCK_EX); //将 更新DNS记录 的反馈记录在案（log.txt）
       echo "首次运行,你的请求信息为" . $putData; 
    }

?>
