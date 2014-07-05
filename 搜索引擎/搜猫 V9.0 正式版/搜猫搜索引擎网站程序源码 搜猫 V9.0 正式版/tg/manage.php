<?php

require_once('global.php');
is_login();
$pagetitle='推广管理';
$action=HtmlReplace($_REQUEST['action']);
switch($action)
{
case 'saveaddlinks':
$do_action=trim(HtmlReplace($_POST['do_action']));
$keywords=trim(HtmlReplace($_POST['keywords']));
$title=trim(HtmlReplace($_POST['title']));
$url=trim(HtmlReplace($_POST['url']));
$pic=trim(HtmlReplace($_POST['pic']));
$description=trim(HtmlReplace($_POST['description']));
$price=trim(HtmlReplace($_POST['price']));
if(empty($price) ||($price<get_qijia($key['keywords'])) ||($price>$user['points']))
{
header('location:manage.php?msg='.urlencode($key['keywords'].' 出价不能低于起价或者你的积分不足以参加竞价！'));
exit();
break;
}
else if($do_action=='savemodify')
{
$link_id=intval($_POST['link_id']);
$array=array('keywords'=>$keywords,'title'=>$title,'url'=>$url,'pic'=>$pic,'description'=>$description,'updatetime'=>time());
$db->update('ve123_zz_links',$array,"link_id='".$link_id."'");
}
else
{
$array=array('keywords'=>$keywords,'title'=>$title,'url'=>$url,'pic'=>$pic,'description'=>$description,'price'=>$price,'user_id'=>$user['user_id'],'updatetime'=>time());
$db->insert('ve123_zz_links',$array);
}
header('location:manage.php?');
break;
case 'updateprice':
$arrPrice=$_POST['arrPrice'];
foreach($arrPrice as $link_id=>$price)
{
$key=$db->get_one("select * from ve123_zz_links where link_id='".$link_id."'");
if(get_qijia($key['keywords'])>$price)
{
header('location:manage.php?msg='.urlencode($key['keywords'].' 出价不能低于起价！'));
exit();
}
$array=array('price'=>$price,'updatetime'=>time());
$db->update('ve123_zz_links',$array,"link_id='".$link_id."' and user_id='".$user['user_id']."'");
}
header('location:manage.php?msg='.urlencode('更新成功！'));
break;
case 'del':
$link_id=intval($_GET['link_id']);
$db->query("delete from ve123_zz_links where link_id='".$link_id."' and user_id='".$user['user_id']."'");
header('location:manage.php?msg='.urlencode('删除成功！'));
break;
}
;echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<title>首页--';echo $pagetitle;;echo '</title>
        <link type="text/css" rel="stylesheet" media="all" href="images/global.css" />  
	    <style type="text/css">
<!--
.STYLE1 {
	color: #0000FF;
	font-weight: bold;
}
-->
        </style>
</head>
	<body>
';
headhtml();
;echo '	  
<div class="wrapper">
<div id="msg" style=\'color:#f00;text-align:center;\'>
	        ';
$msg=HtmlReplace($_GET['msg']);
if(empty($msg))
{
echo '';
}
else
{
echo $msg;
}
;echo '</div>
<table border=0 cellspacing=1 cellpadding=3 class="tablebg">
	<form name="form3" method="post" action="manage.php">  
	<input type="hidden" name="action" value="updateprice">
  <tr>
    <th>序号</th>
    <th>关 键 词</th>
    <th>&nbsp;</th>
    <th>关键词状态</th>
    <th>起价</th>
    <th>出价模式</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>点击次数</th>
    <th>点击平均价</th>
    <th>消费金额</th>
    <th width="50">&nbsp;</th>
  </tr>
    <tr>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit4" value="更新出价">
		</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  ';
