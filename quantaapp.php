<?php

class quantaapp
{
	private $ServiceID=37;
	private $CreateCompany='QCI';
	private $CreateName='95052215';
	private $Organization="MIS";

	public function registerList($ServiceID='')
	{
		//register list 
		$ServiceID=$ServiceID?$ServiceID:$this->ServiceID;
		$jsonarr=array(
			'ServiceID'=>$ServiceID,
			'CreateCompany'=>$this->CreateCompany,
			'CreateName'=>$this->CreateName
		);
		$url = 'https://appservice.quantatw.com/API/ServiceRegisterList';

		$PostData =json_encode($jsonarr);
		$result=self::postData($url,$PostData);
		return $result;
	}  
	public  function simpleMessage($data=array(),$ServiceID='')
	{
		$ServiceID=$ServiceID?$ServiceID:$this->ServiceID;
		self::sendOneMessage($ServiceID,$data,1);
	}
	public  function webMessage($data=array(),$ServiceID='')
	{
		$ServiceID=$ServiceID?$ServiceID:$this->ServiceID;
		self::sendOneMessage($ServiceID,$data,2);
	}
	public function sendOneMessage($ServiceID,$data, $type=1)
	{
		//$type=1 simple message
		//$type=2 web message

		$jsonarr=array(
			'ServiceID'=>$ServiceID,			//服務代號
			'ReceiveCompanyCode'=>$data['ReceiveCompanyCode'], 	//收訊者公司別
			'ReceiveType'=>$data['ReceiveType'], 			//收訊者類別
			'ReceiveID'=>$data['ReceiveID'],		//收訊者代號
			'Title'=>$data['Title'],			//訊息標題	
			'Detail'=>$data['Detail'],		//訊息內文
			'Organization'=>$this->Organization,			//發佈訊息單位
			'CreateCompany'=>$this->CreateCompany,		//服務提供者公司別
			'CreateName'=>$this->CreateName		//服務建立者帳號
		);
		$PostData ="[". json_encode($jsonarr)."]";
		if($type==1)
			$url='https://appservice.quantatw.com/API/SimpleMessage';
		elseif($type==2)
			$url='https://appservice.quantatw.com/API/WebMessage';
		else
			return false;

		$result=self::postData($url,$PostData);
		return $result;
	}
	public function fullMessage($ServiceID='',$data=array())
	{
		//full message with duplex
		$ServiceID=$ServiceID?$ServiceID:$this->ServiceID;
		$jsonarr=array(
			'ServiceID'=> $ServiceID,               //服務代號
			'ReceiveCompanyCode'=>$data['ReceiveCompanyCode'],    //收訊者公司別
			'ReceiveType'=> $data['receivetype'], 			//收訊者類別。1：發送全公司。2：發送特定部門。3：發送特定個人 
			'ReceiveID'=> $data['receiveid'], 		//收訊者代號：若類別為1→則輸入QCI or QSMC..等。若類別為2→則輸入部門代碼。若類別為3→則輸入工號
			'Title'=> $data['title'],			//訊息標題
			'Detail'=> $data['detail'],		//訊息內文
			'ShortFlag'=>$data['shortflag'],				//短摘要註記   0=>預設值 1=>不顯示Detail
			'MessageType'=>$data['messagetype'],			//訊息類別(單向=>1，雙向=>2)
			'Link'=>$data['link'],	//回覆Web API link
			'Guid'=>$data['guid'],			//回覆key值	
			'ApproveValue'=>$data['approvevalue'],		//雙向選單值
			'ApproveText'=>$data['approvetext'],	//雙向選單顯示文字
			'Organization'=> $this->Organization,			//發佈訊息單位
			'CreateCompany'=> $this->CreateCompany,			//服務提供者公司別
			'CreateName'=> $this->CreateName			//服務提供者名稱
		);
		$PostData ="[". json_encode($jsonarr)."]";
		$url = 'https://appservice.quantatw.com/API/PortalMessage/';
		$result=self::postData($url,$PostData);
		return $result;
	}

	public function postData($url,$data)
	{				
		if(!$url || !$data) return false;
		
		$ch = curl_init();
		//這行請參考 http://curl.haxx.se 的介紹
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($curl, CURLOPT_CAPATH, "/certificate");
		//curl_setopt($curl, CURLOPT_CAINFO, "/certificate/server.crt");
		//authentication
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		//curl_setopt($ch, CURLOPT_USERPWD, 'quanta\tds:James&Amy');
		//curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-type: application/json;charset=utf-8'));
		//設定CURLOPT_POST 為 1或true，表示要用POST方式傳遞
		curl_setopt($ch, CURLOPT_POST, true);
		//CURLOPT_POSTFIELDS 後面則是要傳接的POST資料。
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		$output = curl_exec($ch);
		curl_close($ch);
		//echo curl_error();
		return $output;
		//echo print_r(json_decode($output));
	}
}
?>