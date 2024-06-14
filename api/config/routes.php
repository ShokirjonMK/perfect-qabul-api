<?php

$controllers = [
    'exam-subject',
    'site',
    'university',
    'exam-student',
    'exam',
    'option',
    'question',
    'direction',
    'direction-subject',
    'subject',
    'edu-year',
    'edu-form',
    'student',
    'nationality',
    'languages',
    'password',
    'region',
    'area',
    'country',
    'auth',
    'user',


    'user-access-type',
    'user-access',

    'subject-content',
    'citizenship',
];

$controllerRoutes = [];

foreach ($controllers as $controller) {
    $rule = [
        'class' => 'yii\rest\UrlRule',
        'controller' => $controller,
        'prefix' => '<lang:\w{2}>'
    ];
    if ($controller == 'basis-of-learning') {
        $rule['pluralize'] = false;
    }
    $controllerRoutes[] = $rule;
}

$routes = [

    // Login and get access_token from server
    'POST <lang:\w{2}>/auth/login' => 'auth/login',
    'POST <lang:\w{2}>/auth/logout' => 'user/logout',
    // User Self update data
    'PUT <lang:\w{2}>/users/self' => 'user/self',
    // User Get Self data
    'GET <lang:\w{2}>/users/self' => 'user/selfget',

    // Get me
    'GET <lang:\w{2}>/users/me' => 'user/me',

    // Auth
    'GET <lang:\w{2}>/index' => 'auth/index',
    'POST <lang:\w{2}>/login' => 'auth/login',
    'POST <lang:\w{2}>/is-phones' => 'auth/is-phone',
    'POST <lang:\w{2}>/sms-number' => 'auth/sms-number',
    'POST <lang:\w{2}>/password' => 'auth/password',


    'POST <lang:\w{2}>/new-password' => 'auth/new-password',
    'POST <lang:\w{2}>/sms-confirm' => 'auth/sms-confirm',
    'POST <lang:\w{2}>/reset-password' => 'auth/pass-reset',


    /** telegram */
    'GET <lang:\w{2}>/telegrams/bot' => 'telegram/bot',
    /** Oferta */
    'POST <lang:\w{2}>/users/oferta/' => 'user/oferta',

    // Student Get me
    'GET <lang:\w{2}>/students/me' => 'student/me',

    /** MIP pinfl */
    'GET <lang:\w{2}>/users/get/' => 'user/get',

    // Roles and permissions endpoint
    'GET <lang:\w{2}>/roles' => 'access-control/roles', // Get roles list
    'GET <lang:\w{2}>/roles/<role>/permissions' => 'access-control/role-permissions', // Get role permissions
    'POST <lang:\w{2}>/roles' => 'access-control/create-role', // Create new role
    'POST <lang:\w{2}>/create-permission' => 'access-control/create-permission', // Create new permission
    'PUT <lang:\w{2}>/roles' => 'access-control/update-role', // Update role
    'DELETE <lang:\w{2}>/roles/<role>' => 'access-control/delete-role', // Delete role
    'GET <lang:\w{2}>/permissions' => 'access-control/permissions', // Get permissions list


    // Questions
    'PUT <lang:\w{2}>/questions/is-check/<id>' => 'question/is-check',
    'POST <lang:\w{2}>/questions/add' => 'question/add',

    // Test belgilash
    'PUT <lang:\w{2}>/exam-students/question/<id>' => 'exam-student/question',
    'PUT <lang:\w{2}>/exam-students/finish/<id>' => 'exam-student/finish',
    'GET <lang:\w{2}>/exam-students/get/<id>' => 'exam-student/get',
    'POST <lang:\w{2}>/exam-students/increment' => 'exam-student/increment',
    'GET <lang:\w{2}>/exam-students/pdf' => 'exam-student/pdf',

    // Student
    'GET <lang:\w{2}>/edu-type' => 'student/edu-type',
    'GET <lang:\w{2}>/sertificate-type' => 'student/sertificate-type',
    'GET <lang:\w{2}>/exam-type' => 'student/exam-type',

];

return array_merge($controllerRoutes, $routes);
