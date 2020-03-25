<?
header( 'Content-Type: text/html; charset=utf-8' );
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$sno=$_REQUEST['sno'];

if($sno)
{
	$cn="";
	$strSql="SELECT pno,pname,pprice,ppic,psize,psuit,pgood,pnormal,pbad,prater,cname FROM ttprodtb AS P INNER JOIN ttcatetb AS C ON P.sno=C.sno AND P.cno=C.cno WHERE P.sno=$sno ORDER BY P.sno,C.cno";
	$data_rows=mysql_query($strSql,$link);
	$tt=mysql_num_rows($data_rows);
	$k=0;
	echo "<div id='selpddiv'></div>";
	while(list($pno,$pname,$pprice,$ppic,$psize,$psuit,$pgood,$pnormal,$pbad,$prater,$cname)=mysql_fetch_row($data_rows))
	{
		$pricestr .=$pprice."-";
		if($ppic=="") $ppicstr="&nbsp;"; else $ppicstr="<img class='tonus' src='prodpic/$ppic' width='20' height='20' />";
		if($cn!=$cname && $cn!="") echo "</table></div>";
		if($cn!=$cname || $cn=="") {
			echo "<div class='pdincate$k'><div style='font-size:14px;font-weight:bold; padding:5px 35px; text-align:left;'><img src='css/images/tree_checkbox_2.gif' />系列:$cname</div><table border='0' cellpadding='0' cellspacing='1' id='profiletb' width='99%'><tr><th title='點此全選本系列'><input class='checkcate' type='checkbox' value='$k' ></th><th>品名</th><th>大小</th><th>單價</th><th>功效</th><th>圖片</th><th>投票</th></tr>";
			$cn=$cname;
		}

		echo "<tr><td><input id='pno[]' name='pno[]' type='checkbox' value='$pno' class='$pprice'></td><td title='$pintro'>$pname</td><td>$psize</td><td>$pprice</td><td>$psuit</td><td>$ppicstr</td><td><img class='vote' id='pgood' subid='$pno' src='images/btngood.gif' alt='$pgood 票' /> <img class='vote' id='pnormal' subid='$pno' src='images/btnnormal.gif' alt='$pnormal 票' /> <img id='pbad' class='vote' subid='$pno' src='images/btnbad.gif' alt='$pbad 票' /></td></tr>";
		$k++;
		if($k==$tt) echo "</table></div>";
	}
	
	$pricearr=explode("-",$pricestr);
	sort($pricearr);
	$ppchkbox="<input class='checkall' type='checkbox' >全選 ";
	$tmppp="";
	foreach($pricearr as $pp)
	{
		if($pp!=="" && strpos("$tmppp","$pp")<=0) 
		{
			$ppchkbox .="<input class='ppsel' value='$pp' type='checkbox'>$pp 元 ";
			$tmppp .="-".$pp;
		}
	}
	echo "<script>
		$(document).ready(function(){
			$('#selpddiv').html(\"".$ppchkbox."\");
			$('.ppsel').click(function(){
				var flg=$(this).attr('checked');
				var val=$(this).val();
				if(flg)
				{
					$('#storeproduct input[class*='+val+']').attr('checked',true);
				}
				else
				{
					$('#storeproduct input[class*='+val+']').attr('checked',false);
				}
			});
			$('td:empty').html('&nbsp;');
			$('.tonus').tooltip({
				delay: 0, showURL: false, bodyHandler: function() {
				return $('<img/>').attr('src', this.src);}
			});
			$('.checkall').click(function(){
				var flg=$(this).attr('checked');
				if(flg)
					$('#storeproduct input:checkbox').attr('checked',true);
				else
					$('#storeproduct input:checkbox').attr('checked',false);
			});
			$('.checkcate').click(function(){
				var chk=$(this).val();
				var flg=$(this).attr('checked');
				if(flg)
					$('.pdincate'+chk+' input:checkbox').attr('checked',true);
				else
					$('.pdincate'+chk+' input:checkbox').attr('checked',false);
			});
			$('.vote').click(function(){
				var pno=$(this).attr('subid');
				var pratesel=$(this).attr('id');
				$.post('rateprod.php',{pno:pno,pratesel:pratesel},function(ret){
					if(ret==true) alert('投票成功'); else alert('您尚未登入,你已投過此商品');
				});
			});
			
		});
	</script>";
	
}

?>