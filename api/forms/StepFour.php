<?php

namespace api\forms;

use api\resources\User;
use common\models\Languages;
use common\models\model\LoginHistory;
use common\models\Student;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class StepFour extends Model
{
    public $exam_type;
    public $dtm_file;
    public $exam_file;

    public $file;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $fileExtension = 'pdf , jpg , png';
    const UPLOADS_FOLDER = 'uploads/student/files/';

    const GENERAL_EDUCATION = 1;
    const DTM_RESULT = 2;
    const PEREVOT = 3;

    public function rules()
    {
        return [
            [['exam_type'], 'required'],
            [['exam_type'], 'integer'],
            [['dtm_file'], 'string' , 'max' => 255],
            [['file' , 'exam_file'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
        ];
    }

    public static function stepFour($student, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
        } else {

            if ($model->exam_type == self::DTM_RESULT) {
                $model->file = UploadedFile::getInstancesByName('file');
                if ($model->file) {
                    $model->file = $model->file[0];
                    $url = $model->upload($model->file);
                    if ($url) {
                        $model->dtm_file = $url;
                        $student->dtm_file = $model->dtm_file;
                    } else {
                        $errors[] = $model->errors;
                    }
                }

                $model->exam_file = UploadedFile::getInstancesByName('exam_file');
                if ($model->exam_file) {
                    $model->exam_file = $model->exam_file[0];
                    $url1= $model->upload2($model->exam_file);
                    if ($url1) {
                        $student->permit_file = $url1;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
            }
            $student->exam_type = $model->exam_type;

            if (!$student->validate()){
                $errors[] = $student->errors;
            } else {
                $student->save(false);
                $user = $student->user;
                $user->step = User::STEP_5;
                $user->save(false);
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

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName =  \Yii::$app->security->generateRandomString(15) . '.' . $this->file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function upload2()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName =  \Yii::$app->security->generateRandomString(15) . '.' . $this->exam_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->exam_file->saveAs($url, false);
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

    public function examTypesArray($key = null)
    {
        $array = [
            [
                "id" => self::GENERAL_EDUCATION,
                "name" => 'Umum ta\'lim',
            ],
            [
                "id" => self::DTM_RESULT,
                "name" => 'DTM imtixon natijasiga ko\'ra',
            ],
        ];

        if (isset($array[$key-1])) {
            return $array[$key-1];
        }

        return $array;
    }
}
