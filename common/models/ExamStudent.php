<?php

namespace common\models;

use api\resources\ResourceTrait;
use common\models\Translate;
use Da\QrCode\QrCode;
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
class ExamStudent extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_END = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_id'], 'required'],
            [['edu_year_id','download_contract', 'direction_id', 'exam_id', 'user_id', 'student_id','start_time' , 'finish_time', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted' , 'attempt_count'], 'integer'],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
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

            'edu_year_id',
            'direction_id',
            'start_time',
            'finish_time',
            'ball',
            'current_time' => function () {
                return time();
            },
            'attempt_count',
            'download_contract',
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
            'direction',
            'exam',
            'user',
            'student',
            'contract',
            'examSubject',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getExamSubject()
    {
        return $this->hasMany(ExamSubject::className(), ['exam_student_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0])->orderBy('type asc');
    }

    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }


    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getContract()
    {
        $student = $this->student;
        if ($this->attempt_count == $student->attempt_count && $student->entered == 1) {
            $university = University::findOne([
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($university) {
                $url_1 = $university->domen_name."contract/".$this->id."/2";
                $url_2 = $university->license_url;
                $url_3 = $university->domen_name."contract/".$this->id."/3";
                $qrCode_1 = (new QrCode($url_1))
                    ->setSize(120)
                    ->setMargin(5);
                $qrCode_2 = (new QrCode($url_2))
                    ->setSize(120)
                    ->setMargin(5);
                $qrCode_3 = (new QrCode($url_3))
                    ->setSize(120)
                    ->setMargin(5);
                return [
                    'contract' => $qrCode_1->writeDataUri(),
                    'contract_3' => $qrCode_3->writeDataUri(),
                    'license_url' => $qrCode_2->writeDataUri()
                ];
            }
        }
        return null;
    }

    public static function createItem($exam)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();
        $student_user = current_user_id();
        $currentStudent = current_student();
        $student = $currentStudent->id;

        $examStudent = ExamStudent::findOne([
            'exam_id' => $exam->id,
            'user_id' => $student_user,
            'attempt_count' => $currentStudent->attempt_count,
            'is_deleted' => 0
        ]);
        if (!$examStudent) {
            if ($exam->finish_time < time()) {
                $errors[] = _e('Exam time is over.');
            } else {
                $model = new ExamStudent();
                $model->exam_id = $exam->id;
                if ($currentStudent->direction_id != $model->exam->direction_id) {
                    $errors[] = _e('Exam not found.');
                } else {
                    if (!($model->validate())) {
                        $errors[] = $model->errors;
                    } else {
                        if ($model->exam->status != 1 || $model->exam->is_deleted == 1) {
                            $errors[] = _e('Exam not found.');
                        } else {
                            $model->edu_year_id = $model->exam->edu_year_id;
                            $model->direction_id = $model->exam->direction_id;
                            $model->start_time = $time;
                            $model->finish_time = strtotime('+'. $model->exam->duration_time .' minutes' , $model->start_time);
                            $model->user_id = $student_user;
                            $model->student_id = $student;
                            $model->status = 1;
                            $model->attempt_count = $currentStudent->attempt_count;
                            if (!$model->validate()) {
                                $errors[] = $model->errors;
                            } else {
                                $model->save(false);
                                $subjects = DirectionSubject::find()
                                    ->where([
                                        'direction_id' => $model->direction_id,
                                        'status' => 1,
                                        'is_deleted' => 0,
                                    ])
                                    ->groupBy('subject_id')
                                    ->orderBy('type asc')
                                    ->all();
                                foreach ($subjects as $subject) {
                                    $examSubject = new ExamSubject();
                                    $examSubject->exam_id = $model->exam_id;
                                    $examSubject->exam_student_id = $model->id;
                                    $examSubject->student_id = $model->student_id;
                                    $examSubject->user_id = $model->user_id;
                                    $examSubject->subject_id = $subject->subject_id;
                                    $examSubject->type = $subject->type;
                                    $examSubject->ball = 0;
                                    if (!$examSubject->validate()) {
                                        $errors[] = $examSubject->errors;
                                    } else {
                                        $examSubject->save(false);
                                        foreach (json_decode($subject->question_distribution) as $level => $count) {
                                            $questions = Question::find()
                                                ->where([
                                                    'subject_id' => $subject->subject_id,
                                                    'is_checked' => 1,
                                                    'status' => 1,
                                                    'level' => $level ?? 1,
                                                    'is_deleted' => 0,
                                                ])
                                                ->orderBy(new Expression('rand()'))
                                                ->limit($count)
                                                ->all();
                                            if (count($questions) != $count) {
                                                $errors[] = _e('Not enough questions.');
                                            } else {
                                                foreach ($questions as $question) {
                                                    $examStudentQuestion = new ExamStudentQuestion();
                                                    $examStudentQuestion->exam_id = $model->exam_id;
                                                    $examStudentQuestion->user_id = $model->user_id;
                                                    $examStudentQuestion->student_id = $model->student_id;
                                                    $examStudentQuestion->exam_student_id = $model->id;
                                                    $examStudentQuestion->question_id = $question->id;
                                                    $examStudentQuestion->exam_subject_id = $examSubject->id;
                                                    $examStudentQuestion->options = Question::optionsArray($question->id);
                                                    if (!$examStudentQuestion->validate()) {
                                                        $errors[] = $examStudentQuestion->errors;
                                                    } else {
                                                        $examStudentQuestion->save(false);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $model = $examStudent;
            if ($model->status == self::STATUS_ACTIVE) {
                if ($model->finish_time < $time) {
                    $exSubjects = ExamSubject::find()
                        ->where([
                            'exam_id' => $model->exam_id,
                            'user_id' => $model->user_id,
                            'status' => 1,
                            'is_deleted' => 0,
                        ])->all();
                    $all_ball = 0;
                    if (count($exSubjects) > 0) {
                        foreach ($exSubjects as $exSubject) {
                            $correcCount = $exSubject->correctCount;
                            $ball = 0;
                            if ($exSubject->type == DirectionSubject::BLOCK_1) {
                                $ball = DirectionSubject::BLOCK_1 * $correcCount;
                            } elseif ($exSubject->type == DirectionSubject::BLOCK_2) {
                                $ball = DirectionSubject::BLOCK_2 * $correcCount;
                            }
                            $exSubject->ball = $ball;
                            $exSubject->save(false);
                            $all_ball = $all_ball + $ball;
                        }
                    }
                    $model->ball = $all_ball;
                    $model->status = self::STATUS_END;
                    $model->save(false);

                    $student = $examStudent->student;
                    $phone = $student->user->username;

                    $sms = false;
                    if (preg_match('/^\d+$/', $phone)) {
                        if (strlen($phone) == 12) {
                            $sms = true;
                        }
                    }

                    if ($model->ball >= Student::CONTRACT_BALL) {
                        $student->entered = Student::ENTERED;
                        $student->contract_type = Student::CONTRACT_1;

                        if ($sms) {
                            Message::examFinish($student->user->username);
                        }

                    } else {
                        if ($student->attempt_count == 2) {
                            $student->entered = Student::ENTERED;
                            if ($sms) {
                                Message::examFinish($student->user->username);
                            }
                        }
                        $student->contract_type = Student::CONTRACT_1_5;
                    }
                    $student->save(false);
                }
            } elseif ($model->status == self::STATUS_END) {
                $download = Yii::$app->request->get('download');
                if ($download == 1) {
                    $model->download_contract = time();
                    $model->save(false);
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return ['is_ok' => true , 'data' => $model];
        }
        $transaction->rollBack();
        return ['is_ok' => false , 'errors' => simplify_errors($errors)];
    }

    public static function question($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        $examStudent = $model->examStudent;

        if ($examStudent->status == ExamStudent::STATUS_END) {
            $errors[] = _e('You have completed the exam!');
        } else {
            if (!($examStudent->start_time <= $time && $examStudent->finish_time >= $time)) {
                $errors[] = _e('You can study the exam at the appointed time.');
            } else {
                if (isset($post['student_option'])) {
                    $model->student_option = $post['student_option'];
                    if ($model->answerOption($model->question_id) == $model->student_option) {
                        $model->is_correct = 1;
                    } else {
                        $model->is_correct = 0;
                    }
                } else {
                    $errors[] = _e("Option Id not found.");
                }
            }
        }

        if (count($errors) == 0) {
            $model->update(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function finish($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $exSubjects = ExamSubject::find()
            ->where([
                'exam_id' => $model->exam_id,
                'exam_student_id' => $model->id,
                'user_id' => $model->user_id,
                'status' => 1,
                'is_deleted' => 0,
            ])->all();
        $all_ball = 0;
        if (count($exSubjects) > 0) {
            foreach ($exSubjects as $exSubject) {
                $correcCount = $exSubject->correctCount;
                $ball = 0;
                if ($exSubject->type == 1) {
                    $ball = DirectionSubject::BLOCK_1 * $correcCount;
                } elseif ($exSubject->type == 2) {
                    $ball = DirectionSubject::BLOCK_2 * $correcCount;
                }
                $exSubject->ball = $ball;
                $exSubject->save(false);
                $all_ball = $all_ball + $ball;
            }
        }
        $model->ball = $all_ball;
        $model->status = self::STATUS_END;
        $student = $model->student;
        $phone = $student->user->username;

        $sms = false;
        if (preg_match('/^\d+$/', $phone)) {
            if (strlen($phone) == 12) {
                $sms = true;
            }
        }

        if ($model->ball >= Student::CONTRACT_BALL) {
            $student->entered = Student::ENTERED;
            $student->contract_type = Student::CONTRACT_1;
            if ($sms) {
                Message::examFinish($student->user->username);
            }
        } else {
            if ($student->attempt_count == 2) {
                $student->entered = Student::ENTERED;
                if ($sms) {
                    Message::examFinish($student->user->username);
                }
            }
            $student->contract_type = Student::CONTRACT_1_5;
        }
        $student->update(false);

        if (count($errors) == 0) {
            $model->update(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function allFinish($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        $models = ExamStudent::find()
            ->where(['status' => 1])
            ->andWhere(['<' , 'finish_time' , $time])
            ->all();

        if (count($models) > 0) {
            foreach ($models as $model) {
                $exSubjects = ExamSubject::find()
                    ->where([
                        'exam_id' => $model->exam_id,
                        'exam_student_id' => $model->id,
                        'user_id' => $model->user_id,
                        'status' => 1,
                        'is_deleted' => 0,
                    ])->all();
                $all_ball = 0;
                if (count($exSubjects) > 0) {
                    foreach ($exSubjects as $exSubject) {
                        $correcCount = $exSubject->correctCount;
                        $ball = 0;
                        if ($exSubject->type == DirectionSubject::BLOCK_1) {
                            $ball = DirectionSubject::BLOCK_1 * $correcCount;
                        } elseif ($exSubject->type == DirectionSubject::BLOCK_2) {
                            $ball = DirectionSubject::BLOCK_2 * $correcCount;
                        }
                        $exSubject->ball = $ball;
                        $exSubject->save(false);
                        $all_ball = $all_ball + $ball;
                    }
                }
                $model->ball = $all_ball;
                $model->status = self::STATUS_END;

                $student = $model->student;
                if ($model->ball >= Student::CONTRACT_BALL) {
                    $student->entered = Student::ENTERED;
                    $student->contract_type = Student::CONTRACT_1;
                } elseif ($model->ball < Student::CONTRACT_BALL && $model->ball > 0) {
                    $student->entered = Student::ENTERED;
                    $student->contract_type = Student::CONTRACT_1_5;
                } else {
                    $student->entered = Student::NOT_ENTERED;
                }
                $student->attempt_count = $student->attempt_count + 1;
                $student->save(false);
                $model->update(false);
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function increment($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $student = current_student();

        if ($student->attempt_count == 1 && $student->entered != 1 && $student->contract_type == 1.5) {
            $student->attempt_count = 2;
            $student->save(false);
        } else {
            $errors[] = _e("You have your first attempt.");
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function examStdDelete($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $student = $model->student;

        if ($student->contract_type == 1) {
            $errors[] = ['There is no option to delete'];
        } else {
            $student->contract_type = Student::CONTRACT_1_5;
            $student->entered = null;
            $student->save(false);

            $model->is_deleted = 1;
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
