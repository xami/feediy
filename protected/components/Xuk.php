<?php
class Xuk
{
    protected static $WPPATH='/data/htdocs/haoselang';
    protected static $WPDOMAIN='http://www.haoselang.com';
//    protected $xuk_pass='';
    //get item links, insert db

    /*
     * $id传入页号，解析并取得相册列表页面的链接
     */
	public static function getList($id)
	{

        $id=($id<1) ? 1 : $id;

        $expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 3000;
        $src='http://xuk.ru/'.$id.'.html';

        $html='';
        $page=Tools::OZCurl($src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            Yii::log('curl error::'.$src,$level='warning',$category='list');
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
        if(!isset($links[2]) || empty($links[2])){
            Yii::log('empty $links[2]',$level='warning',$category='list');
            return false;
        }
        
        if(!isset($links[3]) || empty($links[3])){
            Yii::log('empty $links[3]',$level='warning',$category='list');
            return false;
        }

        if(!isset($names) || empty($names)){
            Yii::log('empty $names',$level='warning',$category='list');
            return false;
        }

        if(!isset($names[1]) || empty($names[1])){
            Yii::log('empty $names[1]',$level='warning',$category='list');
            return false;
        }
        
        if(count($names[1])!=count($links[2]) || count($names[1])!=count($links[3])){
            Yii::log('count($names[1])!=count($links[2]) || count($names[1])!=count($links[3]',$level='warning',$category='list');
            return false;
        }


        $i=0;
        $_gallery=new WpNggGallery;
        foreach($names[1] as $name){

            $gallery=clone $_gallery;
            $gallery->path='wp-content/gallery/xuk/'.trim($links[2][$i]).'/'.trim($links[3][$i]);
            $gallery->slug=trim($links[2][$i]).'/'.trim($links[3][$i]);
            $data=$gallery->search()->getData();
//                pd($data);

            if(empty($data)){
                $name_utf8=iconv($http_code, 'utf-8', $name);
                $gallery->name=$name_utf8;
                $gallery->title=trim($links[3][$i]);
                $gallery->galdesc=trim($links[2][$i]);
                $gallery->pageid=0;
                $gallery->previewpic=1;
                $gallery->author=0;         //if updated gallery ,then set to 1
                if(!$gallery->save()){
                    Yii::log('$gallery->save()::'.serialize($gallery->getData()),$level='warning',$category='list');
                }
            }

            $i++;
//                echo "\r\n";
        }


        return true;
	}
	
    //get items,insert db
    /*
     * 取得具体的相册的图片链接
     */
	public static function getItem()
	{
        $expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 300;

		$gallery=WpNggGallery::model()->find('author=0');
        if(empty($gallery->slug)){
            Yii::log('empty($gallery->slug)',$level='warning',$category='item');
            return false;
        }
        $src='http://xuk.ru/'.$gallery->slug.'/vid-1.html';

        $html='';
        $page=Tools::OZCurl($src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            Yii::log('curl error::'.$src,$level='warning',$category='item');
            return false;
        }

        preg_match_all("'<a\s+class=\"xuk_gallery\"\s+href=\"(.*?)\">'isx", $html, $images);
        if(empty($images)){
            Yii::log('empty($images)',$level='warning',$category='item');
            return false;
        }
        
        $pictures=new WpNggPictures;
        foreach($images[1] as $image){
//                $key=MCrypy::encrypt($image, Yii::app()->params['xuk_pass'], 128);
            $key=md5($image);
            $suffix=substr($image,strrpos($image,'.'));
            $suffix_len=strlen($suffix);
            if($suffix_len<4 || $suffix_len>5 || (substr($suffix, 0, 1)!='.')){
                Yii::log('suffix error::'.$image,$level='warning',$category='item');
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
                $_pictures->description='xxer.info,'.$gallery->name;
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
                    if(!$src_obj->save()){
                        Yii::log('$src_obj->save()::'.serialize($src_obj->getData()),$level='warning',$category='item');
                    }
//                    $gallery->previewpic=$_pictures->pid;
//                    $gallery->save();
                }else{
                    Yii::log('$_pictures->save()::'.serialize($_pictures->getData()),$level='warning',$category='item');
                }
            }
        }

        $gallery->author=-1;
        if(!$gallery->save()){
            Yii::log('$gallery->save()::'.serialize($gallery->getData()),$level='warning',$category='item');
        }
        return true;
	}

