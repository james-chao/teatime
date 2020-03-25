<?
include_once(dirname(__FILE__) . '/Classes/utility.php');

require_once "conn.php";
$uno=$_COOKIE['teatimeuserno'];
?>
<div style="border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;">您可能也認識的朋友</div>
<?php 

function checkUrl($url) {
    preg_match('/^(?P<protocol>[a-z]+):\/\/(?P<hostname>[\w\._-]+)(?P<resource>\/.*)/',$url, $uriSet);
    $portMap = array('http' => 80, 'https' => 443);
    if (!isset($portMap[$uriSet['protocol']]))
        return false;
    else
        $port = $portMap[$uriSet['protocol']];
    $fh = fsockopen('tcp://'.$uriSet['hostname'], $port, $errno, $errstr, 30);
    if (!$fh) {
        $rc = false;
    } else {
        $cmd = "HEAD {$uriSet['resource']} HTTP/1.1\r\n";
        $cmd .= "Host: www.example.com\r\n";
        $cmd .= "Connection: Close\r\n\r\n";

        fputs($fh, $cmd);
        $response = fgets($fh);

        list($h, $rc) = explode(' ', $response);
        while(!feof($fh))
            fgets($fh);
        fclose($fh);
        
    }
    return ($rc==200)?true:false; //404 false
}

function authProxy($url,$proxy_ip='',$proxy_port='',$loginpassw='')
{
    $loginpassw = 'quanta\tds:James&Amy';
    $proxy_ip = '10.243.17.220'; //proxy
    $proxy_port = '80';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
    curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpassw);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function ddd($url){
	$auth = base64_encode('quanta\tds:James&Amy');
	$aContext = array(
		'http' => array(
			'proxy' => 'tcp://proxy:80',
			'request_fulluri' => true,
			'header' => "Proxy-Authorization: Basic $auth",
		),
	);
	$cxContext = stream_context_create($aContext);
	
	$sFile = file_get_contents("http://www.google.com", false, $cxContext);
	
	return $sFile;
}

$strSql="SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttfriendtb N INNER JOIN ttusertb U ON F.ufno=N.uno AND N.ufno=U.uno WHERE uonoff='y' AND F.uno=$uno AND F.fagree='y' AND N.ufno NOT IN (SELECT ufno FROM ttfriendtb WHERE uno=$uno) UNION SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttfriendtb N INNER JOIN ttusertb U ON F.uno=N.uno AND N.ufno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND F.fagree='y' AND N.ufno<>$uno LIMIT 25";
$data_rows=mysql_query($strSql);

while(list($ufno,$uname,$unick,$upic)=mysql_fetch_row($data_rows))
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	
	echo "<div  class='userdiv' data-ufno='$ufno'><div class='userpicdiv'><img class='userpic' title='加 $unick 為我的朋友' src='$imgsrc'  /></div><div class='usernick'>$uname</div><div class='useradd' /></div></div>";
}
?><div style="clear:both;border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;"><a href="tobefriend.php" >朋友搜尋</a></div>

<style>
.userdiv{ border:solid 1px #eee;width:56px;height:55px;float:left;margin:2px; text-align:center;}
.userpic{ width:40px;height:40px;}
.useradd{ width:16px;height:16px;background-image:url(css/icons/edit_add.png);background-repeat:no-repeat; position:relative; top:-48px;left:36px; left:16px\9; z-index:1000; display:none;}
.usernick{font-size:11px;}

</style>
<script>
$(document).ready(function(){
	$('.userdiv').mouseover(function(){
		$(this).css('border','solid 1px #ccc').find('.useradd').css('display','block');
	}).mouseout(function(){
		$(this).css('border','solid 1px #eee').find('.useradd').css('display','none');
	}).click(function(){
		var ufno=$(this).data('ufno');
		$.post("addfriend.php",{ufno:ufno},function(r){
			var msg="";
			if(r==0) msg="加入失敗";
			else if(r==1) msg="交友等候認可回覆";
			else if(r==2) msg="已經加入過了";
			else msg="加入朋友發生錯誤";
	  		$.messager.show({title:'交友訊息',msg:msg,width:150});
		});
	});
});
</script>