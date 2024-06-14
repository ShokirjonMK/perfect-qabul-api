<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $region_id
 * @property int|null $type
 * @property string|null $postcode
 * @property string|null $lat
 * @property string|null $long
 * @property int|null $sort
 * @property int|null $status
 * @property string|null $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 *
 * @property Region $region
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'type', 'sort', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'postcode'], 'string', 'max' => 150],
            [['lat', 'long'], 'string', 'max' => 100],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'region_id' => 'Region ID',
            'type' => 'Type',
            'postcode' => 'Postcode',
            'lat' => 'Lat',
            'long' => 'Long',
            'sort' => 'Sort',
            'status' => _e('Status'),
            'created_on' => 'Created On',
            'created_by' => _e('Created By'),
            'updated_on' => 'Updated On',
            'updated_by' => _e('Updated By'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name',
            'region_id',
            'type',
            'postcode',
            'lat',
            'long',
            'sort',
            'status',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'region',
            'profileslive',
            'profiles',
            'country',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['area_id' => 'id']);
    }

    /**
     * Gets query for [[Profiles0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfileslive()
    {
        return $this->hasMany(Profile::className(), ['permanent_area_id' => 'id']);
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }
}
