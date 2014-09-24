<?php

error_reporting(E_ALL ^ E_DEPRECATED);
$con=mysql_connect("localhost","root","");
	mysql_select_db("aaa");
						
$i = 0;
$select_tbl=mysql_query("select * from rparents",$con);
while($fetch=mysql_fetch_array($select_tbl))
{
$contact[$i]=$fetch['contact'];
$i++;
}
$comma_sep = implode(",",$contact);
echo $comma_sep;
?>