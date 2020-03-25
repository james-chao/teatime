<?php
/*!
 * upload demo for php
 * @requires xhEditor
 * 
 * @author Yanis.Wang<yanis.wang@gmail.com>
 * @site http://xheditor.com/
 * @licence LGPL(http://www.opensource.org/licenses/lgpl-license.php)
 * 
 * @Version: 0.9.6 (build 111027)
 * 
 * 註1：本程序僅為演示用，請您務必根據自己需求進行相應修改，或者重開發
 * 註2：本程序特別針對HTML5上傳，加入了特殊處理
 */
header('Content-Type: text/html; charset=UTF-8');

$inputName='filedata';//表單文件域name
$attachDir='upload/neworder';//上傳文件保存路徑，結尾不要帶/
$dirType=1;//1:按天存入目錄 2:按月存入目錄 3:按擴展名存目錄  建議使用按天存
$maxAttachSize=2097152;//最大上傳大小，默認是2M
$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上傳擴展名
$msgType=2;//返回上傳參數的格式：1，只返回url，2，返回參數數組
$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上傳模式，僅為演示用
ini_set('date.timezone','Asia/Taipei');//時區

$err = "";
$msg = "''";
$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
$localName='';

if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上傳
	file_put_contents($tempPath,file_get_contents("php://input"));
	$localName=urldecode($info[2]);
}
else{//標準表單式上傳
	$upfile=@$_FILES[$inputName];
	if(!isset($upfile))$err='文件域的name錯誤';
	elseif(!empty($upfile['error'])){
		switch($upfile['error'])
		{
			case '1':
				$err = '文件大小超過了php.ini定義的upload_max_filesize值';
				break;
			case '2':
				$err = '文件大小超過了HTML定義的MAX_FILE_SIZE值';
				break;
			case '3':
				$err = '文件上傳不完全';
				break;
			case '4':
				$err = '無文件上傳';
				break;
			case '6':
				$err = '缺少臨時文件夾';
				break;
			case '7':
				$err = '寫文件失敗';
				break;
			case '8':
				$err = '上傳被其它擴展中斷';
				break;
			case '999':
			default:
				$err = '無有效錯誤代碼';
		}
	}
	elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '無文件上傳';
	else{
		move_uploaded_file($upfile['tmp_name'],$tempPath);
		$localName=$upfile['name'];
	}
}

if($err==''){
	$fileInfo=pathinfo($localName);
	$extension=$fileInfo['extension'];
	if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
	{
		$bytes=filesize($tempPath);
		if($bytes > $maxAttachSize)$err='請不要上傳大小超過'.formatBytes($maxAttachSize).'的文件';
		else
		{
			switch($dirType)
			{
				case 1: $attachSubDir = 'day_'.date('ymd'); break;
				case 2: $attachSubDir = 'month_'.date('ym'); break;
				case 3: $attachSubDir = 'ext_'.$extension; break;
			}
			$attachDir = $attachDir.'/'.$attachSubDir;
			if(!is_dir($attachDir))
			{
				@mkdir($attachDir, 0777);
				@fclose(fopen($attachDir.'/index.htm', 'w'));
			}
			PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
			$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
			$targetPath = $attachDir.'/'.$newFilename;
			
			rename($tempPath,$targetPath);
			@chmod($targetPath,0755);
			$targetPath=jsonString($targetPath);
			if($immediate=='1')$targetPath='!'.$targetPath;
			if($msgType==1)$msg="'$targetPath'";
			else $msg="{'url':'".$targetPath."','localname':'".jsonString($localName)."','id':'1'}";//id參數固定不變，僅供演示，實際項目中可以是數據庫ID
		}
	}
	else $err='上傳文件擴展名必需為：'.$upExt;

	@unlink($tempPath);
}

echo "{'err':'".jsonString($err)."','msg':".$msg."}";


function jsonString($str)
{
	return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
}
function formatBytes($bytes) {
	if($bytes >= 1073741824) {
		$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
	} elseif($bytes >= 1048576) {
		$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
	} elseif($bytes >= 1024) {
		$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
	} else {
		$bytes = $bytes . 'Bytes';
	}
	return $bytes;
}
?>