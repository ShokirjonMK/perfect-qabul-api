<?php

namespace api\resources;

use common\models\model\Student;
use common\models\model\TeacherAccess;
use common\models\model\Translate;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Da\QrCode\QrCode;
use api\resources\User;

trait ResourceTrait
{

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function loadApi($data)
    {
        return $this->load($data, '');
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Get created At
     *
     * @return void
     */
    public function getCreatedAt()
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * Get created At
     *
     * @return void
     */
    public function getUpdatedAt()
    {
        return date('Y-m-d H:i:s', $this->updated_at);
    }

    public static function createFromTable($nameArr, $table_name, $model_id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        foreach ($nameArr as $key => $value) {

            $new_translate[$key] = new Translate();
            $new_translate[$key]->name = $value;
            $new_translate[$key]->table_name = $table_name;
            $new_translate[$key]->model_id = $model_id;
            if ($new_translate[$key]->save()) {
                $transaction->commit();
                return true;
            } else {
                $errors[] = $new_translate[$key]->getErrorSummary(true);
                return simplify_errors($errors);
            }
        }
    }

    public static function teacher_access_user_id($teacher_access_id)
    {
        return TeacherAccess::findOne($teacher_access_id)
            ->user_id ?? null;
    }

    public static function student_now($type = null, $user_id = null)
    {
        if ($user_id == null) {
            $user_id = current_user_id();
        }
        if ($type == null) {
            $type = 1;
        }
        $student = Student::findOne(['user_id' => $user_id]);
        if ($type == 1) {
            return $student->id ?? null;
        } elseif ($type == 2) {
            return $student ?? null;
        }
    }

    public static function student($type = null, $user_id = null)
    {
        if ($user_id == null) {
            $user_id = current_user_id();
        }
        if ($type == null) {
            $type = 1;
        }
        $student = Student::findOne(['user_id' => $user_id]);
        if ($type == 1) {
            return $student->id ?? null;
        } elseif ($type == 2) {
            return $student ?? null;
        }
    }

    public static function findByStudentId($id, $type = null)
    {
        if ($type == null) {
            $type = 1;
        }
        $student = Student::findOne(['id' => $id]);
        if ($type == 1) {
            return $student->user_id ?? null;
        } elseif ($type == 2) {
            return $student ?? null;
        }
    }

    public static function teacher_access($type = null, $select = [], $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }

        if (is_null($type)) {
            $type = 1;
        }

        if (empty($select)) {
            $select = ['id'];
        }
        if ($type == 1) {
            return TeacherAccess::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')])
                ->select($select);
        } elseif ($type == 2) {
            return TeacherAccess::find()
                ->asArray()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')])
                ->select($select)
                ->all();
        }
    }

    public function orderCreate($order, $data, $tableName)
    {
        $orderDescOne = $tableName->find()
            ->where($data)
            ->orderBy('order desc')
            ->one();
        if (isset($orderDescOne)) {
            if ($orderDescOne->order + 1 < $order) {
                $model->order = $orderDescOne->order + 1;
            } elseif ($orderDescOne->order > $order) {
                $orderUpdate = $tableName->find()->where([
                    'between', 'order', $order, $orderDescOne->order
                ])
                    ->andWhere($data)
                    ->all();
                if (isset($orderUpdate)) {
                    foreach ($orderUpdate as $orderItem) {
                        $orderItem->order = $orderItem->order + 1;
                        $orderItem->save();
                    }
                }
            } elseif ($orderDescOne->order == $order) {
                $orderDescOne->order = $orderDescOne->order + 1;
                $orderDescOne->save();
            }
        } else {
            $model->order = 1;
        }
    }

    public function orderUpdate($order, $data, $tableName, $modelOrder)
    {

        if ($order < $modelOrder) {
            $orderUpdate = $tableName->find()->where([
                'between', 'order', $order-1, $modelOrder - 1
            ])->andWhere($data)->all();

            if (isset($orderUpdate)) {
                foreach ($orderUpdate as $orderItem) {
                    $orderItem->order = $orderItem->order + 1;
                    $orderItem->save(false);
                }
            }
        }

        if ($order > $modelOrder) {
            $orderUpdate = $tableName->find()->where([
                'between', 'order', $modelOrder + 1, $order
            ])->andWhere($data)->all();

            if (isset($orderUpdate)) {
                foreach ($orderUpdate as $orderItem) {
                    $orderItem->order = $orderItem->order - 1;
                    $orderItem->save(false);
                }
            }
        }

    }

    public static function encodeMK($key)
    {
        $str = '';
        foreach (str_split((string)$key) as $one) {

            $symKey = (int)$one + 97;
            $str .= chr($symKey);
        }
        return $str;
    }

//    public static function qrCode($user_id)
//    {
//        $user = User::findOne($user_id);
//        $profile = $user->profile;
//        $fullName = $profile->last_name . " " . $profile->first_name . " " . $profile->middle_name;
//        $position = $user->position;
//        $date = date("d-m-Y H:i:s");
//
//        // Sh, Ch, O', G'
//        $text = $profile->first_name . " " . $profile->last_name . PHP_EOL . $position . PHP_EOL . date("d-m-Y H:i:s");
//
//        $qrCode = (new QrCode($text))
//            ->setSize(120)
//            ->setMargin(5);
//
//        return $qrCode->writeDataUri();
//    }

    public static function decodeMK($string)
    {
        // return $string;
        // $string = "ejdg-biebc";
        $num = '';
        foreach (str_split((string)$string) as $one) {
            if ($one == "-") {
                $num .= $one;
            } else {
                $num .= ((int)ord($one) - 97);
            }
        }
        return $num;
    }

}
