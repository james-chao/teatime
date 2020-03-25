<?php
session_start();

if(empty($_SESSION['teatimeldapgroup']) || empty($_SESSION['teatimeldapreceipt'])) 
{
	include_once(dirname(__FILE__) . '/Classes/myLDAP.php');
	
	$myldap=new myLDAP();
	
	if($myldap->isAuth())
	{
		$ldapgroup = $myldap->getAllGroup();
		$_SESSION['teatimeldapgroup']=json_encode($ldapgroup);
		$ldapreceipt = $myldap->getAllUser();
		$_SESSION['teatimeldapreceipt']=json_encode($ldapreceipt);
	}
}
else
{
	if(!empty($_SESSION['teatimeldapgroup'])) $ldapgroup=json_decode($_SESSION['teatimeldapgroup']);
	if(!empty($_SESSION['teatimeldapreceipt'])) $ldapreceipt=json_decode($_SESSION['teatimeldapreceipt']);
	$ldapgroup=object_to_array($ldapgroup);
	$ldapreceipt=object_to_array($ldapreceipt);
}

if(empty($_SESSION['teatimeldapgroup']) || empty($_SESSION['teatimeldapreceipt'])) 
{
	echo "{query:'',suggestions:['no any data from Ldap...']}";
	exit;
}



$receipts=array();
$emails=array();
$query=$_GET['query'];

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

function object_to_array($stdclassobject)
{
    $_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;

    foreach ($_array as $key => $value) {
        $value = (is_array($value) || is_object($value)) ? object_to_array($value) : $value;
        $array[$key] = $value;
    }

    return $array;
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