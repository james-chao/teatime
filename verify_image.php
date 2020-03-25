<?php
session_start();   // if header already send, change output_buffering = On at php.ini.

$vi = new vCodeImage();
$vi -> SetImage(2,1,120,50,120,1);

class vCodeImage
{
	var $mode;			// 1.文字模式, 2.字母模式,3.文字字母混合模式, 4.其他文字字母優化模式
	var $v_num;			// 驗證碼個數
	var $img_w;			// 圖像寬度
	var $img_h;			// 圖像高度
	var $int_pixel_num; // 干擾像數個數
	var $int_line_num;	// 干擾線條數量
	var $font_dir;		// 字型文件路徑
	var $border;		// 圖像邊框
	var $borderColor;	// 圖像邊框顏色

	public function SetImage($mode, $v_num, $img_w, $img_h, $int_pixel_num, $int_line_num, $font_dir='font', $border=false, $borderColor='0,0,0')
	{

		if(!isset($_SESSION['vCode']) && function_exists("session_register")){
				session_register('vCode');
		}
		$_SESSION['vCode'] = "";

		$this -> mode = $mode;
		$this -> v_num = $v_num;
		$this -> img_w = $img_w;
		$this -> img_h = $img_h;
		$this -> int_pixel_num = $int_pixel_num;
		$this -> int_line_num = $int_line_num;
		$this -> font_dir = $font_dir;
		$this -> border = $border;
		$this -> borderColor = $borderColor;
		$this -> GenerateImage();

	}

	public function GetChar($mode)
	{
		if($mode == "1"){
			$ychar = "0,1,2,3,4,5,6,7,8,9";
		}else if($mode == "2"){
			$ychar = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
		}else if($mode == "3"){
			$ychar = "0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
		}else{
			$ychar = "3,4,5,6,7,8,9,a,b,c,d,h,k,p,r,s,t,w,x,y";
		}
		return $ychar;
	}
 
	public function RandColor($rs, $re, $gs, $ge, $bs, $be)
	{
		$r = mt_rand($rs, $re);
		$g = mt_rand($gs, $ge);
		$b = mt_rand($bs, $be);
		return array($r, $g, $b);
	}
 
	public function GenerateImage()
	{
		$fonts = scandir($this -> font_dir);
		$ychar = $this -> GetChar($this -> mode);
		$list = explode(",", $ychar);
		$cmax = count($list) - 1;
		$fmax = count($fonts) - 2;
		$fontrand = mt_rand(2, $fmax);
		$font = $this -> font_dir."/".$fonts[$fontrand];
		
		// 驗證碼
		$v_code = "";
		for($i = 0; $i < $this-> v_num; $i++){	
			$randnum = mt_rand(0, $cmax);
			$this_char = $list[$randnum];
			$v_code .= $this_char;
		}
		// 扭曲圖形		

		$im = imagecreatetruecolor ($this -> img_w + 50, $this -> img_h);
		$color = imagecolorallocate($im, 32, 81, 183);
		$ranum = mt_rand(0, 2);
		if($ranum == 0){
			$color = imagecolorallocate($im, 32, 81, 183);
		}else if($ranum == 1){
			$color = imagecolorallocate($im, 17, 158, 20);
		}else{
			$color = imagecolorallocate($im, 196, 31, 11);
		}
		
		imagefill($im, 0, 0, imagecolorallocate($im, 255, 255, 255) );
		imagettftext ($im, 24, mt_rand(-6, 6), 10, $this -> img_h * 0.6, $color, $font, $v_code);

		// 干擾線條
		for($i = 0; $i < $this -> int_line_num; $i++){
			$rand_color_line = $color;
			imageline($im, mt_rand(2,intval($this -> img_w/3)), mt_rand(10,$this -> img_h - 10), mt_rand(intval($this -> img_w - ($this -> img_w/3) + 50),$this -> img_w), mt_rand(0,$this -> img_h), $rand_color_line);
		}

		$ranum = mt_rand(0, 1);
		$dis_range = mt_rand(8, 12);
		$distortion_im = imagecreatetruecolor ($this -> img_w * 1.5 ,$this -> img_h);        
		imagefill($distortion_im, 0, 0, imagecolorallocate($distortion_im, 255, 255, 255));
		for ($i = 0; $i < $this -> img_w + 50; $i++) {
			for ($j = 0; $j < $this -> img_h; $j++) {
				$rgb = imagecolorat($im, $i, $j);
				if($ranum == 0){
					if( (int)($i+40+cos($j/$this -> img_h * 2 * M_PI) * 10) <= imagesx($distortion_im) && (int)($i+20+cos($j/$this -> img_h * 2 * M_PI) * 10) >=0 ) {
						imagesetpixel ($distortion_im, (int)($i+10+cos($j/$this -> img_h * 2 * M_PI - M_PI * 0.4) * $dis_range), $j, $rgb);
					}
				}else{
					if( (int)($i+40+sin($j/$this -> img_h * 2 * M_PI) * 10) <= imagesx($distortion_im) && (int)($i+20+sin($j/$this -> img_h * 2 * M_PI) * 10) >=0 ) {
						imagesetpixel ($distortion_im, (int)($i+10+sin($j/$this -> img_h * 2 * M_PI - M_PI * 0.4) * $dis_range), $j, $rgb);
					}
				}
			}
		}

		// 干擾像素
		for($i = 0; $i < $this -> int_pixel_num; $i++){
			$rand_color_pixel = $color;
			imagesetpixel($distortion_im, mt_rand() % $this -> img_w + 20, mt_rand() % $this -> img_h, $rand_color_pixel);
		}

		// 繪製邊框
		if($this -> border){
			$border_color_line = $color;
			imageline($distortion_im, 0, 0, $this -> img_w, 0, $border_color_line); // 上橫
			imageline($distortion_im, 0, 0, 0, $this -> img_h, $border_color_line); // 左豎
			imageline($distortion_im, 0, $this -> img_h-1, $this -> img_w, $this -> img_h-1, $border_color_line); // 下橫
			imageline($distortion_im, $this -> img_w-1, 0, $this -> img_w-1, $this -> img_h, $border_color_line); // 右豎
		}

		//imageantialias($distortion_im, true); // 消除鋸齒
	
		//exit;
		//$time = time();
		//$_SESSION['vCode'] = $v_code."|".$time; // 把驗證碼與時間賦與給 $_SESSION[vCode], 時間欄位可以驗證是否超時
		$_SESSION['vCode'] = $v_code;
		setcookie("teatimevcode",$v_code,time() + 24 * 60 * 60);
		// 生成圖像給瀏覽器
		
		//if(function_exists("imagedestroy")) return "YES".$distortion_im ; else return "NO".$distortion_im;
		if (function_exists("imagegif")) {
			header ("Content-type: image/gif");
			imagegif($distortion_im);
		}else if (function_exists("imagepng")) {
			header ("Content-type: image/png");
			imagepng($distortion_im);
		}else if (function_exists("imagejpeg")) {
			header ("Content-type: image/jpeg");
			imagejpeg($distortion_im, "", 80);
		}else if (function_exists("imagewbmp")) {
			header ("Content-type: image/vnd.wap.wbmp");
			imagewbmp($distortion_im);
		}else{
		  die("No Image Support On This Server !");
		}

		imagedestroy($im);
		imagedestroy($distortion_im);
	}
}
?>