    //get images, make small images, insert dbs
    /*
     * 取得相册里面的图片文件,并保存
     */
	public static function getImage()
	{
		$expire=isset($_REQUEST['expire']) ? intval($_REQUEST['expire']) : 30;
        $src_obj=WpNggSrc::model()->find('status=0');

        if(empty(self::$WPPATH) || !isset($src_obj->src) || empty($src_obj->src)){
            Yii::log('empty(self::$WPPATH) || !isset($src_obj->src) || empty($src_obj->src)',$level='warning',$category='image');
            return false;
        }

        $html='';
        $page=Tools::OZCurl($src_obj->src, $expire);
        if((isset($page['ErrNo']) && $page['ErrNo']==0) &&
           (isset($page['Info']['http_code']) && $page['Info']['http_code']==200)){
            $html=$page['Result'];
        }else{
            Yii::log('Tools::OZCurl($src_obj->src, $expire)::'.$src_obj->src,$level='warning',$category='image');
            return false;
        }

        $save_path=self::$WPPATH.DIRECTORY_SEPARATOR.$src_obj->path;
        if(!is_dir($save_path)){
            if(!mkdir($save_path, 755, true)){
                Yii::log('mkdir($save_path, 755, true)',$level='warning',$category='image');
                return false;
            }
        }
        $thumbs_path=self::$WPPATH.DIRECTORY_SEPARATOR.$src_obj->path.DIRECTORY_SEPARATOR.'thumbs';
        if(!is_dir($thumbs_path)){
            if(!mkdir($thumbs_path, 755, true)){
                Yii::log('mkdir($thumbs_path, 755, true)',$level='warning',$category='image');
                return false;
            }
        }
        
        if(file_put_contents($save_path.DIRECTORY_SEPARATOR.$src_obj->filename,$html)){
            $gallery=WpNggGallery::model()->findByPk($src_obj->gid);
            $gallery->previewpic=$src_obj->pid;
            $gallery->save();
            $src_obj->status=1;
            $src_obj->save();
            return true;
        }

        Yii::log('file_put_contents($save_path.DIRECTORY_SEPARATOR.$src_obj->filename,$html)',$level='warning',$category='image');
        return false;
	}

