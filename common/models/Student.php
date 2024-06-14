<?php

namespace common\models;

use api\forms\StepTwo;
use api\resources\Password;
use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class Student extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const CONTRACT_1 = 1;
    const CONTRACT_1_5 = 1.5;
    const CONTRACT_BALL = 56.7;
    const ENTERED = 1;
    const NOT_ENTERED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                ], 'required'
            ],
            [
                [
                    'user_id',
                    'language_id',
                    'gender',

                    'passport_number',
                    'passport_pin',

                    'edu_type',
                    'diploma_type',
                    'certificate_type',
                    'certificate_level',
                    'certificate_level_type',
                    'general_edu_type',
                    'edu_form_id',
                    'direction_id',
                    'exam_type',
                    'entered',
                    'attempt_count',
                    'exam_form',

                    'order',
                    'status',
                    'gender',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['passport_issued_date','passport_given_date'], 'date' , 'format' => 'yyyy-mm-dd'],
            [['contract_type'], 'safe'],
            [
                [
                    'first_name',
                    'last_name',
                    'middle_name',
                    'image',
                    'passport_serial',
                    'passport_given_by',
                    'edu_name',
                    'dtm_file',
                    'certificate_file',
                    'diploma_file',
                    'permit_file',
                    'invois'
                ], 'string', 'max' => 255],
            [
                [
                    'phone',
                ], 'string', 'max' => 50
            ],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'step' => function() {
                return $this->user->step;
            },
            'first_name',
            'last_name',
            'middle_name',
            'image',
            'passport_number',
            'passport_serial',
            'passport_pin',
            'passport_issued_date',
            'passport_given_date',
            'passport_given_by',
            'birthday' => function () {
                if ($this->birthday != null) {
                    return date("d-m-Y" , strtotime($this->birthday));
                }
                return null;
            },
            'gender',
            'phone',
            'invois',
            'attempt_count',
            'exam_form',

            'entered',
            'contract_type',
            'contract',
            'language_id',

            'edu_type' => function () {
                if ($this->edu_type != null) {
                    return $this->eduType;
                }
                return 0;
            },
            'edu_name',
            'diploma_type',
            'diploma_file',
            'd_file' => function () {
                return $this->diploma_file;
            },


            'certificate_type',
            'certificate_level' => function () {
                if ($this->certificate_level != 0) {
                    return $this->certificateLevel;
                }
                return 0;
            },
            'c_file' => function () {
                return $this->certificate_file;
            },
            'certificate_level_type',

            'general_edu_type',
            'edu_form_id',
            'direction_id',

            'exam_type',
            'dtm_file',
            'permit_file',

            'language',
            'eduForm',
            'direction',

            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'user',
            'contract',
            'usernamePass',
            'username',
            'password',
            'eduForm',
            'profile',
            'examStudent',
            'direction',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
        return $extraFields;
    }



    public function getPassword()
    {
        return $this->usernamePass['password'];
    }

    public function getUsername()
    {
        return $this->user->username;
    }


    // getCountry
    public function getCountry()
    {
        return Countries::findOne($this->countries_id) ?? null;
    }

    // getRegion
    public function getRegion()
    {
        return Region::findOne($this->region_id) ?? null;
    }

    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    public function getExamStudent()
    {
        return $this->hasMany(ExamStudent::className(), ['student_id' => 'id'])->where(['status' => 2 , 'attempt_count' => $this->attempt_count , 'is_deleted' => 0]);
    }

    public function getContract()
    {
        $query = ExamStudent::findOne([
            'student_id' => $this->id,
            'attempt_count' => $this->attempt_count,
            'direction_id' => $this->direction_id,
            'status' => 2
        ]);
        if ($query) {
            return [
                'contract' => $query->contract,
                'eduYear' => $query->eduYear,
                'finish_time' => $query->finish_time,
                'id' => $query->id,
                'ball' => $query->ball,
            ];
        }
        return null;
    }

    // getArea
    public function getArea()
    {
        return Area::findOne($this->area_id) ?? null;
    }

    public function getEduType()
    {
        $model = new StepTwo();
        return $model->eduTypesArray($this->edu_type);
    }

    public function getCertificateLevel()
    {
        $model = new StepTwo();
        return $model->sertificateArray($this->certificate_level);
    }


    // getNationality
    public function getNationality()
    {
        return Nationality::findOne($this->profile->nationality_id) ?? null;
    }


    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
            $this->invois = (int) round(microtime(true) * 1000);
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
