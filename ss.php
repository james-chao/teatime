<?
header( 'Content-Type: text/html; charset=utf-8' );

include_once(dirname(__FILE__) . '/Classes/quantaapp_v2.php');
$workid='95052215';
$mailbody="您好,

您的朋友 aa 於 aaa 的時候,在TeaTime訂購系統發起了一個新的邀約。
以下是本邀約的詳細資料
============================
發起人: aaa
邀約標題: aaa
訂單密碼: http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]
邀約說明: aaa
終止時間: aaa
============================
若確認參加，請使用上述的'訂單密碼'登入，謝謝。
-------------------------------------------
感謝您參與下午茶訂購系統 http://lttap01.rsquanta.com
系統聯絡人:james.chao@quantatw.com";

$app=new quantaapp();
$userlist=$app->registerList();
$pos=strpos($userlist,$workid);
if($pos!==false)
{
	$msgdata=array(
		'ReceiveType'=>'3', 			//收訊者類別 3:person
		'ReceiveID'=>'quanta\\'.$workid,		//收訊者代號
		'Title'=>'您有一個新的下午茶邀約',			//訊息標題	
		'Detail'=>$mailbody
	);
	$ret=$app->simpleMessage($msgdata);
}
print_r($ret);	
?>