    /*
     * 发表帖子
     */
    public static function postGallery()
    {
        $gallery=WpNggGallery::model()->recently_one()->find('author=-1');
        if(empty($gallery->slug) || empty($gallery->name)){
            Yii::log('empty($gallery->slug) || empty($gallery->name)',$level='warning',$category='post');
            return false;
        }
        $date=date('Y-m-d H:i:s');
/*
 *
wp_terms
term_id 	name 	slug 	term_group
3 	Teen 	teen 	0
4 	Celeb 	celeb 	0
5 	Pussy 	pussy 	0
6 	Bikini 	bikini 	0
7 	Asian 	asian 	0
8 	Lesbian 	lesbian 	0
9 	Tits 	tits 	0
10 	Other 	other 	0

wp_term_taxonomy
term_taxonomy_id 	term_id 	taxonomy 	description 	parent 	count
3 	3 	category 		0 	1
4 	4 	category 		0 	0
5 	5 	category 		0 	0
7 	7 	category 		0 	0
8 	8 	category 		0 	0
9 	9 	category 		0 	0
10 	10 	category 		0 	0

wp_term_relationships
object_id 	term_taxonomy_id 	term_order
*/
        $post=new WpPosts;
        $post->post_author=1;
        $post->post_date=$date;
        $post->post_date_gmt=$date;
        $post->post_content='[nggallery id='.$gallery->gid.']';
        $post->post_title=$gallery->name;
        $post->post_excerpt='';
        $post->post_status='publish';
        $post->comment_status='open';
        $post->ping_status='open';
        $post->post_name=$gallery->title;
        $post->to_ping='';
        $post->pinged='';
        $post->post_modified=$date;
        $post->post_modified_gmt=$date;
        $post->post_content_filtered='';
        $post->post_parent=0;
        $post->guid='';
        $post->menu_order=0;
        $post->post_type='post';
        $post->comment_count=0;
        if($post->save()){
            $post->guid=self::$WPDOMAIN.'/?p='.$post->ID;
            if(!$post->save()){
                Yii::log('$post->ID'.serialize($post->getData),$level='warning',$category='post');
            }

            switch(strtolower($gallery->galdesc)){
                case 'teen':
                    $cid=3;break;
                case 'celeb':
                    $cid=4;break;
                case 'pussy':
                    $cid=5;break;
                case 'bikini':
                    $cid=6;break;
                case 'asian':
                    $cid=7;break;
                case 'lesbian':
                    $cid=8;break;
                case 'tits':
                    $cid=9;break;
                case 'other':
                    $cid=10;break;
                default:
                    $cid=0;
            }

            if(!empty($cid)){
                $TR=new WpTermRelationships;
                $TR->object_id=$post->ID;
                $TR->term_taxonomy_id=$cid;
                $TR->term_order=0;
                if($TR->save()){
                    $TT=WpTermTaxonomy::model()->findByPk($cid);
                    $TT->count++;
                    if(!$TT->save()){
                        Yii::log('$TT->save()::'.serialize($TT->getData),$level='warning',$category='post');
                    }
                }else{
                    Yii::log('$TR->save()::'.serialize($TR->getData),$level='warning',$category='post');
                }
            }

            $gallery->author=1;
            if(!$gallery->save()){
                Yii::log('$gallery->save()::'.serialize($gallery->getData),$level='warning',$category='post');
            }

            $src=WpNggSrc::model()->find('gid=:gid AND status=0', array(':gid'=>$gallery->gid));
            if(isset($src->id) && !empty($src->id)){
                $post->post_status='private';
                $post->save();
            }
            return true;
        }

        

        Yii::log('$post->save()::'.serialize($post->getData),$level='warning',$category='post');
        return false;

    }

    public static function createThumbnail()
    {
        $pictures=WpNggPictures::model()->find('meta_data IS NULL or meta_data=\'\'');
        $once=trim(strtolower(Yii::app()->request->getParam('once', '')));
        
        if(!isset($pictures->pid) || empty($pictures->pid)){
            Yii::log('empty($data)::',$level='warning',$category='thumbnail');
            return false;
        }

        return array('pid'=>$pictures->pid, 'dm'=>self::$WPDOMAIN, 'once'=>$once);
    }

    public static function publicPost()
    {
        $post=WpPosts::model()->find('post_status=:st', array(':st'=>'private'));

        if(isset($post->id) && !empty($post->id)){
            $gid=0;
            if(!empty($post->post_content)){
                $e_start=explode('[nggallery id=',$post->post_content);
                if(isset($e_start[1]) && !empty($e_start[1])){
                    $gid=substr($e_start[1], 0 , -1);
                    if(intval($gid) != $gid){
                        $gid=0;
                    }
                }
            }
            
            if(!empty($gid)){
                $src=WpNggSrc::model()->find('gid=:gid AND status=0', array(':gid'=>$gid));
                if(!isset($src->id)){
                    $post->post_status='publish';
                    if($post->save()){
                        return true;
                    }
                }
            }
        }

        return false;
    }
}