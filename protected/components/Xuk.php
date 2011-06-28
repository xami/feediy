<?php
class Xuk
{
    protected static $WPPATH='';
//    protected $xuk_pass='';
    //get item links, insert db
	public static function getList()
	{
		$id=isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 1;
        $id=($id<1) ? 1 : $id;

        $expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 30;
        $src='http://xuk.ru/'.$id.'.html';

        $html='';
        $page=Tools::OZCurl($src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            return false;
        }

        //get album name
        preg_match_all("'vid-2\.html\"\s+class=\"contentheading0\">([^<]*?)<\/a>'isx", $html, $names);
        //ex: <a href="http://xuk.ru/teen/kapris-7/vid-1.html">
        preg_match_all("'<a\s+href=\"(http:\/\/xuk\.ru\/([^\/]*?)\/([^\/]*?)\/vid-1\.html)\">'isx", $html, $links);
        preg_match("'<meta\s+http-equiv=\"Content-Type\"\s+content=\"text/html;\s?charset=(.*?)\"\s?[\/]?>'isx", $html, $http_code);
//        pr($names);
//        pr($links);


        $http_code=isset($http_code[1]) ? $http_code[1] : 'utf-8';
//        echo "\r\n";
        if(!isset($links[2]) || !empty($links[2])){
            return false;
        }
        if(!isset($links[3]) || !empty($links[3])){
            return false;
        }

        if(!isset($names) || !empty($names)){
            if(!isset($names[1]) || !empty($names[1])){
                return false;
            }
            if(count($names[1])!=count($links[2]) || count($names[1])!=count($links[3])){
                return false;
            }


            $i=0;
            foreach($names[1] as $name){

                $gallery=new WpNggGallery;
                $gallery->path='wp-content/gallery/xuk/'.trim($links[2][$i]).'/'.trim($links[3][$i]);
                $gallery->slug=trim($links[2][$i]).'/'.trim($links[3][$i]);
                $data=$gallery->search()->getData();
//                pd($data);

                if(empty($data)){
                    $name_utf8=iconv($http_code, 'utf-8', $name);
                    $gallery->name=$name_utf8;
                    $gallery->title=trim($links[3][$i]);
                    $gallery->galdesc='';
                    $gallery->pageid=0;
                    $gallery->previewpic=1;
                    $gallery->author=0;         //if updated gallery ,then set to 1
                    $gallery->save();
//                    echo $i;
                }
                
                $i++;
//                echo "\r\n";
            }
        }

        return true;
	}
	
    //get items,insert db
	public static function getItem()
	{
        $expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 30;
        
		$gallery=WpNggGallery::model()->find('author=0');
        if(empty($gallery->slug)){
            return false;
        }
        $src='http://xuk.ru/'.$gallery->slug.'/vid-1.html';

        $html='';
        $page=Tools::OZCurl($src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            return false;
        }

        preg_match_all("'<a\s+class=\"xuk_gallery\"\s+href=\"(.*?)\">'isx", $html, $images);
        if(!empty($images)){
            $pictures=new WpNggPictures;
            foreach($images[1] as $image){
//                $key=MCrypy::encrypt($image, Yii::app()->params['xuk_pass'], 128);
                $key=md5($image);
                $suffix=substr($image,strrpos($image,'.'));
                $suffix_len=strlen($suffix);
                if($suffix_len<4 || $suffix_len>5 || (substr($suffix, 0, 1)!='.')){
                    continue;
                }


                if(strpos($gallery->slug,'/')!==false){
                    $alt=explode('/',$gallery->slug);
                }else{
                    $alt[0]='xxer';
                    $alt[1]='girl';
                }


                $_pictures=clone $pictures;
                $_pictures->image_slug=$key;
                $_pictures->galleryid=$gallery->gid;
                $data=$_pictures->search()->getData();

                if(empty($data)){
                    $_pictures->post_id=0;
                    $_pictures->filename=$key.$suffix;
                    $_pictures->description=$gallery->name.','.$alt[0].','.$alt[1];
                    $_pictures->alttext=$gallery->name.','.$alt[0].','.$alt[1];
                    $_pictures->imagedate=date('Y-m-d H:i:s');
                    $_pictures->exclude=0;
                    $_pictures->sortorder=0;
                    $_pictures->meta_data='';
                    if($_pictures->save()){
                        $src_obj=new WpNggSrc;
                        $src_obj->pid=$_pictures->pid;
                        $src_obj->gid=$gallery->gid;
                        $src_obj->src=$image;
                        $src_obj->name=$gallery->name;
                        $src_obj->path=$gallery->path;
                        $src_obj->filename=$_pictures->filename;
                        $src_obj->status=0;
                        $src_obj->mktime=date('Y-m-d H:i:s');
                        $src_obj->save();
                    }
                }
            }
        }

        return true;
	}

    //get images, make small images, insert db
	public static function getImage()
	{
		$expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 30;
        $src_obj=WpNggSrc::model()->find('status=0');

        if(empty(self::$WPPATH) || !isset($src_obj->src) || empty($src_obj->src)){
            return false;
        }

        $html='';
        $page=Tools::OZCurl($src_obj->src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            return false;
        }

        $save_path=self::$WPPATH.DIRECTORY_SEPARATOR.$src_obj->path;
        
        if(mkdir($save_path, 755, true)){
            if(file_put_contents($save_path.DIRECTORY_SEPARATOR.$src_obj->filename,$html)){
                $src_obj->status=1;
                $src_obj->save();
                return true;
            }
        }

        return false;
	}
}