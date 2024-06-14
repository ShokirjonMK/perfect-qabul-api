<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\BaseConsole;

class SetupController extends Controller
{
    public $preview;
    public $db_data;
    public $url_data;
    public $setup_app;

    private $overwrite;
    private $overwrite_inc;

    private function welcomeMessage()
    {
        echo "-------------------------------------------------------\n";
        echo "\n";
        echo "Content Management Framework Setup\n";
        echo "Created by ShokirjonMK\n";
        echo "\n";
        echo "-------------------------------------------------------\n\n";
    }

    public function actionIndex()
    {
        $this->welcomeMessage();

        echo "Welcome to the application setup wizard :)\n";
        echo "Before using CMF, you need to set up site URL and database configuration.\n";
        echo "\n";
        echo "Please enter setup/app to install the application.\n";
        echo "Or enter setup/create-config to create configurations with default values.\n";
        echo "\n";
    }

    public function actionApp()
    {
        $this->reset('db_data');
        $this->reset('url_data');

        $this->welcomeMessage();

        echo "Application setup wizard\n";
        echo "Set up site URL and database to generate configuration files to run the application.\n";
        echo "\n";

        $this->checkConfigFiles();
        $this->initAppConsole();
        $this->clearTemp();
    }

    public function actionUpdate()
    {
        $this->reset('db_data');
        $this->reset('url_data');

        $this->welcomeMessage();

        echo "Updating configurations...\n";

        $this->consoleConfig();
        $this->clearTemp();

        echo "Application update has been successfully finished. You can now start using the app. \n";
    }

    public function actionCreateConfig()
    {
        $this->db_data = array();
        $this->url_data = array();

        $this->welcomeMessage();

        echo "Application setup wizard\n";
        echo "Before using CMF, you need to set up site URL and database configuration.\n";
        echo "\n";
        echo "This command will create default configuration files. After that, you need to edit them and set up entries to use the application.\n";
        echo "\n";

        $this->checkConfigFiles();

        if ($this->overwrite) {
            $startMessage = 'yes';
        } else {
            $startMessage = BaseConsole::input("Do you want to create application config files? (yes|no): ");
        }

        if ($startMessage == 'yes') {
            $_config_inc = dirname(__DIR__) . '/setup/config.inc.php';
            $_config_theme = dirname(__DIR__) . '/setup/config.theme.php';

            if (is_file($_config_inc) && is_file($_config_theme)) {
                $create_inc_file = $this->configIncFile();
                $create_theme_file = $this->configThemeFile();

                if ($create_inc_file && $create_theme_file) {
                    $this->consoleConfig();
                    $this->clearTemp();
                    echo "Configuration files have been successfully created. Now you can edit them to set up :) \n";
                } elseif ($create_inc_file || $create_theme_file) {
                    $this->consoleConfig();
                    $this->clearTemp();
                    echo "Configuration file has been successfully created. Now you can edit them to set up :) \n";
                } else {
                    echo "Nothing to update :)\n";
                }
            } else {
                echo "\n";
                echo "Oops, an error occurred! \n";
                echo "The installation config files were not found. Unable to create file :( \n";
            }
        } else {
            echo "Bye bye :))\n";
        }
    }

    private function checkConfigFiles()
    {
        $this->overwrite = false;
        $this->overwrite_inc = false;

        $config_inc = dirname(dirname(__DIR__)) . '/config.inc.php';

        if (is_file($config_inc)) {
            $i = 0;
            $resp = false;
            $this->overwrite = true;

            do {
                $i++;
                $message = BaseConsole::input("The file 'config.inc.php' was found. Do you want to overwrite it? (yes|no): ");
                $message = strtolower($message);

                if ($message == 'yes') {
                    $resp = true;
                    $this->overwrite_inc = true;
                } elseif ($message == 'no') {
                    $resp = true;
                    $this->overwrite_inc = false;
                } else {
                    $resp = false;
                }
            } while (!$resp);
        }
    }

    private function initAppConsole()
    {
        if ($this->overwrite && !$this->overwrite_inc) {
            echo "\n";
            echo "Skip updating 'config.in.php' file... \n";

            $this->configThemeFile();
            return;
        } else {
            $this->setURL();
            echo "\n";
            echo "Great, now let's set up the database :) \n";

            $this->setDatabse();
            echo "\n";
            echo "All right! I am creating config files now, please wait... \n";

            $this->checkingData();

            if ($this->setup_app) {
                $this->consoleConfig();

                echo "Well done :)\n";
                echo "Configuration files have been successfully created. You can now start using the app. \n";
                echo "\n";
            }
        }
    }

