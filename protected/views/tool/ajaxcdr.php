<?php
/**
 * User: lijia
 * Date: 6/30/11
 * Time: 4:42 PM
 */
 ?>
<form name="update_thumb">
<input name="_wpnonce" type="hidden" value="<?php echo $once; ?>" />
<input name="action" type="hidden" value="ngg_ajax_operation" />
<input name="image" type="hidden" value="<?php echo $pid; ?>" />
<input name="operation" type="hidden" value="create_thumbnail" />
</form>

<script type="text/javascript">
function run(){
    AjaxCrossDomainRequest('<?php echo $dm; ?>/wp-admin/admin-ajax.php', 'POST', 'update_thumb', 'mycallback()');
}
function mycallback(){
    if(AjaxCrossDomainResponse==1){
        location.reload();
    }else{
        alert('error');
    }
}
</script>
<?php
$cs=Yii::app()->clientScript;
//$cs->registerCoreScript('jquery');
//$cs->registerScript('update_thumb', "AjaxCrossDomainRequest('".$dm."/wp-admin/admin-ajax.php', 'POST', 'update_thumb', 'mycallback()');", CClientScript::POS_READY);
$cs->registerScriptFile("/js/ajaxcdr/ajaxcdr.js", CClientScript::POS_END);
//$cs->registerScript('update_thumb', '$("#to_run").get(0).click();', CClientScript::POS_READY);
?>
