<?php

/**
 * This is the model class for table "wp_ngg_src".
 *
 * The followings are the available columns in table 'wp_ngg_src':
 * @property integer $id
 * @property integer $pid
 * @property integer $gid
 * @property string $src
 * @property string $name
 * @property string $path
 * @property string $filename
 * @property integer $status
 * @property string $mktime
 * @property string $uptime
 */
class WpNggSrc extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return WpNggSrc the static model class
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
		return 'wp_ngg_src';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, filename', 'required'),
			array('pid, gid, status', 'numerical', 'integerOnly'=>true),
			array('src, name, filename', 'length', 'max'=>255),
			array('path, mktime, uptime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, pid, gid, src, name, path, filename, status, mktime, uptime', 'safe', 'on'=>'search'),
		);
	}
	
	public function scopes()
    {
        return array(
            'recently_one'=>array(
                'order'=>'id ASC',
                'limit'=>1,
            ),
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
			'gallery'=>array(self::BELONGS_TO, 'WpNggGallery', 'gid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'gid' => 'Gid',
			'src' => 'Src',
			'name' => 'Name',
			'path' => 'Path',
			'filename' => 'Filename',
			'status' => 'Status',
			'mktime' => 'Mktime',
			'uptime' => 'Uptime',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('gid',$this->gid);
		$criteria->compare('src',$this->src,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('mktime',$this->mktime,true);
		$criteria->compare('uptime',$this->uptime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}