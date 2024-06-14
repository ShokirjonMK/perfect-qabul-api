<?php

namespace common\models;

use api\resources\ResourceTrait;
use common\models\Translate;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_year".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class EduYear extends \yii\db\ActiveRecord
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
        return 'edu_year';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['start_year', 'end_year', 'type'], 'required',],
            [[
                'start_year',
                'end_year',
                'order',
                'status',
                'type',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted'
            ], 'integer'],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function($model) {
                return $model->start_year. " - ". $model->end_year . " - ". $model->type;
            },
            'type',
            'order',
            'start_year',
            'end_year',
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
            'eduPlans',
            'eduSemestrs',
            'timeTables',
            'description',
            'teacherCheckingType',
            'activeEduYear',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Get Tranlate
     *
     * @return void
     */
    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }


    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }


    public function getDescription()
    {
        return $this->translate->description ?? '';
    }


    public static function createItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['year'])) {
            $yearPost = $post['year'];
            $year = EduYear::find()
                ->orderBy('id desc')
                ->one();
            if (isset($year)) {
                if (!($yearPost > $year->end_year)) {
                    $errors[] = ['year' => _e('Created before '.$yearPost)];
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($year->type > 2 || $year->type < 1) {
                    $errors[] = ['type' => _e('The type value is invalid')];
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                if ($year->type == 1) {
                    $model = new EduYear();
                    $model->start_year = $year->start_year;
                    $model->end_year = $year->end_year;
                    $model->type = 2;
                    $model->status = 0;
                    if (!$model->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }
                    $start_date = $model->end_year;
                }
                if ($year->type == 2) {
                    $start_date = $year->end_year;
                }

                for ($i=$start_date; $i<=$yearPost; $i++) {
                    $model = new EduYear();
                    $model->start_year = $i;
                    $model->end_year = $i+1;
                    $model->type = 1;
                    $model->status = 0;
                    if (!$model->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }

                    $model2 = new EduYear();
                    $model2->start_year = $i;
                    $model2->end_year = $i+1;
                    $model2->type = 2;
                    $model2->status = 0;
                    if (!$model2->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }
                }

            } else {
                for ($i=2020; $i<=2030; $i++) {
                    $model = new EduYear();
                    $model->start_year = $i;
                    $model->end_year = $i+1;
                    $model->type = 1;
                    $model->status = 0;
                    if (!$model->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }

                    $model2 = new EduYear();
                    $model2->start_year = $i;
                    $model2->end_year = $i+1;
                    $model2->type = 2;
                    $model2->status = 0;
                    if (!$model2->save()){
                        $errors[] = ['error' => _e('Error saving data')];
                    }
                }
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        EduYear::updateAll(['status' => 0], ['status' => 1]);

        $model->status = 1;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save(false)) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
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
