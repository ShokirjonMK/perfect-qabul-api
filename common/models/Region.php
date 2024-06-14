<?php

namespace common\models;

use api\resources\ResourceTrait;
use common\models\Countries;
use common\models\Translate;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_kirill
 * @property string|null $slug
 * @property int|null $country_id
 * @property int|null $parent_id
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
 * @property Countries $country
 * @property Region $parent
 * @property Region[] $regions

 */
class Region extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';
    
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'parent_id', 'type', 'sort', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'name_kirill', 'slug', 'postcode'], 'string', 'max' => 150],
            [['lat', 'long'], 'string', 'max' => 100],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'name' => _e('Name'),
            'name_kirill' => _e('Name Kirill'),
            'slug' => _e('Slug'),
            'country_id' => _e('Country ID'),
            'parent_id' => _e('Parent ID'),
            'type' => _e('Type'),
            'postcode' => _e('Postcode'),
            'lat' => _e('Lat'),
            'long' => _e('Long'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
            'created_on' => _e('Created On'),
            'created_by' => _e('Created By'),
            'updated_on' => _e('Updated On'),
            'updated_by' => _e('Updated By'),
                           
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'name',
            'name_kirill',
            'slug',
            'country_id',
            'parent_id',
            'type',
            'postcode',
            'lat',
            'long',
            'sort',
            'status',
            'created_on',
            'created_by',
            'updated_on',
            'updated_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'country',
            'parent',
            'regions',
            
            'description',    
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    
    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /**
     * Region createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // some logic for creating

        $has_error = Translate::checkingAll($post);

        if ($has_error["status"]) {
            if ($model->save()) {
                if (isset($post["description"])) {
                    Translate::createTranslate($post["name"], $model->tableName(), $model->id, $post["description"]);
                } else {
                    Translate::createTranslate($post["name"], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error["errors"]);
        }    
    }

    /**
     * Region updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        
        // some logic for updating

        $has_error = Translate::checkingUpdate($post);
        if ($has_error["status"]) {
            if ($model->save()) {
                if (isset($post["name"])) {
                    if (isset($post["description"])) {
                        Translate::updateTranslate($post["name"], $model->tableName(), $model->id, $post["description"]);
                    } else {
                        Translate::updateTranslate($post["name"], $model->tableName(), $model->id);
                    }
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error["errors"]);
        }
    }

    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
