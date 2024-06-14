<?php

namespace api\forms;

use common\models\Direction;
use api\resources\User;
use common\models\EduForm;
use common\models\Languages;
use common\models\LoginHistory;
use common\models\Student;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class StepThree extends Model
{
    public $edu_form_id;
    public $direction_id;
    public $language_id;

    public $file;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $fileExtension = 'pdf , jpg , png';
    const UPLOADS_FOLDER = 'uploads/student/files/';

    public $exam_form;

    public $certificate_type;
    public $certificate_level;
    public $certificate_level_type;
    public $certificate_file;

    public function rules()
    {
        return [
            [['edu_form_id','direction_id','language_id','certificate_type' , 'exam_form'], 'required'],
            [['edu_form_id','direction_id','certificate_level','certificate_type','certificate_level_type' , 'exam_form'], 'integer'],
            [['certificate_file'], 'string' , 'max' => 255],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
        ];
    }

    public static function stepThree($student, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
        } else {

            if (!($model->exam_form == 0 || $model->exam_form == 1)) {
                $errors[] = ['exam_form' => 'Exam form value error'];
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $model->file = UploadedFile::getInstancesByName('c_file');
            if ($model->file) {
                $model->file = $model->file[0];
                $url = $model->upload($model->file);
                if ($url) {
                    $model->certificate_file = $url;
                } else {
                    $errors[] = $model->errors;
                }
                $student->certificate_file = $model->certificate_file;
            }

//            $student->general_edu_type = $model->general_edu_type;
            $student->edu_form_id = $model->edu_form_id;
            $student->direction_id = $model->direction_id;
            $student->language_id = $model->language_id;
            $student->exam_form = $model->exam_form;


            $student->certificate_type = $model->certificate_type;
            $student->certificate_level = $model->certificate_level;
            $student->certificate_level_type = $model->certificate_level_type;


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

}
