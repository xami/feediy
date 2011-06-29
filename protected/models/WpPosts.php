<?php

/**
 * This is the model class for table "wp_posts".
 *
 * The followings are the available columns in table 'wp_posts':
 * @property string $ID
 * @property string $post_author
 * @property string $post_date
 * @property string $post_date_gmt
 * @property string $post_content
 * @property string $post_title
 * @property string $post_excerpt
 * @property string $post_status
 * @property string $comment_status
 * @property string $ping_status
 * @property string $post_password
 * @property string $post_name
 * @property string $to_ping
 * @property string $pinged
 * @property string $post_modified
 * @property string $post_modified_gmt
 * @property string $post_content_filtered
 * @property string $post_parent
 * @property string $guid
 * @property integer $menu_order
 * @property string $post_type
 * @property string $post_mime_type
 * @property string $comment_count
 */
class WpPosts extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return WpPosts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wp_posts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('post_content, post_title', 'required'),
			array('menu_order', 'numerical', 'integerOnly'=>true),
			array('post_author, post_status, comment_status, ping_status, post_password, post_parent, post_type, comment_count', 'length', 'max'=>20),
			array('post_name', 'length', 'max'=>200),
			array('guid', 'length', 'max'=>255),
			array('post_mime_type', 'length', 'max'=>100),
			array('post_date, post_date_gmt, post_modified, post_modified_gmt', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'post_author' => 'Post Author',
			'post_date' => 'Post Date',
			'post_date_gmt' => 'Post Date Gmt',
			'post_content' => 'Post Content',
			'post_title' => 'Post Title',
			'post_excerpt' => 'Post Excerpt',
			'post_status' => 'Post Status',
			'comment_status' => 'Comment Status',
			'ping_status' => 'Ping Status',
			'post_password' => 'Post Password',
			'post_name' => 'Post Name',
			'to_ping' => 'To Ping',
			'pinged' => 'Pinged',
			'post_modified' => 'Post Modified',
			'post_modified_gmt' => 'Post Modified Gmt',
			'post_content_filtered' => 'Post Content Filtered',
			'post_parent' => 'Post Parent',
			'guid' => 'Guid',
			'menu_order' => 'Menu Order',
			'post_type' => 'Post Type',
			'post_mime_type' => 'Post Mime Type',
			'comment_count' => 'Comment Count',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ID',$this->ID,true);
		$criteria->compare('post_author',$this->post_author,true);
		$criteria->compare('post_date',$this->post_date,true);
		$criteria->compare('post_date_gmt',$this->post_date_gmt,true);
		$criteria->compare('post_content',$this->post_content,true);
		$criteria->compare('post_title',$this->post_title,true);
		$criteria->compare('post_excerpt',$this->post_excerpt,true);
		$criteria->compare('post_status',$this->post_status,true);
		$criteria->compare('comment_status',$this->comment_status,true);
		$criteria->compare('ping_status',$this->ping_status,true);
		$criteria->compare('post_password',$this->post_password,true);
		$criteria->compare('post_name',$this->post_name,true);
		$criteria->compare('to_ping',$this->to_ping,true);
		$criteria->compare('pinged',$this->pinged,true);
		$criteria->compare('post_modified',$this->post_modified,true);
		$criteria->compare('post_modified_gmt',$this->post_modified_gmt,true);
		$criteria->compare('post_content_filtered',$this->post_content_filtered,true);
		$criteria->compare('post_parent',$this->post_parent,true);
		$criteria->compare('guid',$this->guid,true);
		$criteria->compare('menu_order',$this->menu_order);
		$criteria->compare('post_type',$this->post_type,true);
		$criteria->compare('post_mime_type',$this->post_mime_type,true);
		$criteria->compare('comment_count',$this->comment_count,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}