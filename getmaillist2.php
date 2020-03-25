<?php
session_start();
include_once(dirname(__FILE__) . '/Classes/myLDAP.php');

$receipts=array();
$emails=array();
$query=$_GET['query'];
	
$myldap=new myLDAP();
	
if($myldap->isAuth())
{
	$ldapgroup = $myldap->getKeyGroup($query);
	$ldapreceipt = $myldap->getKeyUser($query);
	//print_r($ldapgroup);
	//print_r($ldapreceipt);
}


function withSlashes($str){
        return preg_replace('/([\x00-\x1F\*])/e',
                            '"\\\\$1"',
                            $str);
    }
	
function keywordcheck($v) 
{	
	//global $query;
	$query=$_GET['query'];
	//$query='***管理資訊中心';//管理資訊中心資訊系統處
	$query=withSlashes($query);
	//echo $query;
	if (eregi("^$query", $v)) return true;

	return false;
}


$matcharr=array_filter($ldapgroup,"keywordcheck");
foreach($matcharr as $email=>$receipt)
{
	$receipts[]="'".$receipt."'";
	$emails[]="'".$email."'";
}
$matcharr=array_filter($ldapreceipt,"keywordcheck");
foreach($matcharr as $email=>$receipt)
{
	$receipts[]="'".$receipt."'";
	$emails[]="'".$email."'";
}
//print_r($matcharr);

echo "{query:'".$query."',
 suggestions:[".implode(",",$receipts)."],
 data:[".implode(",",$emails)."]}";

?>