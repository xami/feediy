<?php
class Tools
{
	public static function OZCurl($src, $expire=60, $show=false)
	{
//		$show = isset($_REQUEST['show']) ? intval($_REQUEST['show']) : false;
//		$expire = (isset($_REQUEST['expire']) && (intval($_REQUEST['expire'])>10)) ? intval($_REQUEST['expire']) : 10;
//		$src = isset($_REQUEST['src']) ? trim($_REQUEST['src']) : '';
//		if(empty($src)){
//			return false;
//		}
		
		$expire = intval($expire)>20 ? intval($expire) : 20;
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
		}
		
		return $c;
	}
	
	
	
}