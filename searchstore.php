<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
//if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']=="") exit;

//header( 'Content-Type: text/html; charset=utf-8' );

require_once "conn.php";
$thecity=$_REQUEST['city'];
$thecounty=$_REQUEST['county'];
$srchtxt=$_REQUEST['srchtxt'];

$page_count=12; //每頁設定顯示筆數

if($thecity!="") {$scity=" AND scity='$thecity'"; $page_count=28;}
if($thecounty!="") $scounty=" AND scounty='$thecounty'";
if($srchtxt!="") $srchtxt=" AND CONCAT_WS(sname,sintro,scity,scounty,saddr,sdispatch,stype,stel,sfax) LIKE '%$srchtxt%' ";
$snamestring="$scity $scounty $srchtxt";

$i=0;
$maxcol=4;
$maxstore=48;

$strSql="SELECT COUNT(*) FROM ttstoretb WHERE 1 $snamestring ORDER BY sname LIMIT $maxstore";
$sql_data_count=mysql_query($strSql); 
$row = mysql_fetch_array($sql_data_count); 
$storecnt=$row[0]; //stores count
//$page_total=intval($storecnt/$page_count);  //算出總共有多少頁
//if ($storecnt % $page_count) $page_total++; //有餘數加一頁
$page_total=ceil($storecnt/$page_count); //取上限
if (isset($_REQUEST['page'])&& $_REQUEST['page']!="") $page=intval($_REQUEST['page']); else $page=1;  //GET取得page頁數，若沒有則跑第1頁



$move=$page_count * ($page - 1); //資料移動筆數 ,sno=43 is teatime system
$strSql="SELECT sno,sname,spic,stel,saddr,sintro,sdispatch,stype,surl FROM ttstoretb WHERE 1 AND sno<>43 $snamestring ORDER BY lastupdate desc,sname LIMIT ";
$sql_data_move=mysql_query("$strSql $move,$page_count"); 

$tt=mysql_num_rows($sql_data_move);
/* show by ul,li
while(list($sno,$storename,$storepic,$storetel,$storeaddr) = mysql_fetch_row($sql_data_move))
{
	if($i%$maxcol==0) echo "<ul>";
	echo "<li><img class='tonus' alt='電話:$storetel\n地址:$storeaddr' src='storepic/$storepic' width='35' height='25' /><input type='radio' id='sno[]' name='sno[]' value='$sno' /><label>$storename</label></li>";

	if($i%$maxcol==2 || $i==$tt-1) echo "</ul>";
	$i++;
}
*/
echo "<center><table border='0' cellspacing='1' cellpadding='0' width='99%'>";
while(list($sno,$storename,$storepic,$storetel,$storeaddr,$sintro,$sdispatch,$stype,$surl) = mysql_fetch_row($sql_data_move))
{
	if($surl!="") $surlstr="<a href='$surl' target='_blank' title='參訪本網站$surl'><img src='storepic/$storepic' width='35' height='25' /></a>";
	else $surlstr="<img src='storepic/$storepic' width='35' height='25' />";
	 if($i%$maxcol==0) echo "<tr>";
 	echo "<td align='left' bgcolor='#F6F6F6'>$surlstr<input type='radio' class='sno' id='sno[]' name='sno[]' value='$sno' /><span class='tonus' title='<img src=storepic/$storepic width=120 height=80><br>網址:$surl<br>類型:$stype<br>電話:$storetel<br>地址:$storeaddr<br>送達地區:$sdispatch<br>簡介:$sintro'>$storename</span></td>";
					
	if($i%$maxcol==($maxcol-1) || $i==($tt-1))  echo "</tr>";
	$i++;
}
echo "</table></center>";

function showpages($page,$page_total)
{
	
	for($k=1;$k<=$page_total;$k++)
	{
		if($page==$k)
			$ps .=" <a href='javascript:void(0);' title='$k' style='color:#ffffff;background-color:#69f'>$k</a> ";
		else
			$ps .=" <a href='javascript:void(0);' title='$k' style='border:solid 1px #cccccc;'>$k</a> ";
	}
	return $ps;
}

