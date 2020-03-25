<?
header( 'Content-Type: text/html; charset=utf-8' );

include_once(dirname(__FILE__) . '/Classes/utility.php');

$sno=$_REQUEST['selsno'];
$uno=$_COOKIE['teatimeuserno'];
$pnoarr=$_REQUEST['pno'];
if(is_array($pnoarr)) $pnos=implode(",",$pnoarr);

$otitle=trim($_REQUEST['ordertitle']);
$unick=trim($_REQUEST['nickname']);
$deadline=trim($_REQUEST['deadline']);
$onote=trim($_REQUEST['ordernote']);
$uemail=trim($_REQUEST['useremail']);
$cycle=$_REQUEST['cycle'];
if($cycle=="byone")
{
	$operiod="none#0";
}
else if($cycle=="bydays")
{
	$operiod="days#".trim($_REQUEST['duration']);
}
else if($cycle=="byweeks")
{
	$operiod="weeks#".implode("-",$_REQUEST['week']);
}

$odeadarr=explode(" ",$deadline); //from 05/04/2011 13:04:32
$oymdarr=explode("/",$odeadarr[0]);
$odeadline=$oymdarr[2]."-".$oymdarr[0]."-".$oymdarr[1]." ".$odeadarr[1]; //to 2011-05-04 13:04:32
$ocreatetime=date("Y-m-d H:i:s");
$oorderkey=md5($sno.$uno.$ocreatetime);

$alldata=array("$unick","$uemail","$otitle","$onote","$odeadline","$oorderkey","$ocreatetime");

if($sno && $uno && $pnos)
{
	//$strSql="INSERT INTO ttordertb (sno,uno,unick,uemail,pnos,otitle,onote,odeadline,operiod,oorderkey,oonoff,omailflag,ocreatetime) VALUES ($sno,$uno,'$unick','$uemail','$pnos','$otitle','$onote','$odeadline','$operiod','$oorderkey','y','0','$ocreatetime')";
	global $util;
	$values=array(
		'sno'=>$util->db->SQLValue($sno,"int"),
		'uno'=>$util->db->SQLValue($uno,"int"),
		'unick'=>$util->db->SQLValue($unick),
		'uemail'=>$util->db->SQLValue($uemail),
		'pnos'=>$util->db->SQLValue($pnos),
		'otitle'=>$util->db->SQLValue($otitle),
		'onote'=>$util->db->SQLValue($onote),
		'odeadline'=>$util->db->SQLValue($odeadline),
		'operiod'=>$util->db->SQLValue($operiod),
		'oorderkey'=>$util->db->SQLValue($oorderkey),
		'oonoff'=>$util->db->SQLValue('y'),
		'omailflag'=>$util->db->SQLValue('0'),
		'ocreatetime'=>$util->db->SQLValue(date("Y-m-d H:i:s"))
	);
		
	$ret=$util->db->InsertRow("ttordertb",$values);
	if($ret)
	{
		$alldata[7]=$util->db->GetLastInsertID();
		$epxtime= 24 * 60 * 60;
		setcookie("teatimeorderkey",$oorderkey,time()+$epxtime);
		setcookie("teatimeorderno",$alldata[7],time()+$epxtime);
		setcookie("teatimeoldorder",$_COOKIE['teatimeoldorder'].",$alldata[7]",time()+$epxtime);

		sendordermail($alldata);
		save4users($alldata);
		echo "<script>alert('訂單新增成功，請提供訂單密碼給您邀約的成員');location.href='myorder.php';</script>";
	}
	else
		echo "<script>alert('訂單新增失敗');history.back();</script>";
}
else
{
	echo "<script>alert('訂單新增失敗，請確定您已選擇店家及商品');history.back();</script>";
}

