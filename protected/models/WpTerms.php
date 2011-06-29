<?php

/**
 * This is the model class for table "wp_terms".
 *
 * The followings are the available columns in table 'wp_terms':
 * @property string $term_id
 * @property string $name
 * @property string $slug
 * @property string $term_group
 */
class WpTerms extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return WpTerms the static model class
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
		return 'wp_terms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, slug', 'length', 'max'=>200),
			array('term_group', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('term_id, name, slug, term_group', 'safe', 'on'=>'search'),
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
			'term_id' => 'Term',
			'name' => 'Name',
			'slug' => 'Slug',
			'term_group' => 'Term Group',
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

		$criteria->compare('term_id',$this->term_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('term_group',$this->term_group,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}