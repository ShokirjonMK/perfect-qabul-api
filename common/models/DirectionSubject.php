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
class DirectionSubject extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const BLOCK_1 = 3.2;
    const BLOCK_2 = 3.1;

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
        return 'direction_subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direction_id','subject_id' ,'question_count','type'], 'required'],
            [['ball', 'max_ball'], 'number', 'min' => 0, 'max' => PHP_FLOAT_MAX],
            [['question_distribution'], 'safe'],
            [['direction_id','subject_id', 'type', 'question_count','is_certificate', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'direction_id',
            'subject_id',
            'question_count',
            'ball',
            'max_ball',
            'question_distribution',
            'is_certificate',
            'type',
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
            'direction',
            'subject',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        } else {
            $query = DirectionSubject::findOne([
                'direction_id' => $model->direction_id,
                'subject_id' => $model->subject_id,
                'is_deleted' => 0,
            ]);
            if ($query) {
                $errors[] = ['subject_id' => _e('This subject was previously added to this direction')];
            } else {
                $isTypeValid = DirectionSubject::findOne([
                    'direction_id' => $model->direction_id,
                    'type' => $model->type,
                    'is_deleted' => 0,
                ]);
                if ($isTypeValid) {
                    $errors[] = ['type' => _e('This block has subject.')];
                } else {
                    if (isset($post['question_distribution'])) {
                        $model->question_distribution = $post['question_distribution'];
                        $levelJson = json_decode($post['question_distribution']);
                        $levelType = new Question();
                        foreach ($levelJson as $level => $count) {
                            if (!$levelType->levelType($level)) {
                                $errors[] = _e('Level not found.');
                            }
                        }
                    }
                    if ($model->type == 1) {
                        $model->ball = self::BLOCK_1;
                    } elseif ($model->type == 2) {
                        $model->ball = self::BLOCK_2;
                    } else {
                        $errors[] = _e('The Type value is invalid');
                    }
                    $model->max_ball = $model->question_count * $model->ball;
                }
            }
        }

        if (count($errors) == 0) {
            $model->save(false);
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
            $query = DirectionSubject::find()
                ->where([
                    'direction_id' => $model->direction_id,
                    'subject_id' => $model->subject_id,
                    'is_deleted' => 0,
                ])
                ->andWhere(['!=' , 'id' , $model->id])
                ->one();
            if ($query) {
                $errors[] = ['subject_id' => _e('This subject was previously added to this direction.')];
            } else {
                $isTypeValid = DirectionSubject::find()
                    ->where([
                        'direction_id' => $model->direction_id,
                        'type' => $model->type,
                        'is_deleted' => 0,
                    ])
                    ->andWhere(['!=' , 'id' , $model->id])
                    ->one();
                if ($isTypeValid) {
                    $errors[] = ['type' => _e('This block has subject.')];
                } else {
                    if (isset($post['question_distribution'])) {
                        $model->question_distribution = $post['question_distribution'];
                        $levelJson = json_decode($post['question_distribution']);
                        $levelType = new Question();
                        foreach ($levelJson as $level => $count) {
                            if (!$levelType->levelType($level)) {
                                $errors[] = _e('Level not found.');
                            }
                        }
                    }
                    if ($model->type == 1) {
                        $model->ball = self::BLOCK_1;
                        $model->max_ball = $model->question_count * $model->ball;
                    } elseif ($model->type == 2) {
                        $model->ball = self::BLOCK_2;
                        $model->max_ball = $model->question_count * $model->ball;
                    } else {
                        $errors[] = _e('The Type value is invalid');
                    }
                }
            }
        }

        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }

        if (count($errors) == 0) {
            $model->update(false);
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
