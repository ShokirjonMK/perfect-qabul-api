<?php

namespace api\resources;

use common\models\Reference;
use common\models\Subject;
use common\models\User;
use common\models\UserSubject;
use Yii;
use yii\base\Model;

class AccessControl extends Model
{

    public $user_id;
    public $subject_id;
    public $language_ids;

    public $auth;

    public function __construct()
    {
        $this->auth = Yii::$app->authManager;
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'subject_id', 'language_ids'], 'required'],
            [['user_id', 'subject_id'], 'integer'],
            [['language_ids'], 'string'],
        ];
    }

    public static function getRoles()
    {
        $auth = Yii::$app->authManager;
        return $auth->getRoles();
    }

    public static function createRole($model, $body)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validate data
        // check user_id
        $user = User::findOne($model->user_id);
        if (!$user) {
            $errors[] = [_e('User not found.')];
        }

        $bodyObj = json_decode($body);
        foreach ($bodyObj as $obj) {
            // check subject_id
            $subject = Subject::findOne($obj->subject_id);
            if (!$subject) {
                $errors[] = [_e('Subject with ID {subject_id} not found.', ['subject_id' => $obj->subject_id])];
            }

            // check language_ids
            $langs = Reference::find()->where(['type' => 'language', 'id' => $obj->language_ids])->all();
            if (!$langs || count($langs) != count($obj->language_ids)) {
                $errors[] = [_e('Languages with ID {language_ids} not found.', ['language_ids' => implode(',', $obj->language_ids)])];
            }
        }
        //

        // delete old bindings
        UserSubject::deleteAll(['user_id' => $model->user_id]);

        foreach ($bodyObj as $obj) {
            foreach ($obj->language_ids as $lang) {
                $userSubject = new UserSubject();
                $userSubject->user_id = $model->user_id;
                $userSubject->subject_id = $obj->subject_id;
                $userSubject->language_id = $lang;
                if (!$userSubject->save()) {
                    $errors[] = $userSubject->getErrorSummary(true);
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

    public static function getSubjects($employee_id)
    {

        // check user_id
        $errors = [];
        $data = [];
        $user = User::findOne($employee_id);
        if (!$user) {
            $errors[] = [_e('User not found.')];
        }

        if (count($errors) == 0) {
            $userSubjects = UserSubject::find()->where(['user_id' => $employee_id])->all();

            $subjects = [];
            foreach ($userSubjects as $one) {
                $subjects[] = $one->subject_id;
            }
            $subjects = array_unique($subjects);

            foreach ($subjects as $subj) {
                $langs = [];
                foreach ($userSubjects as $one) {
                    if ($subj == $one->subject_id) {
                        $langs[] = $one->language_id;
                    }
                }
                $data[] = [
                    'subject_id' => $subj,
                    'language_ids' => $langs
                ];
            }
        }

        $data = [
            'user_id' => $user->id,
            'subjects' => $data
        ];

        if (count($errors) > 0) {
            return ['is_ok' => false, 'errors' => $errors];
        } else {
            return ['is_ok' => true, 'data' => $data];
        }
    }
}
