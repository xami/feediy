<?php
/**
 * 
 * Enter description here ...
 * @author lijia
 *
<?php
// cURL options can be found here:
// http://php.net/manual/en/function.curl-setopt.php

require_once('Whiz/Http/Client/Curl.php');

// Set cURL options via constructor
$curl = new Whiz_Http_Client_Curl(
  array(CURLOPT_REFERER => 'http://www.google.com/')
);

// Set URL via method (This is just to make things easier)
$curl->setUrl('http://juliusbeckmann.de/');
// $curl->exec('http://juliusbeckmann.de/'); would be also possible

// Set cURL options via method
$curl->setOption(CURLOPT_TIMEOUT, 10);

// Do the request
$curl->exec();

if($curl->isError()) {
  // Error
  var_dump($curl->getErrNo());
  var_dump($curl->getError());
}else{
  // Success
  echo $curl->getResult();
  // More info about the transfer
  // var_dump($curl->getInfo());
  // var_dump($curl->getHeader());
  // var_dump($curl->getVersion());
}

// Close cURL
$curl->close();
?>
<?php

require_once('Whiz/Http/Client/Curl.php');

// Creating a "template" class by overwriting internal config
class My_Curl extends Whiz_Http_Client_Curl {
  protected $_config = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_REFERER => 'http://www.google.com/'
  );
}

$curl = new My_Curl();
$curl->setUrl('http://juliusbeckmann.de/');

// Fetch configured handle
$ch = $curl->getHandle();

// Fetch a copy of the configured handle
// $ch2 = $curl->copyHandle();

// Do with handle what ever you like
// ...
$result = curl_exec($ch);

// Put handle and result back in
$curl->setFromHandle($ch, $result);

// Fetch transfer info
if($curl->isError()) {
  // Error
  var_dump($curl->getErrNo());
  var_dump($curl->getError());
}else{
  // Success
  echo $curl->getResult();
  // More info about the transfer
  // var_dump($curl->getInfo());
  // var_dump($curl->getHeader());
  // var_dump($curl->getVersion());
}

// Close cURL
$curl->close();
?>
 */

class ToolController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionIn2Out()
	{
		$data=array();
		
		if(isset($_REQUEST['in'])){
			$data['in'] = empty($_REQUEST['in']) ? '' : trim($_REQUEST['in']);
			if(strlen($data['in'])>0){
				$out = unserialize($data['in']);
				$tmp='';
				if(!empty($out)){
					foreach($out as $k => $v){
						$tmp.=$k.': '.$v."\r\n";
					}
				}
				$data['out']=$tmp;
			}
		}
		
		$this->render('in2out', $data);
	}
	
	public function actionGet()
	{
		$show = isset($_REQUEST['show']) ? true : false;
		$expire = isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 10;
		$src = isset($_REQUEST['src']) ? trim($_REQUEST['src']) : '';
		
		$o = Tools::OZCurl($src, $expire, $show);
	}

    public function actionXuk()
	{
        //ig: http://www.feediy.com/index.php?r=tool/xuk&op=image

        $op=trim(strtolower(Yii::app()->request->getParam('op', 'list')));
        $id=intval(Yii::app()->request->getParam('id', 1));
        $id=($id<1 || $id>1000) ? 1 : $id;

        if (Yii::app()->request->isAjaxRequest) {
            if($op=='list'){
                //get gallery list
//                $id=isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 1;
                if(Xuk::getList($id)==true){
                    echo json_encode(array('responseStatus'=>200));
                }else{
                    echo json_encode(array('responseStatus'=>500));
                }
            }
            if($op=='item'){
                //get gallery
                if(Xuk::getItem()==true){
                    echo json_encode(array('responseStatus'=>200));
                }else{
                    echo json_encode(array('responseStatus'=>500));
                }
            }
            if($op=='image'){
                //down image
                if(Xuk::getImage()==true){
                    echo json_encode(array('responseStatus'=>200));
                }else{
                    echo json_encode(array('responseStatus'=>500));
                }
            }
            if($op=='post'){
                //new post
                if(Xuk::postGallery()==true){
                    echo json_encode(array('responseStatus'=>200));
                }else{
                    echo json_encode(array('responseStatus'=>500));
                }
            }
            if($op=='publish'){
                //new post
                if(Xuk::publishPost()==true){
                    echo json_encode(array('responseStatus'=>200));
                }else{
                    echo json_encode(array('responseStatus'=>500));
                }
            }
        }else{
            $this->layout='//layouts/simple';
            $this->render('do', array('id'=>$id, 'op'=>$op));
        }
	}

    public function actionThumb()
    {
        //ig: http://www.feediy.com/index.php?r=tool/thumb&once=
        if(($info=Xuk::createThumbnail())!==false){
            $this->layout='//layouts/click';
            $this->render('ajaxcdr', $info);
        }
    }

    public function actionGetlinks()
    {
        $expire = isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 3000;
		$src = isset($_REQUEST['src']) ? trim($_REQUEST['src']) : '';

        $page=Tools::OZCurl($src, $expire);
        if($page==false){
            echo json_encode(array('status'=>500,'data'=>''));
            die;
        }
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            echo json_encode(array('status'=>500,'data'=>''));
            die;
        }

        if($page['Info']['content_type']!='text/html'){
            echo json_encode(array('status'=>500,'data'=>''));
            die;
        }



        preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\']?)(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$html,$links);
