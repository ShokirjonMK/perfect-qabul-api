<?php

namespace backend\models;

use Yii;

class Dashboard extends \yii\db\ActiveRecord
{
    /**
     * Get devices count
     *
     * @return array
     */
    public static function getDevicesCount()
    {
        $output = array(
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
            'unknown' => 0,
        );

        $sql = "SELECT *, COUNT(ua_device) as total
            FROM analytics_users
            GROUP BY ua_device
            ORDER BY created_on DESC";

        $db = Yii::$app->getDb();
        $query = $db->createCommand($sql);
        $results = $query->queryAll();

        if ($results) {
            foreach ($results as $item) {
                $type = array_value($item, 'ua_device');
                $total = array_value($item, 'total');

                if (isset($output[$type])) {
                    $output[$type] = $total;
                } else {
                    $output['unknown'] = $total;
                }
            }
        }

        return $output;
    }

    /**
     * Get country count
     *
     * @return array
     */
    public static function getCountryCount()
    {
        $output = array();

        $sql = "SELECT *, COUNT(country_code) as total
            FROM analytics_users
            GROUP BY country_code
            ORDER BY created_on DESC";

        $db = Yii::$app->getDb();
        $query = $db->createCommand($sql);
        $results = $query->queryAll();

        if ($results) {
            foreach ($results as $item) {
                $country_code = array_value($item, 'country_code');
                $total = array_value($item, 'total');
                $output[$country_code] = $total;
            }
        }

        return $output;
    }

    /**
     * Get visitors count
     *
     * @return array
     */
    public static function getVisitorsCount($args = array())
    {
        $output = array();
        $date_to = array_value($args, 'date_to');
        $date_from = array_value($args, 'date_from');

        if ($date_from && $date_to) {
            $sql = "SELECT *, COUNT(uid) as total
                FROM analytics_views
                WHERE (created_on BETWEEN '{$date_from}' AND '{$date_to}')
                GROUP BY uid, DATE_FORMAT(created_on, '%Y%m%d')
                ORDER BY created_on DESC";

            $db = Yii::$app->getDb();
            $query = $db->createCommand($sql);
            $results = $query->queryAll();

            if ($results) {
                foreach ($results as $item) {
                    $total = array_value($item, 'total');
                    $created_on = array_value($item, 'created_on');

                    if ($created_on) {
                        $_date = \DateTime::createFromFormat('Y-m-d H:i:s', $item['created_on'])->format('Y-m-d');

                        if (isset($output[$_date])) {
                            $output[$_date] = ($output[$_date] + $total);
                        } else {
                            $output[$_date] = $total;
                        }
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Get sessions count
     *
     * @return array
     */
    public static function getSessionCount($args = array())
    {
        $output = array();
        $date_to = array_value($args, 'date_to');
        $date_from = array_value($args, 'date_from');

        if ($date_from && $date_to) {
            $sql = "SELECT *, COUNT(uid) as total
                FROM analytics_sessions
                WHERE (created_on BETWEEN '{$date_from}' AND '{$date_to}')
                GROUP BY uid, DATE_FORMAT(created_on, '%Y%m%d')
                ORDER BY created_on DESC";

            $db = Yii::$app->getDb();
            $query = $db->createCommand($sql);
            $results = $query->queryAll();

            if ($results) {
                foreach ($results as $item) {
                    $total = array_value($item, 'total');
                    $created_on = array_value($item, 'created_on');

                    if ($created_on) {
                        $_date = \DateTime::createFromFormat('Y-m-d H:i:s', $item['created_on'])->format('Y-m-d');

                        if (isset($output[$_date])) {
                            $output[$_date] = ($output[$_date] + $total);
                        } else {
                            $output[$_date] = $total;
                        }
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Count online users
     *
     * @return array
     */
    public static function getOnlineUsersCount()
    {
        $time = date('Y-m-d H:i:s', strtotime('-3 minute'));

        $sql = "SELECT COUNT(*) as counts
                FROM analytics_sessions
                WHERE updated_on > '{$time}'
                ORDER BY created_on DESC";

        $db = Yii::$app->getDb();
        $query = $db->createCommand($sql);
        $result = $query->queryOne();

        return array_value($result, 'counts', '0');
    }

    /**
     * Get last visted pages
     *
     * @return array
     */
    public static function getLastVistedPages($args = array())
    {
        $limit = array_value($args, 'limit', 10);

        $sql = "SELECT *
            FROM analytics_views
            ORDER BY created_on DESC
            LIMIT 0, :limit";

        $db = Yii::$app->getDb();
        $query = $db->createCommand($sql)->bindValues(['limit' => $limit]);
        return $query->queryAll();
    }
}