    private function setURL()
    {
        // Site URL
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Site URL (Eg: https://example.com/) ");
            $string = preg_replace('/\s+/', '', $input);

            if (filter_var($string, FILTER_VALIDATE_URL)) {
                $this->url_data['site_url'] = $string;
            } else {
                echo "Please enter a valid URL! \n";
            }
        } while (empty($this->url_data['site_url']));

        // API URL
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("API URL (Eg: https://api.example.com/) ");
            $string = preg_replace('/\s+/', '', $input);

            if (filter_var($string, FILTER_VALIDATE_URL)) {
                $this->url_data['api_url'] = $string;
            } else {
                echo "Please enter a valid URL! \n";
            }
        } while (empty($this->url_data['api_url']));

        // Admin URL
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Admin URL (Eg: https://admin.example.com/) ");
            $string = preg_replace('/\s+/', '', $input);

            if (filter_var($string, FILTER_VALIDATE_URL)) {
                $this->url_data['admin_url'] = $string;
            } else {
                echo "Please enter a valid URL! \n";
            }
        } while (empty($this->url_data['admin_url']));

        // Assets URL
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Assets URL (Eg: https://assets.example.com/) ");
            $string = preg_replace('/\s+/', '', $input);

            if (filter_var($string, FILTER_VALIDATE_URL)) {
                $this->url_data['assets_url'] = $string;
            } else {
                echo "Please enter a valid URL! \n";
            }
        } while (empty($this->url_data['assets_url']));

        // Check
        $i = 0;
        $finish = false;
        $checked = false;

        do {
            $i++;
            echo "\n";
            echo "Please check the entries \n";
            echo "--- \n";
            echo "Site URL: {$this->url_data['site_url']}\n";
            echo "API URL: {$this->url_data['api_url']}\n";
            echo "Admin URL: {$this->url_data['admin_url']}\n";
            echo "Assets URL: {$this->url_data['assets_url']}\n";
            echo "\n";

            $input = BaseConsole::input("Are the top entries correct? (yes|no): ");
            $input = strtolower($input);

            if ($input == 'yes') {
                $checked = true;
                $finish = true;
            } elseif ($input == 'no') {
                $checked = false;
                $finish = true;
            }
        } while (!$finish);