function sendordermail($ordarr)
{
 	//'$unick','$uemail','$otitle','$onote','$odeadline','$oorderkey','$ocreatetime'
	
	$subject="下午茶訂購系統-新邀約通知信";
	$from="james.chao@quantatw.com";
	$fromname="TeaTime下午茶";
	$to=$ordarr[1];
	$cc="";
	$bcc="";
	$mailbody="<span style='font-size:12px'>
	$ordarr[0] 您好,<br>
	<br>
	您於 $ordarr[6] 的時候,在<u>下午茶訂購系統</u>新增了一個新的邀約。<br>
	以下是本邀約的詳細資料<BR>
	============================================<br>
	發起人: <a href='mailto:$ordarr[1]'>$ordarr[0]</a><br>
	邀約標題: $ordarr[2]<br>
	訂單網址: <span style='color:#00f;font-weight:bold'>[<a href='http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]'>請點我</a>]</span><br>
	邀約說明: $ordarr[3]<br>
	終止時間: $ordarr[4]<br>
	訂單密碼: <span style='color:#00f;font-weight:bold'><a href='http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]'>$ordarr[5]</a></span><br>
	============================================<br>
	<br>
	<a href='http://lttap01.rsquanta.com/'>http://lttap01.rsquanta.com/</a><br>
	<br>
	確認後，請您的邀約成員用上述的<font color='#ff0000'>訂單密碼</font>登入，謝謝。<br>
	---------------------------------------------------<br>
	感謝您的參與<br>
	
	下午茶訂購系統 <a href='lttap01.rsquanta.com' target='_blank'>http://lttap01.rsquanta.com/</a> <br>
	系統聯絡人:james.chao@quantatw.com</span>";
	sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody);
}

function send2friend($ufemail='',$ordarr=array())
{
	//'$unick','$uemail','$otitle','$onote','$odeadline','$oorderkey','$ocreatetime'

	$subject="下午茶訂購系統-$ordarr[0] 邀請您加入訂購";
	$from=$ordarr[1];
	$fromname="TeaTime下午茶";
	if(!empty($ufemail)) $to=$ufemail;
	$cc="";
	$bcc="";
	
	$mailbody="<span style='font-size:12px'>
	您好,<br>
	<br>
	您的朋友 $ordarr[0] 於 $ordarr[6] 的時候,在<u>下午茶訂購系統</u>發起了一個新的邀約。<br>
	以下是本邀約的詳細資料<BR>
	============================================<br>
	發起人: <a href='mailto:$ordarr[1]'>$ordarr[0]</a><br>
	邀約標題: $ordarr[2]<br>
	訂單密碼: <span style='color:#00f;font-weight:bold'><a href='http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]'>$ordarr[5]</a></span><br>
	邀約說明: $ordarr[3]<br>
	終止時間: $ordarr[4]<br>
	============================================<br>
	<br>
	<a href='http://lttap01.rsquanta.com/'>http://lttap01.rsquanta.com/</a><br>
	<br>
	若確認參加，請使用上述的<font color='#ff0000'>訂單密碼</font>登入，謝謝。<br>
	---------------------------------------------------<br>
	感謝您的參與<br>
	
	下午茶訂購系統 <a href='lttap01.rsquanta.com' target='_blank'>http://lttap01.rsquanta.com/</a> <br>
	系統聯絡人:james.chao@quantatw.com</span>";
	
	//echo "$from,$fromname,$to,$cc,$bcc,$subject,$mailbody2";
	sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody);
}
function sendAppMessage($displayname,$ordarr)
{
	global $util;
	$onote=strip_tags($ordarr[3]);
	$workid=$util->myldap->getUserWorkid($displayname);
	$mailbody="您好,
	
	您的朋友 $ordarr[0] 於 $ordarr[6] 的時候,在TeaTime訂購系統發起了一個新的邀約。
	以下是本邀約的詳細資料
	============================
	發起人: $ordarr[0]
	邀約標題: $ordarr[2]
	訂單密碼: http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]
	邀約說明: $onote
	終止時間: $ordarr[4]
	============================
	若確認參加，請使用上述的'訂單密碼'登入，謝謝。
	-------------------------------------------
	感謝您參與下午茶訂購系統 http://lttap01.rsquanta.com
	系統聯絡人:james.chao@quantatw.com";
	if($workid)
	{
		$app=new quantaapp();
		$userlist=$app->registerList();
		$pos=strpos($userlist,$workid);

		if($pos!==false)
		{
			$msgdata=array(
				//'ReceiveCompanyCode'=>'QCI', 	//收訊者公司別
				'ReceiveType'=>'3', 			//收訊者類別 3:person
				'ReceiveID'=>'quanta\\'.$workid,		//收訊者代號
				'Title'=>'您有一個新的下午茶邀約',			//訊息標題	
				'Detail'=>$mailbody
			);
			$msg=$app->simpleMessage($msgdata);
			//print_r($msg);
		}
	}
}
function sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody)
{
	//$header .="Reply-To: ".$to."\r";
	//$header .="Return-Path: ".$to."\r";
	//$header .="Content-Type: multipart/alternative\r";
	$subject="=?UTF-8?B?".base64_encode("$subject")."?=";
	//$mailFrom = '=?utf-8?B?'.base64_encode("$fromname").'?='.' <$from>';
	$mailFrom= $from;
	$headers = "MIME-Version: 1.0\r";
	$headers .= "Content-type: text/html; charset=UTF-8\r";
	$headers .= "From: $mailFrom\r";
	$headers .= "To: $to\r";
	if($cc) $headers .="Cc: $cc\r";
	if($bcc) $headers .= "Bcc: $bcc\r";	
	
	if($from&&$to&&$mailbody&&$subject)
	{
		
		if(chkservice('10.243.29.110',25))
		{
			//echo "mail($to, $subject, $mailbody, $headers)";
			//mail("james.chao@quantatw.com", "test", "test", $headers);
			$ret=mail($to, $subject, $mailbody, $headers);
			
			//echo $mailbody;
			//echo $headers;
			//exit;
		}
	}
	return $ret;
}
function chkservice($host, $port)
{  
    if (ini_get('display_errors')==1){ //判斷ini的display errors的設定
    $ch_ini_display=1;
    ini_set('display_errors', 0);//設定連線錯誤時不要display errors
    }
    $hostip = gethostbyname($host); //檢查輸入的host name dns正解
        if (!$x = fsockopen($hostip, $port, $errno, $errstr, 1)) //測試連線
            $ret=0;
        else
			$ret=1;
	if ($x) fclose($x); //關閉連線
    if ($ch_ini_display==1) ini_set('display_errors', 1); //將ini的display error設定改回來
	return $ret;
} 

