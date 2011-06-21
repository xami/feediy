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
		
		$o = $this->OZCurl($src, $expire, $show);
	}
	
	
	public function OZCurl($src, $expire=60, $show=false)
	{
//		$show = isset($_REQUEST['show']) ? intval($_REQUEST['show']) : false;
//		$expire = (isset($_REQUEST['expire']) && (intval($_REQUEST['expire'])>10)) ? intval($_REQUEST['expire']) : 10;
//		$src = isset($_REQUEST['src']) ? trim($_REQUEST['src']) : '';
//		if(empty($src)){
//			return false;
//		}
		
		$expire = intval($expire)>10 ? intval($expire) : 10;
		$src = trim($src);
		if(empty($src)) return false;
		
		$c = null;
		$key = md5($src);
		$cache = Yii::app()->cache;
		$c=$cache->get($key);
		
		if(empty($c)){
			//Run curl
			$curl = Yii::app()->CURL;
			$curl->run(array(CURLOPT_REFERER => $src));
			$curl->setUrl($src);
			$curl->exec();
			
			if(Yii::app()->CURL->isError()) {
				// Error
				var_dump($curl->getErrNo());
				var_dump($curl->getError());
				
			}else{
				// More info about the transfer
				$c=array(
					'ErrNo'=>$curl->getErrNo(),
					'Error'=>$curl->getError(),
					'Header'=>$curl->getHeader(),
					'Info'=>$curl->getInfo(),
					'Result'=>$curl->getResult(),
				);
			}
			$cache->set($key, $c, $expire);
		}
		
		if($show==true){
			if(!empty($c['Info']['content_type']))
				header('Content-Type: '.$c['Info']['content_type']);
			if($c['Info']['http_code']==200)
				echo $c['Result'];
		}else{
			return $c;
		}
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