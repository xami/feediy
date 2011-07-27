<?php

/**
 * This is the model class for table "wp_ngg_gallery".
 *
 * The followings are the available columns in table 'wp_ngg_gallery':
 * @property string $gid
 * @property string $name
 * @property string $slug
 * @property string $path
 * @property string $title
 * @property string $galdesc
 * @property string $pageid
 * @property string $previewpic
 * @property string $author
 */
class WpNggGallery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return WpNggGallery the static model class
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
		return 'wp_ngg_gallery';
	}
	
	public function scopes()
    {
        return array(
            'recently_one'=>array(
                'order'=>'gid ASC',
                'limit'=>1,
            ),
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, slug', 'required'),
			array('name, slug', 'length', 'max'=>255),
			array('pageid, previewpic, author', 'length', 'max'=>20),
			array('path, title, galdesc', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('gid, name, slug, path, title, galdesc, pageid, previewpic, author', 'safe', 'on'=>'search'),
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
			'gid' => 'Gid',
			'name' => 'Name',
			'slug' => 'Slug',
			'path' => 'Path',
			'title' => 'Title',
			'galdesc' => 'Galdesc',
			'pageid' => 'Pageid',
			'previewpic' => 'Previewpic',
			'author' => 'Author',
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

		$criteria->compare('gid',$this->gid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('galdesc',$this->galdesc,true);
//		$criteria->compare('pageid',$this->pageid,true);
//		$criteria->compare('previewpic',$this->previewpic,true);
//		$criteria->compare('author',$this->author,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}