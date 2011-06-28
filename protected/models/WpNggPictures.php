<?php

/**
 * This is the model class for table "wp_ngg_pictures".
 *
 * The followings are the available columns in table 'wp_ngg_pictures':
 * @property string $pid
 * @property string $image_slug
 * @property string $post_id
 * @property string $galleryid
 * @property string $filename
 * @property string $description
 * @property string $alttext
 * @property string $imagedate
 * @property integer $exclude
 * @property string $sortorder
 * @property string $meta_data
 */
class WpNggPictures extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return WpNggPictures the static model class
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
		return 'wp_ngg_pictures';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('image_slug, filename', 'required'),
			array('exclude', 'numerical', 'integerOnly'=>true),
			array('image_slug, filename', 'length', 'max'=>255),
			array('post_id, galleryid, sortorder', 'length', 'max'=>20),
			array('description, alttext, imagedate, meta_data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('pid, image_slug, post_id, galleryid, filename, description, alttext, imagedate, exclude, sortorder, meta_data', 'safe', 'on'=>'search'),
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
			'pid' => 'Pid',
			'image_slug' => 'Image Slug',
			'post_id' => 'Post',
			'galleryid' => 'Galleryid',
			'filename' => 'Filename',
			'description' => 'Description',
			'alttext' => 'Alttext',
			'imagedate' => 'Imagedate',
			'exclude' => 'Exclude',
			'sortorder' => 'Sortorder',
			'meta_data' => 'Meta Data',
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

		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('image_slug',$this->image_slug);
//		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('galleryid',$this->galleryid);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('alttext',$this->alttext,true);
//		$criteria->compare('imagedate',$this->imagedate,true);
//		$criteria->compare('exclude',$this->exclude);
//		$criteria->compare('sortorder',$this->sortorder,true);
		$criteria->compare('meta_data',$this->meta_data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}