//        pr($links);
        if(empty($links)){
            echo json_encode(array('status'=>500,'data'=>''));
        }else{
            unset($src_info);
            $src_info=parse_url($src);
//            pr($src_info);
            $links_full=array();
            foreach($links[2] as $link){
                $link=trim($link);
                unset($link_info);
                if(strpos($link, 'http://')>0){
                    $links_full[]=$src_info['scheme'].'://'.$src_info['host'].'/'.$link;
                    continue;
                }
                if($link_info['scheme']=='mailto' || $link_info['scheme']=='javascript'){
                    continue;
                }

                $link_info=parse_url($link);

                if(!isset($link_info['path'])){
                    $link_info['path']='';
                }
                if(!isset($link_info['query'])){
                    $link_info['query']='';
                }
                if(!empty($link_info['query'])){
                    $link_info['query']='?'.$link_info['query'];
                }
//                pr($link_info);
                if(strpos($link_info['path'], '/')!==0){
                    if(strpos($src_info['path'], '/')!==0){
                        $src_info['path']='/'.$src_info['path'];
                    }
                    if(substr($src_info['path'], -1, 1)!=='/'){
                        $src_info['path']=$src_info['path'].'/';
                    }
                    $link_info['path']=$src_info['path'].$link_info['path'];
                }

//                if(strpos($link_info['path'],'api')!==false){
//                    continue;
//                }

                if(!isset($link_info['scheme']) || empty($link_info['scheme']) || !isset($link_info['host']) || empty($link_info['host'])){
                    $links_full[]=$src_info['scheme'].'://'.$src_info['host'].$link_info['path'].$link_info['query'];
                }else{
                    //不是当前域名下的链接跳过
                    if(($src_info['scheme']!=$link_info['scheme']) || ($src_info['host']!=$link_info['host'])){
                        continue;
                    }
                    $links_full[]=$link_info['scheme'].'://'.$link_info['host'].$link_info['path'].$link_info['query'];
                }

                
            }
            $links_full=array_unique($links_full);
            //过滤array_unique引起的空白索引
            $ct_link=array();
            if(!empty($links_full)){
                foreach($links_full as $the_link){
                    $ct_link[]=html_entity_decode($the_link);
                }
            }
//            pr($ct_link);
            echo json_encode(array('status'=>200,'count'=>count($links_full),'data'=>$ct_link));
        }

    }

    public function actionTest()
	{
//        var_dump(substr('.afas', 0, 1)!='.') ;
//		echo MCrypy::decrypt('DP9gh8NxCU7dIuk0teVguS5fM5Pzv4ACdDswFgkH8yWUAC+GMqTRp+33XeLbSesX8JsKdV5ZJvdTvlm1V0zNjNP85/xS5UcYn6j4IxsB', Yii::app()->params['xuk_pass'], 128);
//        echo '<br>';
//        echo strlen('_wpnonce=198eceae35&action=ngg_ajax_operation&image=501&operation=create_thumbnail');
        pr(parse_url('link.php?url=http://news.orzero.com'));
        $mystring = 'abc';
        $findme   = 'a';
        echo $pos = strpos($mystring, $findme);

	}
	

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}