$query=$db->query("select * from ve123_zz_links where user_id='".$user['user_id']."'");
$j=0;
while($link=$db->fetch_array($query))
{
$j++;
;echo '
  <tr>
    <td align="center">';echo $j;;echo '</td>
    <td>';echo "<a href=\"?action=modify&link_id=".$link['link_id']."\">".$link['keywords'].'</a>';;echo '</td>
    <td><a target="_blank" href="../s?wd=';echo urlencode($link['keywords']);;echo '">搜索</a></td>
    <td>&nbsp;</td>
    <td>';echo get_qijia($link['keywords']);;echo '&nbsp;积分</td>
    <td><input type="text" name="arrPrice[';echo $link['link_id'];;echo ']" value="';echo $link['price'];;echo '"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>';echo $link['stat_click'];;echo '</td>
    <td>&nbsp;</td>
    <td>';echo $link['consumption'];;echo '</td>
    <td><a href="?action=del&link_id=';echo $link['link_id'];echo '" onclick="if(!confirm(\'确认删除码?\')) return false;">删除</a></td>
  </tr>

  ';
}
;echo '    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit3" value="更新出价"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  </form>
</table>

    <br>
';
if($action!='step_2')
{
if($action=='modify')
{
$link_id=intval($_GET['link_id']);
$action_value='saveaddlinks';
$do_action='savemodify';
$title=$bt_txt='修改关键词';
$link=$db->get_one("select * from ve123_zz_links where link_id='".$link_id."' and user_id='".$user['user_id']."'");
}
else
{
$action_value='step_2';
$title='添加关键词';
$bt_txt='下一步';
}
;echo '    <table width="100%" border="0" cellpadding="3" cellspacing="1" class="tablebg">
	<form name="form1" method="post" action="manage.php">
	<input type="hidden" name="action" value="';echo $action_value;;echo '">
	<input type="hidden" name="do_action" value="';echo $do_action;;echo '">
	<input type="hidden" name="link_id" value="';echo $link_id;;echo '">
      <tr>
        <th colspan="2">';echo $title;;echo '</th>
      </tr>
      <tr>
        <td width="100">关 键 词</td>
        <td><input name="keywords" type="text" size="80" value="';echo $link['keywords'];;echo '">
        最多32字</td>
      </tr>
      <tr>
        <td>网页标题</td>
        <td><input name="title" type="text" size="80" value="';echo $link['title'];;echo '">
        最多20字 </td>
      </tr>
      <tr>
        <td>URL 地址</td>
        <td><input name="url" type="text" size="80" value="';echo $link['url'];;echo '">
        最多248字节</td>
      </tr>
      <tr>
        <td>网页描述</td>
        <td><textarea name="description" cols="80" rows="8">';echo $link['description'];;echo '</textarea>
        最多100字';echo $user['points'];;echo '</td>
      </tr>
      <tr>
        <td>&nbsp;<input type="hidden" name="price" value="';echo $link['price'];;echo '"></td>
        <td><input type="submit" name="Submit" value="';echo $bt_txt;;echo '"></td>
      </tr>
	  </form>
    </table>
';
}
elseif($action=='step_2')
{
$keywords=trim(HtmlReplace($_POST['keywords']));
$title=trim(HtmlReplace($_POST['title']));
$url=trim(HtmlReplace($_POST['url']));
$description=trim(HtmlReplace($_POST['description']));
;echo '   <table width="100%" border="0" cellpadding="3" cellspacing="1" class="tablebg">
   <form name="form2" method="post" action="manage.php">
   <input type="hidden" name="action" value="saveaddlinks">
   <input type="hidden" name="keywords" value="';echo $keywords;;echo '">
   <input type="hidden" name="title" value="';echo $title;;echo '">
   <input type="hidden" name="url" value="';echo $url;;echo '">
   <input type="hidden" name="pic" value="';echo $pic;;echo '">
   <input type="hidden" name="description" value="';echo $description;;echo '">
      <tr>
        <th>关键词</th>
        <th>起价</th>
        <th>出价</th>
      </tr>
      <tr>
        <td>';echo $keywords;;echo '</td>
        <td>';echo get_qijia($keywords);;echo '</td>
        <td><input type="text" name="price"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><input type="submit" name="Submit2" value="提交"></td>
      </tr>
     </form>
    </table>
';
}

?>