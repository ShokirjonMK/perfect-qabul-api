<?php

$controllers = [
    'get-teacher',
    'student',
    'employee',
    'user',
    'department',
    'job',
    'subject',
    'subject-topic',
    'nationality',
    'languages',
    'residence-type',
    'science-degree',
    'scientific-title',
    'special-title',
    'basis-of-learning',
    'building',
    'course',
    'room',
    'course',
    'direction',
    'faculty',
    'kafedra',
    'para',
    'semestr',
    'edu-year',
    'subject',
    'subject-type',
    'subject-category',
    'exams-type',
    'edu-type',
    'edu-form',
    'edu-plan',
    'edu-semestr',
    'edu-semestr-exams-type',
    'edu-semestr-subject',
    'edu-semestr-subject-category-time',
    'teacher-access',
    'password',
    'translate',
    'week',
    'region',
    'area',
    'student-exam',
    'country',

    'time-table',
    'time-option',
    'student-time-table',
    'student-time-option',

    'exam',
    'exam-student',
    'exam-question',
    'exam-question-type',
    'exam-question-option',
    'exam-student-answer',
    'exam-teacher-check',

    'subject-sillabus',

    'question',
    'question-type',
    'question-option',
    'exam-semeta',

    'user-access-type',
    'user-access',

    'subject-access',

    'subject-topic',
    'subject-content',
    'citizenship',
    'notification',
    'notification-role',
    'nationality',
    'category-of-cohabitant',
    'residence-status',
    'social-category',
    'student-category',



    'teacher-checking-type',
    'statistic',

    'exam-checking',
    'exam-appeal',

    'survey-question',
    'survey-answer',
    'election',
    'election-candidate',
    'election-vote',
    'kpi-category',
    'kpi-store',
    'kpi-data',


    'partiya',
    'diploma-type',
    'degree',
    'degree-info',
    'academic-degree',

    'vocation',
    'holiday',
    'job-title',
    'work-rate',

    'table-store',
    'instruction',
    'exam-appeal-semeta',

    'student-order',
    'order-type',
    'relative-info',
    'other-certificate',
    'other-certificate-type',
    'olympic-certificate',
    'sport-certificate',
    'lang-certificate',
    'lang-certificate-type',
    'military',
    'cantract',

    'subject-content-mark',
    'kpi-mark',
    'subject-topic-reference',
    'hostel-category',
    'hostel-category-type',
    'hostel-app',
    'hostel-doc',

    'teacher-content',
    'student-subject-selection',

    'club-category',
    'club',
    'club-time',
    'student-club',

    'attend',
    'attend-reason',
    'student-attend',

    'exam-control',
    'exam-control-student',

    'telegram',
    'test-get-data',
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
    /** telegram */
    'GET <lang:\w{2}>/telegrams/bot' => 'telegram/bot',


    /** MIP pinfl */
    'GET <lang:\w{2}>/users/get/' => 'user/get',
    /** Oferta */
    'POST <lang:\w{2}>/users/oferta/' => 'user/oferta',


    /** Student from Hemis via pinfl */
    'GET <lang:\w{2}>/students/get/' => 'student/get',
    /** Student For turniket */
    'GET <lang:\w{2}>/students/by-pinfl/<pinfl>' => 'student/by-pinfl',
    'GET <lang:\w{2}>/students/time-option-not/' => 'student/time-option-not',

    /** Hostel Yotoqxona */
    'GET <lang:\w{2}>/hostel-docs/check/<id>/' => 'hostel-doc/check',
    'GET <lang:\w{2}>/hostel-docs/not/<id>/' => 'hostel-doc/not',

    /** attend-reason  */
    'GET <lang:\w{2}>/attend-reasons/confirm/<id>/' => 'attend-reason/confirm',

    /** Code Correctors */
    'GET <lang:\w{2}>/exam-students/correct/<key>/' => 'exam-student/correct',
    'POST <lang:\w{2}>/subject-contents/order' => 'subject-content/order',

    /** Code Correctors */

    /* statistics all */
    // statistic student-count-by-faculty
    'GET <lang:\w{2}>/statistics/student-count-by-faculty' => 'statistic/student-count-by-faculty',
    'GET <lang:\w{2}>/statistics/kpi-content-store' => 'statistic/kpi-content-store',

    // statistic Kafedra Questions Teachers
    'GET <lang:\w{2}>/statistics/kafedra' => 'statistic/kafedra',
    'GET <lang:\w{2}>/statistics/checking' => 'statistic/checking',
    'GET <lang:\w{2}>/statistics/checking-chala' => 'statistic/checking-chala',
    'GET <lang:\w{2}>/statistics/exam-checking' => 'statistic/exam-checking',

    /* statistics all */

    // election password generator
    'GET <lang:\w{2}>/elections/<id>/password' => 'election/password',

    // Question status update
    'PUT <lang:\w{2}>/questions/status-update/<id>' => 'question/status-update',
    // Question status list
    'GET <lang:\w{2}>/questions/status-list' => 'question/status-list',

    // KpiCategory Extra fields, term, tab, status
    'GET <lang:\w{2}>/kpi-categories/extra' => 'kpi-category/extra',



    // Login and get access_token from server
    'POST <lang:\w{2}>/auth/login' => 'auth/login',
    // User Self update data
    'PUT <lang:\w{2}>/users/self' => 'user/self',
    // User Get Self data
    'GET <lang:\w{2}>/users/self' => 'user/selfget',
    // Get me
    'GET <lang:\w{2}>/users/me' => 'user/me',
    // Log out
    'POST <lang:\w{2}>/auth/logout' => 'user/logout',

    // TimeTable parent null
    'GET <lang:\w{2}>/time-tables/parent-null' => 'time-table/parent-null',

    // Exam Passwords
    'POST <lang:\w{2}>/exams/get-passwords' => 'exam/get-passwords',
    // Exam Passwords
    'POST <lang:\w{2}>/exams/generate-passwords' => 'exam/generate-passwords',
    // exam Distribution
    'GET <lang:\w{2}>/exams/<id>/distribution' => 'exam/distribution',
    // exam Appeal Distribution
    'GET <lang:\w{2}>/exams/<id>/appeal-distribution' => 'exam/appeal-distribution',
    // exam announced // natijani e'lon qilish
    'GET <lang:\w{2}>/exams/<id>/ad' => 'exam/ad',

    // Department type list
    'GET <lang:\w{2}>/departments/types' => 'department/types',

    // studentga savollarni random tushirish
    'POST <lang:\w{2}>/exam-student-answers/get-question' => 'exam-student-answer/get-question',
    // ExamStudentAnswer Appeal checking
    'PUT <lang:\w{2}>/exam-checkings/<id>/appeal' => 'exam-checking/appeal',

    // teacherga studentlarni random tushirish
    'POST <lang:\w{2}>/exam-teacher-check/random-students' => 'exam-teacher-check/random-students',

    // Subject Content Trash ( get Deleted Content)
    'GET <lang:\w{2}>/subject-contents/trash' => 'subject-content/trash',
    // Subject Content Delete from Trash ( get Deleted Content)  bazadan o'chirish
    'DELETE <lang:\w{2}>/subject-contents/trash/<id>' => 'subject-content/trash-delete',
    // Subject Content type list
    'GET <lang:\w{2}>/subject-contents/types' => 'subject-content/types',

    // Faculty UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/faculties/user-access' => 'faculty/user-access',
    // Kafedra UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/kafedras/user-access' => 'kafedra/user-access',
    // Department UserAccess fakultitetga user biriktirish
    'POST <lang:\w{2}>/departments/user-access' => 'department/user-access',


    /** Free teachers for time tables */
    'GET <lang:\w{2}>/teacher-accesses/free' => 'teacher-access/free',
    'POST <lang:\w{2}>/rooms/free' => 'room/free',
    /**  */


    // Student Get me
    'GET <lang:\w{2}>/students/me' => 'student/me',

    // Student Import
    'POST <lang:\w{2}>/students/import' => 'student/import',
    // Student Export
    'GET <lang:\w{2}>/students/export' => 'student/export',
    // 'POST <lang:\w{2}>/students/read' => 'student/read',

    // My Notifications
    'GET <lang:\w{2}>/notifications/my' => 'notification/my',
    // Notifications Status list
    'GET <lang:\w{2}>/notifications/status-list' => 'notification/status-list',
    // Notifications Approved (tasdiqlavoring)
    'PUT <lang:\w{2}>/notifications/approved/<id>' => 'notification/approved',

    // Roles and permissions endpoint
    'GET <lang:\w{2}>/roles' => 'access-control/roles', // Get roles list
    'GET <lang:\w{2}>/roles/<role>/permissions' => 'access-control/role-permissions', // Get role permissions
    'POST <lang:\w{2}>/roles' => 'access-control/create-role', // Create new role
    'PUT <lang:\w{2}>/roles' => 'access-control/update-role', // Update role
    'DELETE <lang:\w{2}>/roles/<role>' => 'access-control/delete-role', // Delete role
    'GET <lang:\w{2}>/permissions' => 'access-control/permissions', // Get permissions list
    // ***

    'GET <lang:\w{2}>/user-statuses' => 'user/status-list', // Get user statuses

    /* Enums */
    'GET <lang:\w{2}>/genders' => 'enum/genders',
    'GET <lang:\w{2}>/educations' => 'enum/educations',
    'GET <lang:\w{2}>/education-degrees' => 'enum/education-degrees',
    'GET <lang:\w{2}>/disability-groups' => 'enum/disability-groups',
    'GET <lang:\w{2}>/education-types' => 'enum/education-types',
    'GET <lang:\w{2}>/family-statuses' => 'enum/family-statuses',
    'GET <lang:\w{2}>/rates' => 'enum/rates',
    'GET <lang:\w{2}>/topic-types' => 'enum/topic-types',
    'GET <lang:\w{2}>/yesno' => 'enum/yesno',
    /* Enums */
];

return array_merge($controllerRoutes, $routes);
