<?php

namespace common\models;

use api\resources\ResourceTrait;
use common\models\Translate;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property string $name
 * @property int $faculty_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class Exam extends \yii\db\ActiveRecord
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
        return 'exam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_year_id','direction_id' , 'start_time' , 'finish_time','duration_time','language_id'], 'required'],
            [['edu_year_id','direction_id' , 'language_id','start_time' , 'finish_time','duration_time', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            ['finish_time', 'validTime'],
        ];
    }

    public function validTime($attribute, $params)
    {
        if ($this->start_time >= $this->finish_time) {
            $this->addError($attribute, _e('The end time must be greater than the start time!'));
        }
    }

    public function fields()
    {
        $fields =  [
            'id',
            'edu_year_id',
            'direction_id',
            'start_time',
            'finish_time',
            'duration_time',
            'language_id',
            'current_time' => function () {
                return time();
            },

            'studentStatus' => function () {
                return $this->isExamStudent;
            },

            'order',
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
        $extraFields =  [
            'eduYear',
            'examStudent',
            'direction',
            'language',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id'])->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getExamStudent()
    {
        if (isRole('student')) {
            return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])
                ->where([
                    'user_id' => current_user_id(),
                    'is_deleted' => 0,
                ]);
        }
        return $this->hasMany(ExamStudent::className(), ['exam_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public function getIsExamStudent()
    {
        if (isRole('student')) {
            $student = current_student();
            $query = ExamStudent::findOne([
                'exam_id' => $this->id,
                'user_id' => $student->user_id,
                'attempt_count' => $student->attempt_count,
                'is_deleted' => 0
            ]);
            if ($query) {
                if ($query->status == 1 && $query->finish_time > time()) {
                    return 1;
                } elseif ($query->status == 1 && $query->finish_time < time()) {
                    ExamStudent::finish($query);
                    return 2;
                } elseif ($query->status == 2) {
                    return 2;
                }
            }
            return 0;
        }
        return null;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        } else {
            $model->save(false);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        } else {
            $model->save(false);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
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
