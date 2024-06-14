<?php

/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties array list of properties (property => [type, name. comment]) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data) : ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)) : ?>
 *
<?php foreach ($relations as $name => $relation) : ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif;  $has_tranlate = true ?>

 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    public static $selected_language = 'uz';
    
    use ResourceTrait;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

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
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db') : ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
    return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n") ?>
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        <?php foreach ($labels as $name => $label) : ?>
    <?= "'$name' => _e('" . $label . "'),\n" ?>
        <?php endforeach; ?>
        <?php if(str_contains($generator->generateString($label), 'Yii::t')){
            $has_tranlate = true;} else{
            $has_tranlate = false;} ?>
           
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            <?php if($has_tranlate) echo '"name" => function ($model) {
                return $model->translate->name ?? "";
            },' ?>

        <?php foreach ($labels as $name => $label) : ?>
    <?php if($has_tranlate){if($name != 'name'){echo "'$name'" . ",\n";}}else{echo "'$name'" . ",\n";}?>
        <?php endforeach; ?>
];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
    <?php foreach ($relations as $name => $relation) : ?>
    <?php $name = lcfirst($name); ?>
    <?= "'$name'" . "," ?>

    <?php endforeach; ?>
        
            <?php if($has_tranlate) echo "'description'," ?>    
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    <?php if($has_tranlate) echo '
    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), "lang_code", "en");
        return $this->hasMany(Translate::class, ["model_id" => "id"])
            ->andOnCondition(["language" => Yii::$app->request->get("lang"), "table_name" => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), "lang_code", "en");
        return $this->hasMany(Translate::class, ["model_id" => "id"])
            ->andOnCondition(["language" => self::$selected_language, "table_name" => $this->tableName()]);
    }

    /**
     * Get Tranlate
     *
     * @return void
     */
    public function getTranslate()
    {
        if (Yii::$app->request->get("self") == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->translate->description ?? "";
    }
' ?>

    <?php foreach ($relations as $name => $relation) : ?>

    /**
     * Gets query for [[<?= $name ?>]].
     *
     * @return <?= $relationsClassHints[$name] . "\n" ?>
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>

    /**
     * <?= $className ?> createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // some logic for creating

        <?php if($has_tranlate){ echo '$has_error = Translate::checkingAll($post);

        if ($has_error["status"]) {
            if ($model->save()) {
                if (isset($post["description"])) {
                    Translate::createTranslate($post["name"], $model->tableName(), $model->id, $post["description"]);
                } else {
                    Translate::createTranslate($post["name"], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error["errors"]);
        }';}else{
        echo 'if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }';
    }?>
    
    }

    /**
     * <?= $className ?> updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        
        // some logic for updating

        <?php if($has_tranlate){ echo '$has_error = Translate::checkingUpdate($post);
        if ($has_error["status"]) {
            if ($model->save()) {
                if (isset($post["name"])) {
                    if (isset($post["description"])) {
                        Translate::updateTranslate($post["name"], $model->tableName(), $model->id, $post["description"]);
                    } else {
                        Translate::updateTranslate($post["name"], $model->tableName(), $model->id);
                    }
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error["errors"]);
        }';}else{
    echo 'if ($model->save()) {
        $transaction->commit();
        return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }';
}?>

    }
<?php if ($queryClassName) : ?>
    <?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
    ?>
    /**
     * {@inheritdoc}
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),
        ];
    }
    public static function typesList()
    {
        return [
            // types here
            self::TYPE_INACTIVE => _e('TYPE_INACTIVE'),

        ];
    }
}
