<?php

namespace api\resources;

use common\models\Subject as CommonSubject;
use common\models\SubjectInfo;
use Yii;

class Subject extends CommonSubject
{
    use ResourceTrait;

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->info->name ?? '';
            },
            'lang' => function ($model) {
                return $model->info->language ?? '';
            },
            'practical_hours',
            'theoretical_hours',
            'sort',
            'status',
            'created_on',
            'updated_on',
            'created_by',
            'updated_by',
        ];

        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */
    public function extraFields()
    {
        $extraFields =  [
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Get content info
     *
     * @return void
     */
    public function getInfoRelation()
    {
        self::$selected_language = Yii::$app->request->get('lang') ?? 'en';
        return $this->hasMany(SubjectInfo::class, ['subject_id' => 'id'])
                    ->andOnCondition(['language' => self::$selected_language]);
    }

    /**
     * Get info
     *
     * @return void
     */
    public function getInfo()
    {
        return ($this->infoRelation) ? $this->infoRelation[0] : null;
    }

    /**
     * Get department
     *
     * @return void
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->status = 1;         
        if($model->save()){
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];       
                }else{
                    foreach ($post['name'] as $lang => $name) {
                        $info = new SubjectInfo();
                        $info->subject_id = $model->id;
                        $info->language = $lang;
                        $info->name = $name;
                        if (!$info->save()) {
                            $errors[] = $info->getErrorSummary(true);
                        }
                    }
                }
            }else{
                $errors[] = [_e('Please send at least one Name attribute.')];      
            }
        }else{
            $errors[] = $model->getErrorSummary(true);  
        }
        
        if(count($errors) == 0){
            $transaction->commit();
            return true;
        }else{
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    } 

    public static function updateItem($model, $post)
    {
        
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if($model->save()){
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];       
                }else{
                    foreach ($post['name'] as $lang => $name) {
                        $info = SubjectInfo::find()->where(['subject_id' => $model->id, 'language' => $lang])->one();
                        if ($info) {
                            $info->name = $name;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        }
                    }
                }
            }
        }else{
            $errors[] = $model->getErrorSummary(true);  
        }
        
        if(count($errors) == 0){
            $transaction->commit();
            return true;
        }else{
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    } 



}
