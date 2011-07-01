<?php
/**
 * Created by LJ.
 * User: xxer.info
 * Date: 6/30/11
 * Time: 10:01 AM
 */

$var="
var pid=parseInt(".$pid.");
var dm='".$dm."';
";

$run="
function redo(data){
//	location.reload();
}
function apido(){
	jQuery.ajax({
	    'url':dm+'/wp-admin/admin-ajax.php',
	    'complete':redo,
	    'type':'post',
	    'dataType':'json',
	    'contentType':'application/x-www-form-urlencoded',
	    'data':{
	        '_wpnonce':'198eceae35',
	        'action':'ngg_ajax_operation',
	        'image':pid,
	        'operation':'create_thumbnail'
	    },
	    'cache':false
	});
}
";


$js=$var.$run;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');
$packer = new JavaScriptPacker($js, 'Normal', true, false);
$packed = $packer->pack();

$cs->registerScript('items', $packed, CClientScript::POS_END);
$cs->registerScript('items', 'apido();', CClientScript::POS_READY);
?>

<style>
#links{
	font-family: 'Microsoft YaHei',arial,Verdana,helvetica,clean,sans-serif;
	font: 13px arial,helvetica,clean,sans-serif;
	font-size: 14px;
	font-style: normal;
    font-weight: normal;
	background-color: #E1ECFE;
	display: inline-block;
}
#links .item{
	line-height: 1.22em;
	border-style: solid;
    border-width: 1px;
	text-align: left;
	color: #494949;
	background-color: #E9FFF0;
    border-color: #A9F5E3;
    margin: 4px 5px;
    padding: 1px 5px 2px;
	float: left;
	display: inline-block;
	width: 355px;
}
#links .right{
	line-height: 1.22em;
	border-style: solid;
    border-width: 1px;
	text-align: left;
	color: green;
	background-color: #F1F5FA;
    border-color: #CCCCCC;
    margin: 5px;
    padding: 5px;
    display: inline-block;
}
#links .have{
	line-height: 1.22em;
	border-style: solid;
    border-width: 1px;
	text-align: left;
	color: #494949;
	background-color: #EEEEEE;
    border-color: #CCCCCC;
    margin: 2px 10px;
    padding: 1px 5px 2px;
}
#links .error{
	line-height: 1.22em;
	border-style: solid;
    border-width: 1px;
	text-align: left;
	color: red;
	background-color: #EEEEEE;
    border-color: #CCCCCC;
    margin: 5px;
    padding: 5px;
    display: inline-block;
}
#links .loading{
	background: url("/css/loading.gif") no-repeat scroll 0 0 transparent;
}
</style>

<div id="links"></div>
