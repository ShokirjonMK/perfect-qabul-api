<?php

namespace common\models;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $user_id
 * @property float|null $checked
 * @property float|null $checked_full
 * @property string|null $image
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $passport_serial
 * @property string|null $passport_number
 * @property string|null $passport_pin
 * @property string|null $passport_issued_date
 * @property string|null $passport_given_date
 * @property string|null $passport_given_by
 * @property string|null $birthday
 * @property int|null $gender
 * @property string|null $phone
 * @property string|null $phone_secondary
 * @property string|null $passport_file
 * @property int|null $countries_id
 * @property int|null $region_id
 * @property int|null $area_id
 * @property int|null $permanent_countries_id
 * @property int|null $permanent_region_id
 * @property int|null $permanent_area_id
 * @property string|null $permanent_address
 * @property string|null $address
 * @property string|null $description
 * @property int|null $is_foreign
 * @property int|null $citizenship_id citizenship_id fuqarolik turi
 * @property int|null $nationality_id millati id
 * @property int|null $telegram_chat_id
 * @property int|null $diploma_type_id diploma_type
 * @property int|null $degree_id darajasi id
 * @property int|null $academic_degree_id academic_degree id
 * @property int|null $degree_info_id degree_info id
 * @property int|null $partiya_id partiya id
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Area $area
 * @property Countries $countries
 * @property Area $permanentArea
 * @property Countries $permanentCountries
 * @property Region $permanentRegion
 * @property Region $region
 * @property User $user

 */
class Profile extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    const UPLOADS_FOLDER_STUDENT_IMAGE = 'uploads/student-images/';

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
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['user_id'], 'required'
            ],
            [
                [
                    'user_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['image','first_name','last_name','middle_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'first_name',
            'last_name',
            'middle_name',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Get user fullname
     *
     * @param object $profile
     * @return mixed
     */
    public static function getFullname($profile)
    {
        $fullname = '';

        if ($profile && $profile->first_name) {
            $fullname = _strtotitle($profile->first_name) . ' ';
        }

        if ($profile && $profile->last_name) {
            $fullname .= _strtotitle($profile->last_name);
        }

        return $fullname ? trim($fullname) : 'Unknown User';
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), "lang_code", "en");
        return $this->hasMany(Translate::class, ["model_id" => "id"])
            ->andOnCondition(["language" => Yii::$app->request->get("lang"), "table_name" => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), "lang_code", "en");
        return $this->hasMany(Translate::class, ["model_id" => "id"])
            ->andOnCondition(["language" => self::$selected_language, "table_name" => $this->tableName()]);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    /**
     * Gets query for [[Countries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasOne(Countries::className(), ['id' => 'countries_id']);
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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
        ];
    }
}
