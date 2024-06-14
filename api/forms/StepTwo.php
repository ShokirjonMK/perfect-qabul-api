<?php

namespace api\forms;

use api\resources\User;
use common\models\Languages;
use common\models\model\LoginHistory;
use common\models\Student;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class StepTwo extends Model
{
    public $edu_type;
    public $edu_name;
    public $diploma_type;
    public $diploma_file;
    public $certificate_type;
    public $certificate_level;
    public $certificate_level_type;
    public $certificate_file;
    public $exam_form;

    const ACADEMIC_LYCEUM = 1;
    const COLLEGE = 2;
    const SECONDARY_SCHOOL = 3;
    const PRIVATE_SCHOOL = 4;
    const BACHELOR = 5;


    const IELTS_BRITISH_COUNCIL = 1;
    const IELTS_IDP = 2;
    const IELTS_INDICATOR = 3;
    const CEFR = 4;
    const BOSHQA = 5;

    const B2 = 1;
    const C1 = 2;
    const C2 = 3;

    public $file;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb
    public $fileExtension = 'pdf , jpg , png';
    const UPLOADS_FOLDER = 'uploads/student/files/';

    public function rules()
    {
        return [
            [['edu_type','edu_name','diploma_type','certificate_type' , 'exam_form'], 'required'],
            [['edu_type','diploma_type','certificate_level','certificate_type','certificate_level_type','exam_form'], 'integer'],
            [['edu_name','certificate_file','diploma_file'], 'string' , 'max' => 255],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->fileExtension, 'maxSize' => $this->fileMaxSize],
        ];
    }

    public static function stepTwo($student, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
        } else {

            if (!($model->exam_form == 0 || $model->exam_form == 1)) {
                $errors[] = ['exam_form' => 'Exam form value error'];
            } else {
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

            $model->file = UploadedFile::getInstancesByName('d_file');
            if ($model->file) {
                $model->file = $model->file[0];
                $url = $model->upload($model->file);
                if ($url) {
                    $model->diploma_file = $url;
                } else {
                    $errors[] = $model->errors;
                }
                $student->diploma_file = $model->diploma_file;
            }

            $student->edu_type = $model->edu_type;
            $student->edu_name = $model->edu_name;
            $student->diploma_type = $model->diploma_type;

            $student->certificate_type = $model->certificate_type;
            $student->certificate_level = $model->certificate_level;
            $student->certificate_level_type = $model->certificate_level_type;

            if (!$student->validate()){
                $errors[] = $student->errors;
            } else {
                $student->save(false);
                $user = $student->user;
                $user->step = User::STEP_3;
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

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

    public function eduTypesArray($key = null)
    {
        $array = [
            [
                "id" => self::ACADEMIC_LYCEUM,
                "name" => 'Akademik litsey',
            ],
            [
                "id" => self::COLLEGE,
                "name" => 'Kollej',
            ],
            [
                "id" => self::SECONDARY_SCHOOL,
                "name" => 'O\'rta maktab',
            ],
            [
                "id" => self::PRIVATE_SCHOOL,
                "name" => 'Xususiy maktab'
            ],
            [
                "id" => self::BACHELOR,
                "name" => 'Bakalavr'
            ],
        ];

        if (isset($array[$key-1])) {
            return $array[$key-1];
        }

        return $array;
    }

    public function sertificateArray($key = null, $level = null)
    {
        $array = [
            [
                "id" => self::IELTS_BRITISH_COUNCIL,
                "name" => 'IELTS British Council',
            ],
            [
                "id" => self::IELTS_IDP,
                "name" => 'IELTS IDP',
            ],
            [
                "id" => self::IELTS_INDICATOR,
                "name" => 'IELTS Indicator',
            ],
            [
                "id" => self::CEFR,
                "name" => 'CEFR',
                'level' => [
                    [
                        'id' => self::B2,
                        'name' => 'B2',
                    ],
                    [
                        'id' => self::C1,
                        'name' => 'C1',
                    ],
                    [
                        'id' => self::C2,
                        'name' => 'C2',
                    ],
                ]
            ],
            [
                "id" => self::BOSHQA,
                "name" => 'Boshqalar'
            ],
        ];

        if (isset($array[$key-1])) {
            if (isset($array[$level-1]) && isset($array[$key-1]['level'])) {
                return $array[$key-1]['level'][$level-1];
            }
            return $array[$key-1];
        }

        return $array;
    }
}
