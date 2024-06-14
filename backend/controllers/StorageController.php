<?php

namespace backend\controllers;

use base\BackendController;
use common\models\StorageBrowser;

/**
 * Stprage controller
 */
class StorageController extends BackendController
{
    private $path;
    private $path_name;
    private $path_url;
    private $view_type;
    private $configuration;

    /**
     * Init controller
     */
    public function init()
    {
        parent::init();

        $this->path = UPLOADS_PATH;
        $this->path_name = _e('Home');
        $this->path_url = assets_url('uploads/');
        $this->configuration = array('depth' => '== 0');
    }

    /**
     * Open uploads path
     *
     * @return void
     */
    public function actionFiles()
    {
        return $this->render('page', [
            'path_name' => $this->path_name,
            'path' => '/files/',
        ]);
    }

    /**
     * Open uploads path
     *
     * @return void
     */
    public function actionImages()
    {
        return $this->render('page', [
            'path_name' => $this->path_name,
            'path' => '/images/',
        ]);
    }

    /**
     * Open uploads path
     *
     * @return void
     */
    public function actionUploads()
    {
        return $this->render('page', [
            'path_name' => $this->path_name,
            'path' => '/',
        ]);
    }

    /**
     * View
     *
     * @return void
     */
    public function actionView()
    {
        return $this->renderPartial('browser', [
            'path_name' => $this->path_name,
        ]);
    }

    /**
     * Action get
     *
     * @return void
     */
    public function actionGet()
    {
        $path = $this->path;
        $path_url = $this->path_url;

        // Check path
        $path_name = input_get('path', '/');

        if ($path_name && $path_name != '/') {
            $path_name = str_replace('\\', '/', trim($path_name));
            $path_name = trim($path_name, '/');
            $path_name = '/' . $path_name;

            $path = $path . trim($path_name, '/');
            $path = StorageBrowser::path_name_convert($path) . DIRECTORY_SEPARATOR;
            $path_url = trim($path_url, '/') . $path_name;
        }

        // Init finder
        $finder = new StorageBrowser();
        $finder->config($this->configuration);

        $files = $finder->getFiles($path, $path_url);
        $folders = $finder->getFolders($path, $path_url);

        $folders_list_view = $this->renderPartial('folders-list', ['folders' => $folders, 'path_name' => $path_name]);
        $folders_grid_view = $this->renderPartial('folders-grid', ['folders' => $folders, 'path_name' => $path_name]);
        $files_list_view = $this->renderPartial('files-list', ['files' => $files, 'path_name' => $path_name]);
        $files_grid_view = $this->renderPartial('files-grid', ['files' => $files, 'path_name' => $path_name]);
        $files_info = $this->renderPartial('files-info', ['files' => $files, 'folders' => $folders, 'path_name' => $path_name]);

        $output['error'] = '';
        $output['path'] = $path_name;
        $output['path_list'] = StorageBrowser::pathBreadcrumb($path_name, $this->path_name);
        $output['files_count'] = count($files);
        $output['files_list'] = trim($files_list_view);
        $output['files_grid'] = trim($files_grid_view);
        $output['folders_list'] = trim($folders_list_view);
        $output['folders_grid'] = trim($folders_grid_view);
        $output['infos'] = trim($files_info);

        return $this->asJson($output);
    }

