<?php

namespace common\models;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Option;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Predis\Configuration\Options;
use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\Subject;
use yii\db\Expression;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $subject_id
 * @property string $file
 * @property string $text
 * @property int $level
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class Question extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public $upload;
    public $fileMaxSize = 1024 * 1024 * 2; // 2 Mb
    public $fileExtension = 'png, jpg';

    const UPLOADS_FOLDER = 'uploads/question/';

    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;

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
        return 'question';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                ['subject_id'] , 'required'
            ],
            [['text'] , 'safe'],
            [['file'] , 'string' , 'max' => 255],
            [['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
            [['subject_id','level','is_checked','order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'subject_id',
            'text',
            'file',
            'level',
            'is_checked',
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
            'options',
            'subject',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getOptions()
    {
        return $this->hasMany(Option::className(), ['question_id' => 'id'])->where(['status' => 1 , 'is_deleted' => 0]);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public static function optionsArray($id) {
        $options = Option::find()
            ->select('id')
            ->where([
                'question_id' => $id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->orderBy(new Expression('rand()'))
            ->asArray()->all();
        return json_encode($options);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('upload');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->is_checked = 0;
        if (count($errors) == 0) {
            $model->save();
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function ischeck($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['is_checked'])) {
            $options = $model->options;
            if (count($options) == 0) {
                $errors[] = _e("There are no answer options!");
            } else {
                $isCorrectTrue = false;
                foreach ($options as $option) {
                    if ($option->is_correct == 1) {
                        $isCorrectTrue = true;
                    }
                }
                if (!$isCorrectTrue) {
                    $errors[] = _e("There is no correct answer among the answer options.");
                }
            }
            $model->is_checked = $post['is_checked'];
            if (!$model->save(false)) {
                $errors[] = $model->errors;
            }
        } else {
            $errors[] = ['is_checked' => _e('Is Checked required!')];
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

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->upload = UploadedFile::getInstancesByName('upload');
        if ($model->upload) {
            $model->upload = $model->upload[0];
            $fileUrl = $model->upload();
            if ($fileUrl) {
                $model->file = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        $model->is_checked = 0;
        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(12) . '.' . $this->upload->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->upload->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

    public function levelType($key)
    {
        $t = false;
        if ($key == self::LEVEL_ONE || $key == self::LEVEL_TWO || $key == self::LEVEL_THREE) {
            $t = true;
        }
        return  $t;
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
