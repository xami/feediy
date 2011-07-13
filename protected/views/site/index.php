<?php $this->pageTitle=Yii::app()->name;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');

?>
<style type="text/css">
#msg {
    background: none repeat scroll 0 0 #E6E6E6;
    color: #666666;;
    height: 20px;
    line-height: 20px;
    text-align: center;
    margin: 20px 0;
}
.one {
	background: url(images/circular-1.gif) top left no-repeat;
	padding-left: 30px;
	padding-bottom: 5px;
}
.two {
	background: url(images/circular-2.gif) top left no-repeat;
	padding-left: 30px;
	padding-bottom: 5px;
}
.three {
	background: url(images/circular-3.gif) top left no-repeat;
	padding-left: 30px;
	padding-bottom: 5px;
}
.four {
	background: url(images/circular-4.gif) top left no-repeat;
	padding-left: 30px;
	padding-bottom: 5px;
}
.ibox {
    background: none repeat scroll 0 0 #E5ECF9;
    border: 2px solid #CCCCCC;
    font: 14px "Trebuchet MS","Helvetica",sans-serif;
    padding: 4px;
    height: 19px;
}
.but {
    background: url("images/but.gif") repeat-x scroll 50% top #CDE4F2;
    border: 1px solid #C5E2F2;
    cursor: pointer;
    height: 30px;
    margin-left: 5px;
    width: 60px;
}

</style>

<h1>网站地图自动生成V0.1(免费)</h1>
<div id="msg">请知晓下面的事项</div>
<div class="one">可以自由分发整理的结果,请保留本站的链接以及保证内容的完整性</div>
<div class="two">程序只识别正常的html链接,忽略js生成的跳转代码</div>
<div class="three">程序采用单线程取得内容,不会对目标服务器造成很大压力</div>
<div class="four">程序会自动分析目标网站的内容并生成网站地图,不限页数,当前版本只支持两层逻辑深度</div>
<div id="msg">整理出的网站地图内容系FeeDiy根据您的指令自动整理的结果,不代表FeeDiy赞成被整理网站的内容或立场</div>

<input class="ibox" type="text" value="http://" size="63" name="initurl" id="initurl">
<input class="but" type="button" value="分析" id="st1">
<br />

    
<script type="text/javascript">
//<![CDATA[

(function($) {
    $.fn.siteMap = function(settings) {
        var url_index = new Array(2);
        var url_data = new Array(3);
        var url_depth = 0;
        var index = '';
        
        settings = jQuery.extend({
            api_url: '/tool/getlinks'
        },settings || {});
        
        this.click(function (){
            url_index[0] = new Array();
            url_index[0].push($(this).prev().val());
            _run();
        });

        var _run = function(){
            _get_url_list();
        }

        var _get_url_list = function (){
            if(url_index[url_depth]==undefined){
                url_depth++;
                if(url_index[url_depth]==undefined){
                    return false;
                }
                index = url_index[url_depth].shift();
                if(index==undefined){
                    return false;
                }
            }else{
                index = url_index[url_depth].shift();
            }
            
            jQuery.ajax({
                'url':settings.api_url,
                'success':_save_url_list,
//                'complete':_get_index,
                'dataType':'json',
                'data':{'src':index},
                'cache':false
            });

        };

        var _save_url_list = function (list){
            if(list==null || list.status==null || list.status!=200){
                return false;
            }
            if(url_depth<2){
                if(list!=null){
                    for(i=0;i<list.length;i++){
                        if(url_index[url_depth+1]==undefined){
                            url_index[url_depth+1]=new Array();
                        }
                        url_index[url_depth+1].push(list[i]);
                    }
                }
            }
            if(list!=null){
                if(url_depth==0){
                    url_data[0]=new Array();
                    url_data[0].push();
                }
                for(i=0;i<list.length;i++){
                    url_data[url_depth+1].push(list[i]);
                }
            }
            _run();
        };

    }
})(jQuery);
$('#st1').siteMap();
//]]>
</script>