        if (!$checked) {
            echo "\n";
            echo "Ok, let's try again :) \n";
            $this->reset('url_data');
            $this->setURL();
        }
    }

    private function setDatabse()
    {
        $i = 0;
        $supported = array('mysql', 'mongodb', 'pgsql');
        $supported_str = implode(' | ', $supported);

        do {
            $i++;
            $input = BaseConsole::input("Database type (Eg: {$supported_str}) ");
            $string = preg_replace('/\s+/', '', $input);

            if (in_array($string, $supported)) {
                $this->db_data['type'] = $string;
            } else {
                echo "Please enter a valid database type! \n";
            }
        } while (empty($this->db_data['type']));

        // Host
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Host (Eg: localhost) ");
            $string = preg_replace('/\s+/', '', $input);

            if (!empty($string)) {
                $this->db_data['host'] = $string;
            }
        } while (empty($this->db_data['host']));

        // Port
        $i = 0;
        $port = 3306;

        if ($this->db_data['type'] == 'pgsql') {
            $port = 5432;
        } elseif ($this->db_data['type'] == 'mongodb') {
            $port = 27017;
        }

        do {
            $i++;
            $input = BaseConsole::input("Port (Eg: {$port} | default) ");
            $string = preg_replace('/\s+/', '', $input);

            if (strtolower($string) == 'default') {
                $this->db_data['port'] = $port;
            } elseif (is_numeric($string)) {
                $this->db_data['port'] = $string;
            } else {
                echo "Please enter a valid database port! \n";
            }
        } while (empty($this->db_data['port']));

        // Database
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Database name (Eg: myapp_db) ");
            $string = preg_replace('/\s+/', '', $input);

            if (!empty($string)) {
                $this->db_data['db_name'] = $string;
            }
        } while (empty($this->db_data['db_name']));

        // Username
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Username (Eg: root) ");
            $string = preg_replace('/\s+/', '', $input);

            if (!empty($string)) {
                $this->db_data['username'] = $string;
            }
        } while (empty($this->db_data['username']));

        // Password
        $i = 0;

        do {
            $i++;
            $input = BaseConsole::input("Password (Eg: mypass) ");
            $string = preg_replace('/\s+/', '', $input);
            $this->db_data['password'] = $string;
        } while ($i < 1);

        // Check
        $i = 0;
        $finish = false;
        $checked = false;

        do {
            $i++;
            echo "\n";
            echo "Please check the entries: \n";
            echo "--- \n";
            echo "Database type: {$this->db_data['type']}\n";
            echo "Host: {$this->db_data['host']}\n";
            echo "Port: {$this->db_data['port']}\n";
            echo "Database name: {$this->db_data['db_name']}\n";
            echo "Username: {$this->db_data['username']}\n";
            echo "Password: {$this->db_data['password']}\n";
            echo "\n";

            $input = BaseConsole::input("Are the top entries correct? (yes|no): ");
            $input = strtolower($input);

            if ($input == 'yes') {
                $checked = true;
                $finish = true;
            } elseif ($input == 'no') {
                $checked = false;
                $finish = true;
            }
        } while (!$finish);

        if (!$checked) {
            echo "\n";
            echo "Okay, let's try again :) \n";
            $this->reset('db_data');
            $this->setDatabse();
        }
    }

    private function checkingData()
    {
        $prepare = true;

        echo "\n";
        echo "Data validation... \n";

        if (is_array($this->url_data) && $this->url_data) {
            $site_url = $this->url_data['site_url'];
            $domain_name = trim($site_url);
            $domain_name = str_replace('http://', '', $domain_name);
            $domain_name = str_replace('https://', '', $domain_name);
            $domain_name = str_replace('www.', '', $domain_name);
            $this->url_data['domain_name'] = trim($domain_name);

            foreach ($this->url_data as $key => &$url) {
                $url = trim($url, '/');

                if ($key != 'domain_name') {
                    $url = $url . '/';
                }
            }
        } else {
            $prepare = false;
            echo "\n";
            echo "Oops, an error occurred! \n";
            echo "Invalid URL data, please try again :( \n";
        }

        if (is_array($this->db_data) && $this->db_data) {
            $this->configDatabase();
        } else {
            $prepare = false;
            echo "\n";
            echo "Oops, an error occurred! \n";
            echo "Invalid database data, please try again :( \n";
        }

        if ($prepare) {
            $this->prepareData();
        }
    }

    private function prepareData()
    {
        echo "\n";
        echo "Preparing data for creation... \n";

        $write = true;

        $config_inc = dirname(__DIR__) . '/setup/config.inc.php';
        $config_theme = dirname(__DIR__) . '/setup/config.theme.php';

        if (!is_file($config_inc)) {
            $write = false;
            echo "\n";
            echo "Oops, an error occurred! \n";
            echo "The installation config file was not found. Unable to create file :( \n";
        } elseif (!is_file($config_theme)) {
            $write = false;
            echo "\n";
            echo "Oops, an error occurred! \n";
            echo "The installation theme file was not found. Unable to create file :( \n";
        }

        if ($write) {
            $this->setup_app = true;
            $this->configIncFile();
            $this->configThemeFile();
        }
    }

    private function configIncFile()
    {
        $file_name = 'config.inc.php';
        $file = dirname(__DIR__) . '/setup/config.inc.php';

        if ($this->overwrite && !$this->overwrite_inc) {
            echo "\n";
            echo "Skip updating '{$file_name}' file... \n";
            return;
        }

        echo "\n";
        echo "Creating '{$file_name}' file... \n";

        $array = include $file;
        $path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        $filename = $path . $file_name;

        if ($this->url_data) {
            $array = array_merge($array, $this->url_data);
        }

        if ($this->db_data) {
            $array['database'] = $this->db_data;
        }

        $app_id = _random_string('alnum', 20) . '-' . strtotime('now');
        $array['app_id'] = strtoupper(_random_string('alpha', 5)) . '-' . md5($app_id);
        $array['redis']['secret_key'] = _random_string('alnum', 50);
        $array['redis']['secret_iv'] = _random_string('alnum', 50);
        $array['site_master_pass'] = rand(100000, 999999);

        $str = '<?php return ' . $this->var_export($array) . ';';
        $str = str_replace("'{PDO_ATTR}'", '\yii\db\mssql\PDO::MYSQL_ATTR_INIT_COMMAND', $str);

        file_put_contents($filename, $str);
        echo "\n";
        echo "File '{$file_name}' created successfully! \n";

        return true;
    }

    private function configThemeFile()
    {
        $file_name = 'config.theme.php';
        $setup_file = dirname(__DIR__) . "/setup/{$file_name}";
        $config_file = dirname(dirname(__DIR__)) . "/{$file_name}";
        $success_message = "File '{$file_name}' created successfully! \n";

        if (is_file($config_file)) {
            $_config = include $config_file;

            if (is_array($_config) && $_config) {
                return;
            }

            echo "\n";
            echo "Updating '{$file_name}' file... \n";
            $success_message = "File '{$file_name}' updated successfully! \n";
        } else {
            echo "\n";
            echo "Creating '{$file_name}' file... \n";
        }

        $array = include $setup_file;
        $path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        $filename = $path . $file_name;

        $str = '<?php return ' . $this->var_export($array) . ';';
        $str = str_replace("'{PDO_ATTR}'", '\yii\db\mssql\PDO::MYSQL_ATTR_INIT_COMMAND', $str);

        file_put_contents($filename, $str);
        echo "\n";
        echo $success_message;

        return true;
    }

    private function configDatabase()
    {
        $db_data = $this->db_data;
        $db_type = $db_data['type'];

        if ($db_type == 'mysql') {
            if (is_numeric($db_data['port']) && $db_data['port'] != 3306) {
                $dsn = "mysql:host={$db_data['host']};port={$db_data['port']};dbname={$db_data['db_name']}";
            } else {
                $dsn = "mysql:host={$db_data['host']};dbname={$db_data['db_name']}";
            }

            $this->db_data = array(
                'db' => array(
                    'class' => "yii\db\Connection",
                    'dsn' => $dsn,
                    'username' => $db_data['username'],
                    'password' => $db_data['password'],
                    'charset' => 'utf8mb4',
                    'enableSchemaCache' => true,
                    'attributes' => [
                        '{PDO_ATTR}' => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));",
                    ],
                )
            );
        } elseif ($db_type == 'pgsql') {
            $this->db_data = array(
                'db' => array(
                    'class' => "yii\db\Connection",
                    'dsn' => "pgsql:host={$db_data['host']};port={$db_data['port']};dbname={$db_data['db_name']}",
                    'username' => $db_data['username'],
                    'password' => $db_data['password'],
                    'charset' => 'utf8mb4',
                )
            );
        } elseif ($db_type == 'mongodb') {
            $this->db_data = array(
                'mongodb' => [
                    'class' => "\yii\mongodb\Connection",
                    'dsn' => "mongodb://{$db_data['username']}:{$db_data['password']}@{$db_data['host']}:{$db_data['port']}/{$db_data['db_name']}",
                ],
            );
        }
    }

    private function reset($type)
    {
        if ($type == 'url_data') {
            $this->url_data = array(
                'domain_name' => '',
                'site_url' => '',
                'api_url' => '',
                'admin_url' => '',
                'assets_url' => '',
            );
        } else {
            $this->db_data = array(
                'type' => '',
                'host' => '',
                'port' => '',
                'db_name' => '',
                'username' => '',
                'password' => '',
            );
        }
    }

    private function consoleConfig()
    {
        $setup_main_local = dirname(__DIR__) . '/setup/main-local.php';

        if (is_file($setup_main_local)) {
            $common_main_local = \Yii::getAlias('@common') . '/config/main-local.php';

            if (!copy($setup_main_local, $common_main_local)) {
                echo "\n";
                echo "Failed to copy main-local.php to 'common/config' folder. \n";
                echo "\n";
            } else {
                echo "\n";
                echo "File '@common/config/main-local.php' updated successfully! \n";
                echo "\n";
            }
        }
    }

    private function clearTemp()
    {
        delete_files_in_dir(TEMP_PATH);
        echo "Deleted temp and cache files and folders. \n";
        echo "\n";
    }

    private function var_export($expression, $return = TRUE)
    {
        $export = var_export($expression, true);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));
        if ((bool)$return) return $export;
        else echo $export;
    }
}
