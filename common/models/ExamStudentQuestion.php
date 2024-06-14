<?php

namespace common\models;

use api\resources\ResourceTrait;
use common\models\Translate;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

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
class ExamStudentQuestion extends \yii\db\ActiveRecord
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
        return 'exam_student_question';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['exam_id', 'user_id', 'student_id','exam_student_id' , 'question_id' ,'exam_subject_id', 'options'], 'required'],

            [['student_option', 'is_correct', 'exam_id', 'user_id', 'student_id', 'exam_student_id' ,'exam_subject_id', 'question_id' , 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['exam_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSubject::className(), 'targetAttribute' => ['exam_subject_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }


    public function fields()
    {
        if (isRole('student') && $this->examStudent->status == 1) {
            $fields =  [
                'id',
                'exam_id',
                'user_id',
                'student_id',
                'exam_student_id',
                'exam_subject_id',
                'question_id',
                'options',
                'student_option',

                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ];
        } else {
            $fields =  [
                'id',
                'exam_id',
                'user_id',
                'student_id',
                'is_correct',
                'exam_student_id',
                'exam_subject_id',
                'question_id',
                'options',
                'student_option',

                'status',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
            ];
        }

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'exam',
            'user',
            'student',
            'examSubject',
            'examStudent',
            'question',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getExamSubject()
    {
        return $this->hasOne(ExamSubject::className(), ['id' => 'exam_subject_id']);
    }

    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    public static function answerOption($id) {
        $option = Option::findOne([
            'question_id' => $id,
            'is_correct' => 1,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (isset($option)) {
            return $option->id;
        }
        return null;
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
