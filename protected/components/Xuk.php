<?php
class Xuk
{
    //get item links, insert db
	public static function getList()
	{
		$id=isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 1;
        $id=($id<1) ? 1 : $id;

        $expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 300;
        $src='http://xuk.ru/'.$id.'.html';

        $html='';
        $page=Tools::OZCurl($src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200))
            $html=$page['Result'];
//        pd($html);

        //ex: <a href="http://xuk.ru/teen/kapris-7/vid-1.html">
        preg_match_all("'<a\s+href=([\"\']?)http:\/\/xuk\.ru\/([^\/]*?)\/([^\/]*?)\/vid-1\.html\\1>'isx", $html, $links);
//        pr($links);

        
	}
	
    //get items,insert db
	public function getItem()
	{
		
	}

    //get images, make small images, insert db
	public function getImage()
	{
		
	}
}