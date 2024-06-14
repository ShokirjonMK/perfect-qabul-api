<?php

namespace base\libs;

use Yii;

/**
 * Redis Database
 */
class RedisDB extends \yii\db\ActiveRecord
{
    /**
     * Get table name
     *
     * @return string
     */
    public static function _tableName()
    {
        $table_name = self::getTableSchema()->name;
        $table_name = str_replace(['{{', '}}', '%'], '', $table_name);
        $table_name = trim($table_name);

        return $table_name;
    }

    /**
     * Find
     *
     * @return mixed
     */
    public static function find()
    {
        return \Yii::createObject(RedisActiveQuery::className(), [get_called_class()]);
    }

    /**
     * This method is invoked before deleting a record.
     *
     * @return bool
     */
    public function beforeDelete()
    {
        $table_name = self::_tableName();
        self::clearAll($table_name, $this->redisDbRelationship());

        parent::beforeDelete();
        return true;
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     *
     * @param bool $insert whether this method called while inserting a record.
     * @return bool
     */
    public function beforeSave($insert)
    {
        $table_name = self::_tableName();
        self::clearAll($table_name, $this->redisDbRelationship());

        return parent::beforeSave($insert);
    }

    /**
     * Database replationship
     *
     * @return mixed
     */
    public function redisDbRelationship()
    {
        return array();
    }

    /**
     * Clear all from redis
     *
     * @param string $table_name
     * @return void
     */
    public static function clearAll($table_name, $db_relationship = null)
    {
        $output = false;
        $db_relationship_list = array();

        if ($table_name) {
            $table_name = 'table_' . $table_name;
            $cached_keys = Redis::keys($table_name . '*');

            if ($cached_keys && is_array($cached_keys)) {
                foreach ($cached_keys as $cached_key) {
                    Redis::delete($cached_key, false);
                }

                $output = true;
            } elseif ($cached_keys && is_string($cached_keys)) {
                $output = true;
                Redis::delete($cached_keys, false);
            }
        }

        if (is_object($db_relationship)) {
            $db_relationship_list[] = $db_relationship;
        } elseif (is_array($db_relationship) && $db_relationship) {
            $db_relationship_list = $db_relationship;
        }

        if ($db_relationship_list) {
            foreach ($db_relationship_list as $relationship) {
                $table_name = '';

                if (is_object($relationship) && method_exists($relationship, '_tableName')) {
                    $table_name = $relationship::_tableName();
                }

                if ($table_name) {
                    $table_name = 'table_' . $table_name;
                    $cached_keys = Redis::keys($table_name . '*');

                    if ($cached_keys && is_array($cached_keys)) {
                        foreach ($cached_keys as $cached_key) {
                            Redis::delete($cached_key, false);
                        }
                    } elseif ($cached_keys && is_string($cached_keys)) {
                        Redis::delete($cached_keys, false);
                    }
                }
            }
        }

        return $output;
    }
}

class RedisActiveQuery extends \yii\db\ActiveQuery
{
    private $redis_active = true;
    private $redis_cache_key = null;

    /**
     * Get table name
     *
     * @return string
     */
    private function _tableName()
    {
        $table_name = $this->getPrimaryTableName();
        $table_name = str_replace(['{{', '}}', '%'], '', $table_name);
        $table_name = trim($table_name);

        return $table_name;
    }

    /**
     * Executes query and returns all results as an array.
     *
     * @param Connection|null $db
     * @return mixed
     */
    public function all($db = null)
    {
        $sql = $this->createCommand()->getRawSql();
        $cached_results = $this->redisResults($sql, 'get_all', $db);

        if (!is_null($cached_results)) {
            return $cached_results;
        }

        $query = parent::all($db);
        return $this->redisOutput($query);
    }

    /**
     * Executes query and returns a single row of result.
     *
     * @param Connection|null $db
     * @return mixed
     */
    public function one($db = null)
    {
        $sql = $this->createCommand()->getRawSql();
        $cached_results = $this->redisResults($sql, 'get_one', $db);

        if (!is_null($cached_results)) {
            return $cached_results;
        }

        $query = parent::one($db);
        return $this->redisOutput($query);
    }

    /**
     * Queries a scalar value by setting [[select]] first.
     * Restores the value of select to make this query reusable.
     * @param string|ExpressionInterface $selectExpression
     * @param Connection|null $db
     * @return bool|string
     */
    protected function queryScalar($selectExpression, $db)
    {
        $modelClass = $this->modelClass;
        $sql = $this->createCommand()->getRawSql();

        if ($db === null) {
            $db = $modelClass::getDb();
        }

        if ($sql === null) {
            return \yii\db\Query::queryScalar($selectExpression, $db);
        }

        $command = (new \yii\db\Query())->select([$selectExpression])
            ->from(['c' => "({$sql})"])
            ->params($this->params)
            ->createCommand($db);

        $this->setCommandCache($command);
        $sql = $command->sql;
        $cached_results = $this->redisResults($sql, 'get_scalar', $db);

        if (!is_null($cached_results)) {
            return $cached_results;
        }

        $scalar = $command->queryScalar();
        return $this->redisOutput($scalar);
    }

    /**
     * Enable or disable redis on query build
     *
     * @param bool $withRedis
     * @return mixed
     */
    public function redis($withRedis = true)
    {
        $this->redis_active = $withRedis;
        return $this;
    }

    /**
     * Create redis cache key
     *
     * @param string $sql
     * @param Connection|null $db
     * @return void
     */
    private function redisCacheInit($sql, $action, $db = null)
    {
        if ($this->redis_active && Redis::is_active()) {
            $tableName = $this->_tableName();

            if ($this->asArray) {
                $action = $action . '_asArray_';
            }

            if ($this->with) {
                $with_sql = array();

                foreach ($this->with as $with_key => $with_item) {
                    if (is_string($with_item)) {
                        $with_sql[$with_item] = '';
                    } elseif (is_object($with_item)) {
                        $query = new \yii\db\Query;
                        $with_item($query);
                        $with_sql[$with_key] = $query->createCommand()->getRawSql();
                    }
                }

                if ($with_sql) {
                    $with_str = serialize($with_sql);
                    $action = $action . '_with_' . md5($with_str);
                }
            }

            if ($sql && $tableName) {
                $tableName = 'table_' . $tableName;
                $this->redis_cache_key = $tableName . '_sql_' . $action . '_' . md5($sql);
            }

            Yii::debug('Redis sql: ' . $sql);
            Yii::debug('Redis sql cache: ' . $this->redis_cache_key);
        }
    }

    /**
     * Get redis results
     *
     * @param string $sql
     * @param Connection|null $db
     * @return mixed
     */
    private function redisResults($sql, $db = null)
    {
        $output = null;

        if ($this->redis_active && Redis::is_active()) {
            $this->redisCacheInit($sql, $db);

            if ($this->redis_cache_key) {
                $cached_item = Redis::get($this->redis_cache_key);

                if (!is_null($cached_item) && is_string($cached_item)) {
                    $hash = Redis::crypt('decrypt', $cached_item);
                    $output = unserialize($hash);
                }
            }
        }

        return $output;
    }

    /**
     * Get data and insert to redis
     *
     * @param mixed $results
     * @return mixed
     */
    private function redisOutput($results)
    {
        if ($this->redis_active && $this->redis_cache_key && Redis::is_active()) {
            $serialize = serialize($results);
            $hash = Redis::crypt('encrypt', $serialize);
            Redis::set($this->redis_cache_key, $hash);
        }

        return $results;
    }
}