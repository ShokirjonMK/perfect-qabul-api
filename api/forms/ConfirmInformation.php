<?php

namespace api\forms;

use api\resources\User;
use common\models\Languages;
use common\models\Student;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ConfirmInformation extends Model
{

    public static function stepConfirm($student) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$student->validate()){
            $errors[] = $student->errors;
        } else {
            $student->save(false);
            $user = $student->user;
            $user->step = User::STEP_6;
            $user->save(false);
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
