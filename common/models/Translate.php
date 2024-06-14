<?php

namespace common\models;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "translate".
 *
 * @property int $id
 * @property string $name
 * @property string $table_name
 * @property int $language
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Languages $languages
 */
class Translate extends \yii\db\ActiveRecord
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
     * used table names:
     * faculty 
     * building
     * 
     * room
     * direction
     * kafedra
     * edu_type
     * subject
     * subject_type
     *
     * 
     * 
     * semestr
     * course
     * para
     * SubjectCategory
     * ExamsType
     * EduYear
     */

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'translate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'model_id', 'table_name', 'language'], 'required'],
            [['model_id', 'language', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['name', 'table_name', 'description'], 'string', 'max' => 255],
            [['language'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'model id',
            'name' => 'Name',
            'table_name' => 'Table Name',
            'language' => 'Languages ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /**
     * Gets query for [[Languages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language']);
    }

    public function extraFields()
    {
        $extraFields =  [
            'languages',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
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


    public static function createTranslate($nameArr, $table_name, $model_id, $descArr = null)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        foreach ($nameArr as $key => $value) {
            if ($value != 'undefined' && $value != 'null' && $value != '') {
                $new_translate = new Translate();
                $new_translate->name = $value;
                $new_translate->table_name = $table_name;
                $new_translate->model_id = $model_id;
                $new_translate->language = $key;

                $new_translate->description = isset($descArr[$key]) ? (($descArr[$key] != "undefined" && $descArr[$key] != "null" && $descArr[$key] != "") ? $descArr[$key] : null) : null;
                if ($new_translate->save(false)) {
                } else {
                    $errors[] = $new_translate->getErrorSummary(true);
                    return simplify_errors($errors);
                }
            }
        }
        $transaction->commit();
        return true;
    }

    public static function updateTranslate($nameArr, $table_name, $model_id, $descArr = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        //$deleteAll = Translate::deleteAll(['model_id' => $model_id]);
        if (isset($nameArr)) {
            foreach ($nameArr as $key => $value) {
                if ($value != 'undefined' && $value != 'null' && $value != '') {
                    $update_tranlate = Translate::find()->where(['model_id' => $model_id, 'table_name' => $table_name, 'language' => $key])->one();
                    if (isset($update_tranlate)) {
                        $update_tranlate->name = $value;
                        $update_tranlate->description = isset($descArr[$key]) ? (($descArr[$key] != "undefined" && $descArr[$key] != "null" && $descArr[$key] != "") ? $descArr[$key] : null) : null;

                        if ($update_tranlate->save(false)) {
                        } else {
                            $errors[] = $update_tranlate->getErrorSummary(true);
                            $transaction->rollBack();
                            return $errors;
                        }
                    } else {
                        $new_translate = new Translate();
                        $new_translate->name = $value;
                        $new_translate->table_name = $table_name;
                        $new_translate->model_id = $model_id;
                        $new_translate->language = $key;
                        $new_translate->description = isset($descArr[$key]) ? (($descArr[$key] != "undefined" && $descArr[$key] != "null" && $descArr[$key] != "") ? $descArr[$key] : null) : null;
                        if ($new_translate->save(false)) {
                        } else {
                            $errors[] = $new_translate->getErrorSummary(true);
                            $transaction->rollBack();
                            return $errors;
                        }
                    }
                }
            }
        }

        $transaction->commit();
        return true;
    }


    public static function deleteTranslate($table_name, $model_id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $delete_tranlate = Translate::find()->where(['model_id' => $model_id, 'table_name' => $table_name])->all();
        foreach ($delete_tranlate as $delete_one) {
            $delete_one->is_deleted = 1;
            $delete_one->save(false);
            if ($delete_one->save(false)) {
            } else {
                $errors[] = $delete_one->getErrorSummary(true);
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $transaction->commit();
        return true;
    }

    public static function checkingAll($post)
    {
        $languages = Languages::find()
            ->asArray()
            ->where(['status' => 1])
            ->select('lang_code')
            ->all();

        $langCodes = [];
        foreach ($languages as $itemLang) {
            $langCodes[] = $itemLang['lang_code'];
        }

        $data = [];
        $errors = [];
        $data['status'] = 1;

        if (isset($post['name'])) {
            if (!is_array($post['name'])) {
                $errors[]['name'] = [_e('Name must be array.')];
//                 $data['errors'][] = $errors;
                $data['status'] = 0;
            } else {
                $nameErrors = [];
                foreach ($post['name'] as $lang => $value) {
                    if (!in_array($lang, $langCodes)) {
                        $nameErrors['name[' . $lang . ']'] = [_e('Wrong language code selected (' . $lang . ').')];
                        // $data['errors'][] = $errors;
                        $data['status'] = 0;
                    }
                }
                $errors[] = $nameErrors;
            }
        } else {
            $errors[]['name'] = [_e('Please send Name attribute as array.')];
            // $data['errors'][] = $errors;
            $data['status'] = 0;
        }
        if (isset($post['description'])) {
            if (!is_array($post['description'])) {
                $errors[]['description'] = [_e('Description must be array.')];
                // array_push($errors, 'description', [_e('Description must be array.')]);
                // $data['errors'][] = $errors;
                $data['status'] = 0;
            } else {
                $descriptionErrors = [];
                foreach ($post['description'] as $lang => $value) {
                    if (!in_array($lang, $langCodes)) {
                        $errors[]['description[' . $lang . ']'] = [_e('Wrong language code selected (' . $lang . ').')];
                        // $data['errors'][] = $errors;
                        $data['status'] = 0;
                    }
                }
                $errors[] = $descriptionErrors;
            }
        }

        $data['errors'] = $errors;
        return $data;
    }


    public static function checkingUpdate($post)
    {
        $languages = Languages::find()
            ->asArray()
            ->where(['status' => 1])
            ->select('lang_code')
            ->all();

        $langCodes = [];
        foreach ($languages as $itemLang) {
            $langCodes[] = $itemLang['lang_code'];
        }

        $data = [];
        $errors = [];
        $data['status'] = 1;

        if (isset($post['name'])) {
            if (!is_array($post['name'])) {
                $errors[]['name'] = [_e('Name must be array.')];
                // $data['errors'][] = $errors;
                $data['status'] = 0;
            } else {
                $nameErrors = [];
                foreach ($post['name'] as $lang => $value) {

                    if (!in_array($lang, $langCodes)) {
                        $nameErrors['name[' . $lang . ']'] = [_e('Wrong language code selected (' . $lang . ').')];
                        // $data['errors'][] = $errors;
                        $data['status'] = 0;
                    }
                }
                $errors[] = $nameErrors;
            }
        } 
        if (isset($post['description'])) {
            if (!is_array($post['description'])) {
                $errors[]['description'] = [_e('Description must be array.')];
                // array_push($errors, 'description', [_e('Description must be array.')]);
                // $data['errors'][] = $errors;
                $data['status'] = 0;
            } else {
                $descriptionErrors = [];
                foreach ($post['description'] as $lang => $value) {
                    if (!in_array($lang, $langCodes)) {
                        $errors[]['description[' . $lang . ']'] = [_e('Wrong language code selected (' . $lang . ').')];
                        // $data['errors'][] = $errors;
                        $data['status'] = 0;
                    }
                }
                $errors[] = $descriptionErrors;
            }
        }

        $data['errors'] = $errors;
        return $data;
    }
}