if($tt>0)
{
	if(($page-1)<1) $prevpage=1; else $prevpage=$page-1;
	if(($page+1)>$page_total) $nextpage=$page_total; else $nextpage=$page+1;
	echo "<div id='pagediv' style='margin:20px;text-align:center;'>【<a href='javascript:void(0);' title='1'>最前</a>】【<a href='javascript:void(0);' title='".$prevpage."'>上一頁</a>】".showpages($page,$page_total)."【<a href='javascript:void(0);' title='".$nextpage."'>下一頁</a>】【<a href='javascript:void(0);' title='$page_total'>最後</a>】(共 $tt 筆)</div>";
}
else
	echo "<div style='text-align:center'>...沒有找到相關店家資料...</div>";
 
?>
<script>
$(document).ready(function(){
	//show image tip
	$('.tonus2').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	$('.tonus').tooltip();
	//$("input[value='"+$.cookie('teatimenowsno')+"']").attr('checked',true);
	var poty=$('#poty').val();

	$('#pagediv a').click(function(){
		var pageno=$(this).attr('title');
		var searchtxt=$('#searchtxt').val();
		if(poty=="pd")
			$.post("searchstore.php",{srchtxt:searchtxt,page:pageno}, function(sdata){$('#thestores').html(sdata);});
		else if(poty=="gm")
		{
			var thecity=$('.thecity option:selected').val();
			var thecounty=$('.thecounty option:selected').val();
			$.post("searchstore.php",{srchtxt:searchtxt,page:pageno,city:thecity,county:thecounty}, function(sdata){$('#thestores').html(sdata);});
		}
	});

	$('.sno').click(function(){
		var sno=$(this).val();

		if(poty=="st") //store detail
		{
			$.post("getstoredata.php",{sno:sno}, function(sdata){						  
			var storedata = sdata.split("|");
				if(storedata.length){
					$('#storename').val(storedata[0]);
					$('#storetel').val(storedata[1]);
					$('#storefax').val(storedata[2]);
					$('#storeurl').val(storedata[3]);
					$('#storeaddr').val(storedata[6]);
					$('#storetype').val(storedata[7]);
					$('#sdispatch').val(storedata[8]);
					$('#stpicimg').attr('src','storepic/'+storedata[9]);
					$('#stpich').val(storedata[9]); //hidden image
					$('#storeintro').text(storedata[10]);
					$('#addrcontainer').twzipcode({countySel:storedata[4],areaSel:storedata[5]});
				}
			});
		}
		else if(poty=="pd") //products
		{
			$.post("getprodcate.php",{sno:sno}, function(cdata){$('#thecates').html(cdata);});
		}
		else if(poty=="gm") //create an order
		{
			$.post("storeprofile.php",{sno:sno}, function(sdata){$('#storeprofile').html(sdata);});
			$.post("storeproduct.php",{sno:sno}, function(pdata){$('#storeproduct').html(pdata);});
		}
		else if(poty=="dw") //download products
		{
			location.href="downprod.php?sno="+sno;
		}
		else if(poty=="sc") //store copy
		{
			$('#newstorename').val($(this).next('.tonus').text()+usefloor(1,99));
			$('#sc').show().window('open');
			
			$('a[iconCls="icon-ok"]').click(function(){
				var nsname=$('#newstorename').val();
				$.post("storecopy.php",{sno:sno,nsname:nsname}, function(err){
					(err==0)?alert('已成功複製'):alert('複製失敗');
					$('#sc').window('close');
				});
			});
			$('a[iconCls="icon-cancel"]').click(function(){$('.easyui-window').window('close');});
		}
	});		
	
	
});
function usefloor(min,max) {
	return Math.floor(Math.random()*(max-min+1)+min);
}
</script>