function save4users($alldata)
{
	global  $util;
	$ufnames=str_replace(";",",",$_POST["ufname"]); 
	$ufnamearr=explode(",",$ufnames);
	$uno=$_COOKIE['teatimeuserno'];
	$userArray=array();
	$mailArray=array();

	foreach($ufnamearr as $term)
	{
		$term=trim($term);
		$ret=$util->myldap->isPersonGroup($term);

		if($ret) //it is a group
		{
			$stripterm=trim($term,"*");
			$subUsers=$util->myldap->getAllUserInGroup($stripterm);

			if (is_array($subUsers)) 
			{
				$userArray = array_merge($userArray, $subUsers); 
				$userArray = array_unique($userArray);
			}
			//$term=trim($term,"*");
			//have to fully matched
			$getarr=$util->myldap->getKeyGroup($term,true); 
			$getarr=array_keys($getarr);
			$mailArray[]=$getarr[0];
			//deal with duplicated email which in group
			foreach($mailArray as $key=>$mail)
			{
				if(array_key_exists($mail,$userArray)) unset($mailArray[$key]);
			}

		}
		else //it is a person
		{
			if(!in_array($term,$userArray)) 
			{
				$userArray[]=$term;
				$getarr=$util->myldap->getKeyUser($term,true); //have to fully matched
				$getarr=array_keys($getarr);
				$mailArray[]=$getarr[0];
			}
		}
		
	}
	
	foreach($userArray as $ufn)
	{
		if(!empty($ufn)) 
		{
			$values=array(
				'uno'=>$uno,
				'ufname'=>$util->db->SQLValue($ufn),
				'ono'=>$util->db->SQLValue($alldata[7],"int"),
				'addtime'=>$util->db->SQLValue(date("Y-m-d H:i:s"))
			);
			$ret=$util->db->InsertRow("ttinvitetb",$values);

			//send quanta app message
			//if($ret) sendAppMessage($ufn,$alldata);
		}
	}
	
	//send email to person or group
	foreach($mailArray as $ufemail)
	{
		send2friend($ufemail,$alldata);
	}
	
	return $ret;
}
?>