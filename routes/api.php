<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\baseController;
use App\Http\Controllers\admin\circleController;
use App\Http\Controllers\admin\EmployeeVoteController;
use App\Http\Controllers\admin\personController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthController::class, 'login']);
// route::get('admin/employee/indexx', [EmployeeVoteController::class, 'indexx']); //هەموو کارمەندا
// route::get('admin/listNoteVoteTest', [EmployeeVoteController::class, 'listNoteVoteTest']); // ئەوانەی دەنگیان نەداوە

Route::middleware('auth:sanctum')->group(
    function () {

        // Auth routes
        Route::controller(AuthController::class)->group(
            function () {

                Route::post('/admin/register', 'register');
                Route::get('/admin/getListUser', 'getUserList');
                Route::get('/admin/getUser/{id}', 'getUser');
                Route::post('/admin/updateUser/{id}', 'userUpdate');
                Route::post('/admin/changePassword/{id}', 'changePassword');

                Route::get('/login-logs', 'index');


                Route::post('/logout', 'logout');
            }
        );

        // Employee Vote routes
        Route::controller(EmployeeVoteController::class)->group(function () {

            //admin
            route::middleware(['role:admin,superadmin'])->group(function () {

                route::post('admin/employee/add', 'store');
                route::get('admin/employee/show/{id}', 'show');
                route::post('admin/employee/update/{id}', 'update');
                route::get('admin/listNoteVote', 'listNoteVote'); // ئەوانەی دەنگیان نەداوە
                route::get('admin/listVoteByBase', 'listVoteByBaseId'); //   ئەوانەی دەنگیان نەداوە بەپێی بنکە
                route::get('admin/listVoteByCircleId', 'listVoteByCircleId'); //   ئەوانەی دەنگیان نەداوە بەپێی بازنە
                route::get('admin/voteStats', 'voteAllStats');
                route::get('getAllVoteStats', 'listVote'); // ئەوانەی دەنگیان داوە
                route::get('getAllNotVote', 'getAllNotVote');
                // amar
                route::get('admin/amar', 'amar');
                route::get('admin/getAllVoteStats', 'getAllVoteStats'); //ئاماری داشبۆردی ئەدمین


                // get doublicated
                // Route::get('admin/getdoublicate', 'doublicateData');

                // Individual card number existence check - Multiple variations
                Route::get('/admin/check-card-number-exists/{card_number}', [EmployeeVoteController::class, 'checkCardNumberExists']);
                Route::get('/admin/check-card-number-exists', [EmployeeVoteController::class, 'checkCardNumberExists']); // Query parameter version

                // Bulk card number existence check - POST and GET versions
                Route::post('/admin/check-card-numbers-exist', [EmployeeVoteController::class, 'checkCardNumbersExistBulk']);
                Route::get('/admin/check-card-numbers-exist', [EmployeeVoteController::class, 'checkCardNumbersExistBulk']); // GET version

                // Card number details and summary
                Route::get('/admin/card-numbers-summary', [EmployeeVoteController::class, 'getCardNumbersSummary']);
                Route::get('/admin/card-number-details/{card_number}', [EmployeeVoteController::class, 'getCardNumberDetails']);
                Route::get('/admin/card-number-details', [EmployeeVoteController::class, 'getCardNumberDetails']); // Query parameter version

                // Legacy endpoint redirect (if you want to keep backward compatibility)
                Route::get('/admin/getdoublicate/{card_number}', [EmployeeVoteController::class, 'checkCardNumberExists']);
                Route::get('/admin/getdoublicate', [EmployeeVoteController::class, 'checkCardNumberExists']);

                // importData
                route::post('/admin/import', 'importData');
            });



            // admin and user
            route::get('admin/voteStat', 'voteAllStats'); //  بۆ ئەمین هەموو زانینی کۆی ژمارەی کارمەنداکان ، هاتوو ، نەهاتوو ، بۆ چاودێر تەنها بنکەی خۆی

            route::get('findEmployee/{search}', 'findEmployee');
            route::get('findEmployeeByMobileName/{search}', 'findEmployeeByNameAndMobile');


            // amar hatw nahatw
            // route::get('/admin/allnahatw', 'all_nahatw');
            // route::get('/admin/allhatw', 'all_hatw');


            route::get('admin/employee/index', 'index'); //هەموو کارمەندا
            //cahwder
            route::get('employee/hatw', 'allhatw'); //هەموو هاتوەکان
            route::get('employee/nahatw', 'allNahatw'); //هەموو نەواتوەکان

            Route::get('/employee/search', 'search');
            // route::get('findEmployeeByMobile/{mobile}', 'findEmployeeByMobile');
            route::post('vote/{id}', 'vote');
            route::post('AddNoteForEmployee/{id}', 'AddNoteForEmployee');
        });

        // circle routes
        Route::controller(circleController::class)->group(function () {
            route::get('circle/index', 'getAllcircle');
            route::get('circle/{id}', 'circelFindById');
            route::post('circle/add', 'add');
            route::post('circle/update/{id}', 'update');
            route::post('circle/delete/{id}', 'delete');



            // People routes
            route::controller(personController::class)->group(function () {
                Route::post('person/employee/add', 'store');
                Route::get('person/employee/findByEmpID/{employeeId}', 'findByEmpID');
                Route::get('person/employee/findByID/{id}', 'findByID');
                Route::post('person/employee/update/{id}', 'update');
                Route::post('person/employee/delete/{id}', 'destroy');
                // Route::get('person/user/{userId}',  'getByUser');
                // Route::get('person/search/{query}',  'search');
                // Route::get('person/statistics/totals', 'getStatistics');
                // Route::post('person/bulk',  'bulkStore');
            });
        })->middleware(['role:admin']);


        // base routes
        Route::controller(baseController::class)->group(function () {
            route::get('base/index', 'getAllBase');
            route::get('base/find/{id}', 'getBaseById');
            route::get('base/getBaseByCircle/{base}', 'getBaseByCircle');

            route::post('base/add', 'add');
            route::post('base/update/{id}', 'update');


            // fix
            route::post('base/fix', 'fix');
        }); //->middleware(['role:admin']);
    }
)
;
