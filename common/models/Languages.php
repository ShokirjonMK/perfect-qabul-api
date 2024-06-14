<?php

namespace common\models;

use Yii;
use api\resources\ResourceTrait;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "languages".
 *
 * @property int $id
 * @property string $name
 * @property string $lang_code
 * @property string $locale
 * @property int $rtl
 * @property int $default
 * @property int $sort
 * @property int $status
 */
class Languages extends \base\libs\RedisDB
{

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{languages}}';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'lang_code'], 'required'],
            [['rtl', 'status', 'sort', 'default'], 'integer'],
            [['name', 'lang_code', 'locale'], 'string'],
            [['rtl', 'status', 'sort'], 'default', 'value' => 0],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'name' => _e('Name'),
            'lang_code' => _e('Code'),
            'locale' => _e('Locale'),
            'rtl' => _e('RTL'),
            'default' => _e('Default'),
            'sort' => _e('Sort'),
            'status' => _e('Status'),
        ];
    }

    /**
     * Get all
     *
     * @param array $where
     * @param string $order_by
     * @return array
     */
    public function getAll($where = array(), $order_by = 'name')
    {
        $query = Languages::find();
        $query->asArray();
        $query->orderBy($order_by);

        if (is_array($where) && $where) {
            $query->where($where);
        }

        $results = $query->all();

        if ($results) {
            foreach ($results as $key => $result) {
                $results[$key]['flag'] = images_url('flags/svg/' . $result['lang_code'] . '.svg');
            }
        }

        return $results;
    }

    /**
     * Get one
     *
     * @param array $where
     * @return array
     */
    public function getOne($where = array())
    {
        $query = Languages::find();
        $query->asArray();

        if (is_array($where) && $where) {
            $query->where($where);
        }

        $result = $query->one();

        if ($result) {
            $result['flag'] = images_url('flags/svg/' . $result['lang_code'] . '.svg');
        }

        return $result;
    }




    public function extraFields()
    {
        $extraFields =  [
            //            'department',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->status = 1;
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->getErrorSummary(true);
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->status = 1;
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->getErrorSummary(true);
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