    /**
     * Actions
     *
     * @return void
     */
    public function actionActions()
    {
        $message = '';
        $action_success = false;

        $path = $this->path;
        $path_url = $this->path_url;

        // Check path
        $path_name = input_get('path', '/');

        if ($path_name && $path_name != '/') {
            $path_name = str_replace('\\', '/', trim($path_name));
            $path_name = trim($path_name, '/');
            $path_name = '/' . $path_name;

            $path = $path . trim($path_name, '/');
            $path = StorageBrowser::path_name_convert($path) . DIRECTORY_SEPARATOR;
            $path_url = trim($path_url, '/') . '/' . $path_name;
        }

        // Check ajax action type
        $action_type = input_post('action_type');

        if ($action_type == 'update_file') {
            $file_name = input_post('file_name');
            $file_permissons = input_post('file_permissons');

            if ($file_name) {
                $action_success = true;
                $message = _e('File updated successfully.');
                $file = $path . $file_name;

                if (is_file($file)) {
                    $name = input_post('name');
                    $permissions = input_post('permissions');

                    if (is_numeric($permissions) && is_numeric($file_permissons) && $permissions != $file_permissons) {
                        $action_success = false;
                        $message = _e('Unable to chmod file. Please check the permissions.');

                        if (chmod($file, $permissions)) {
                            $action_success = true;
                            $message = 'File updated successfully!';
                        }
                    }

                    if ($name && $file_name != $name) {
                        $old_name = $file;
                        $new_name = $path . $name;

                        if (is_dir($new_name)) {
                            $action_success = false;
                            $message = _e('File name already exists.');
                        } elseif (is_file($new_name)) {
                            $action_success = false;
                            $message = _e('The file cannot be renamed. A file with the same name already exists.');
                        } elseif (rename($old_name, $new_name)) {
                            $action_success = true;
                            $message = _e('File renamed successfully.');
                        } else {
                            $action_success = false;
                            $message = _e('Unable to rename file. Please check the permissions.');
                        }
                    }
                }
            }
        } elseif ($action_type == 'update_folder') {
            $folder_name = input_post('folder_name');
            $folder_permissons = input_post('folder_permissons');

            if ($folder_name) {
                $action_success = true;
                $message = _e('Folder updated successfully.');
                $folder = $path . $folder_name;

                if (is_dir($folder)) {
                    $name = input_post('name');
                    $permissions = input_post('permissions');

                    if (is_numeric($permissions) && is_numeric($folder_permissons) && $permissions != $folder_permissons) {
                        $action_success = false;
                        $message = _e('Unable to chmod folder. Please check the permissions.');

                        if (chmod($folder, $permissions)) {
                            $action_success = true;
                            $message = _e('Folder updated successfully.');
                        }
                    }

                    if ($name && $folder_name != $name) {
                        $old_name = $folder;
                        $new_name = $path . $name;

                        if (is_dir($new_name)) {
                            $action_success = false;
                            $message = _e('Unable to rename folder. A folder with the same name already exists.');
                        } elseif (rename($old_name, $new_name)) {
                            $action_success = true;
                            $message = _e('Folder renamed successfully.');
                        } else {
                            $action_success = false;
                            $message = _e('Unable to rename folder. Please check the permissions.');
                        }
                    }
                }
            }
        } elseif ($action_type == 'create_folder') {
            $folder_name = input_post('folder_name');

            if ($folder_name && is_dir($path)) {
                $folder = $path . $folder_name;
                $folder_name_clear = create_slug($folder_name);

                if (is_dir($folder)) {
                    $message = _e('Folder name already exists.');
                } elseif (mkdir($path . $folder_name_clear)) {
                    $action_success = true;
                    $message = _e('Folder created successfully.');
                } else {
                    $message = _e('Unable to create folder. Please check the permissions.');
                }
            } else {
                $message = _e('Unable to create folder. Please check folder name or folder permissions.');
            }
        } elseif ($action_type == 'delete') {
            $names = input_post('names');

            if (is_array($names) && $names) {
                $action_success = true;
                $message = _e('Items deleted successfully.');

                foreach ($names as $name) {
                    $item = $path . $name;
                    $output['wow'] = $item;
                    StorageBrowser::delete($item);
                }
            } else {
                $message = _e('Unable to delete items. Please check the permissions.');
            }
        } elseif ($action_type == 'upload_file') {
            $dir = $path;

            if (is_dir($dir)) {
                $files = $_FILES;
                $allowed_types = StorageBrowser::allowed_file_types('array');

                if ($files) {
                    $upfile = 0;

                    foreach ($files as $key => $file) {
                        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $dot_ext = '.' . $fileExt;
                        $dot_exl = mb_strtolower($dot_ext, 'UTF-8');
                        $fileExtCs = '*.' . $fileExt;
                        $fileExtCs = mb_strtolower($fileExtCs, 'UTF-8');

                        if (in_array($fileExtCs, $allowed_types)) {
                            $file_name = str_replace($dot_ext, '', $file['name']);
                            $file_name = create_slug($file_name) . $dot_exl;
                            $path_file = $dir . $file_name;

                            if (is_file($path_file)) {
                                $i = 0;
                                $new_filename = str_replace($dot_exl, '-1' . $dot_exl, $file_name);
                                $path_file = $dir . $new_filename;

                                do {
                                    $i++;
                                    $new_filename = str_replace($dot_exl, '-' . $i . $dot_exl, $file_name);
                                    $path_file = $dir . $new_filename;
                                } while (is_file($path_file) && $i <= 1000);
                            }

                            $image_data = getimagesize($file['tmp_name']);

                            if (isset($image_data[0])) {
                                $image_width = $image_data[0];

                                if (is_numeric($image_width) && $image_width > 2000) {
                                    $image_content = file_get_contents($file['tmp_name']);

                                    $image = \base\libs\ImageResize::createFromString($image_content);
                                    $image->quality_jpg = 100;
                                    $image->resizeToWidth(2000);
                                    $image->save($file['tmp_name'], null, 100);
                                }
                            }

                            if ($path_file && move_uploaded_file($file['tmp_name'], $path_file)) {
                                $upfile++;
                            }
                        }
                    }

                    if ($upfile > 0) {
                        $action_success = true;

                        if ($upfile == 1) {
                            $message = _e('File uploaded successfully.');
                            $output['toastr'] = _e('File uploaded successfully.');
                        } else {
                            $message = _e('{count} files uploaded successfully.', ['count' => $upfile]);
                            $output['toastr'] = _e('Files uploaded successfully.');
                        }
                    } else {
                        $output['toastr'] = _e('Error on uploading files.');
                        $message = _e('Error on uploading files. Please check folder permissions.<br>Allowed file types: {allowed_types}', ['allowed_types' => implode(', ', $allowed_types)]);
                    }
                } else {
                    $message = _e('Please select files to upload.');
                }
            } else {
                $message = _e('Unable to upload files. Folder does not exists.');
            }
        }

        if ($action_success) {
            $output['error'] = false;
            $output['success'] = true;
            $output['message'] = $message;
        } else {
            $output['error'] = true;
            $output['success'] = false;
            $output['message'] = $message;
        }

        return $this->asJson($output);
    }
}
