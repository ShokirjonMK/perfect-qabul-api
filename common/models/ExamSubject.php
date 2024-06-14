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
class ExamSubject extends \yii\db\ActiveRecord
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
        return 'exam_subject';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['exam_id','exam_student_id', 'user_id', 'student_id','subject_id' , 'type'], 'required'],
            [['exam_id','exam_student_id', 'user_id', 'student_id','subject_id' , 'type', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['ball'], 'safe'],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['exam_student_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamStudent::className(), 'targetAttribute' => ['exam_student_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'exam_id',
            'user_id',
            'student_id',

            'exam_student_id',
            'subject_id',
            'type',
            'ball',

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
            'exam',
            'user',
            'student',
            'subject',
            'examStudent',
            'question',
            'questions',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getQuestion()
    {
        return $this->hasMany(ExamStudentQuestion::className(), ['exam_subject_id' => 'id'])->where(['status' => 1 , 'is_deleted' => 0]);
    }

    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    public function getExamStudent()
    {
        return $this->hasOne(ExamStudent::className(), ['id' => 'exam_student_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getQuestions()
    {
        return $this->hasMany(ExamStudentQuestion::className(), ['id' => 'exam_subject_id'])->where(['status' => 1 , 'is_deleted' => 0]);
    }

    public function getCorrectCount()
    {
        return ExamStudentQuestion::find()
            ->where([
                'exam_subject_id' => $this->id,
                'is_correct' => 1,
                'status' => 1,
                'is_deleted' => 0,
            ])->count();
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $exam = $model->exam;
        $directionSubject = DirectionSubject::findOne([
            'direction_id' => $exam->direction_id,
            'subject_id' => $model->subject_id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($directionSubject) {
            $model->ball = $directionSubject->max_ball;
            $model->save(false);

            $query = ExamSubject::find()
                ->where([
                    'exam_student_id' => $model->exam_student_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->all();
            $ball = 0;
            foreach ($query as $item) {
                $ball = $ball + $item->ball;
            }
            $examStudent = $model->examStudent;
            $examStudent->ball = $ball;
            $examStudent->update(false);

            $student = $model->student;
            if ($model->ball >= Student::CONTRACT_BALL) {
                $student->entered = Student::ENTERED;
                $student->contract_type = Student::CONTRACT_1;
                Message::examSertificate($student->user->username);
            } else {
                $student->entered = Student::ENTERED;
                $student->contract_type = Student::CONTRACT_1;
                Message::examSertificate($student->user->username);
            }
            $student->update(false);

        } else {
            $errors[] = _e('Direction Subject not found.');
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
