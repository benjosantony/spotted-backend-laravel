<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'api/', 'middleware'=>'check.token.api'], function(){
    Route::get('user/login', 'Api\UserController@login');
    Route::get('user/sendActivationCode', 'Api\UserController@sendActivationCode');
    Route::get('user/activate', 'Api\UserController@activateByPhone');
    Route::get('game/startSinglePlay', 'Api\GameController@startSinglePlay');
    Route::post('game/submitSinglePlay', 'Api\GameController@endSinglePlay');
    Route::get('game/startMultiPlay', 'Api\MultiPlayController@startMultiPlay');
    Route::post('game/submitMultiPlay', 'Api\MultiPlayController@endMultiPlay');
    Route::get('game/submitQuiz', 'Api\GameController@submitQuiz');
    Route::get('game/getDailyReward', 'Api\GameController@getDailyReward');
    Route::get('advertisement/save', 'Api\AdvertisementController@bookmarkAdv');
    Route::get('advertisement/list', 'Api\AdvertisementController@listAdv');
    Route::get('advertisement/deal', 'Api\AdvertisementController@useAdv');
    Route::get('advertisement/delete', 'Api\AdvertisementController@deleteAdv');
    Route::get('upgrade/upgrade', 'Api\GameController@upgradeAbilities');
    Route::get('cashOut/upgradeInformation', 'Api\UserController@upgradeCashoutInformation');
    Route::get('social/shareForKey', 'Api\GameController@shareForKey');
    Route::get('social/askForHelp', 'Api\GameController@askForHelp');
    Route::get('social/sendRewardFb', 'Api\GameController@sendRewardFacebook');
    Route::get('inbox/getReward', 'Api\GameController@getReward');
    //Route::get('inbox/list', 'Api\GameController@getListReward');
    Route::get('cashOut/cashout', 'backend\CashOutController@cashout');

    Route::get('event/startSinglePlay', 'Api\EventController@playGame');
    Route::post('event/submitSinglePlay', 'Api\EventController@endGame');
    Route::get('event/share', 'Api\EventController@shareFB');
    Route::get('event/getprize', 'Api\EventController@getPrize');
    Route::get('event/confirmprize', 'Api\EventController@confirmPrize');
    Route::get('event/status', 'Api\EventController@getStatus');
    Route::get('event/leaderboard', 'Api\EventController@getLeaderBoard');
    Route::get('event/stage', 'Api\EventController@getLastStage');
    Route::get('event/condition', 'Api\EventController@getTermAndCondition');
    Route::get('version', 'Api\GameController@getVersion');

    Route::get('mail/list', 'Api\MailBoxController@listMailBoxForClient');
    Route::get('mail/read', 'Api\MailBoxController@markRead');
});

Route::group(['namespace'=> 'backend', 'prefix'=>'admin/'], function() {
    Route::get('', function () {
        return view('backend/index');
    });
});
Route::group(['prefix'=>'admin/', 'middleware'=>'check.login'], function(){
    Route::get('', 'backend\SystemController@index');

    Route::get('eventAdvertisement', 'backend\TelevisionEventController@eventAdvertisement');
    Route::post('televisionEventAdvertisementDetail', 'backend\TelevisionEventController@eventAdvertisementDetail');
    Route::post('insertTelevisionEventAdvertisement', 'backend\TelevisionEventController@insertEventAdvertisement');
    Route::post('saveTelevisionEventAdvertisement', 'backend\TelevisionEventController@saveEventAdvertisement');
    Route::get('eventLeaderboard', 'backend\TelevisionEventController@eventLeaderboard');
    Route::post('saveEventLeaderboard', 'backend\TelevisionEventController@saveEventLeaderboard');
    Route::post('publishEntireEventLeaderboard', 'backend\TelevisionEventController@publishEntireEventLeaderboard');
    Route::post('updateEventAdvertiseStatus', 'backend\TelevisionEventController@updateAdvertiseStatus');

    Route::get('advertisement', 'backend\AdvertisementController@advertisement');
    Route::post('advertisementDetail', 'backend\AdvertisementController@advertisementDetail');
    Route::post('insertAdvertisement', 'backend\AdvertisementController@insertAdvertisement');
    Route::post('saveAdvertisement', 'backend\AdvertisementController@saveAdvertisement');
    Route::post('updateAdvertiseStatus', 'backend\AdvertisementController@updateAdvertiseStatus');

    Route::get('academyConfig', 'backend\GameSettingsController@academyConfig');
    Route::post('insertAcademy', 'backend\GameSettingsController@insertAcademy');
    Route::post('updateAcademy', 'backend\GameSettingsController@updateAcademy');
    Route::delete('academyConfig/{id}', 'backend\GameSettingsController@deleteAcademy');

    Route::get('dockConfig', 'backend\GameSettingsController@dockConfig');
    Route::post('insertDock', 'backend\GameSettingsController@insertDock');
    Route::post('updateDock', 'backend\GameSettingsController@updateDock');
    Route::delete('dockConfig/{id}', 'backend\GameSettingsController@deleteDock');

    Route::get('factoryConfig', 'backend\GameSettingsController@factoryConfig');
    Route::post('insertFactory', 'backend\GameSettingsController@insertFactory');
    Route::post('updateFactory', 'backend\GameSettingsController@updateFactory');
    Route::delete('factoryConfig/{id}', 'backend\GameSettingsController@deleteFactory');

    Route::get('levelConfig', 'backend\GameSettingsController@levelConfig');
    Route::post('insertLevel', 'backend\GameSettingsController@insertLevel');
    Route::post('updateLevel', 'backend\GameSettingsController@updateLevel');
    Route::delete('levelConfig/{id}', 'backend\GameSettingsController@deleteLevel');

    Route::get('compassRate', 'backend\GameSettingsController@compassRate');
    Route::post('insertCompass', 'backend\GameSettingsController@insertCompass');
    Route::post('updateCompass', 'backend\GameSettingsController@updateCompass');
    Route::delete('compassRate/{id}', 'backend\GameSettingsController@deleteCompass');

    Route::get('dailyRewardConfig', 'backend\GameSettingsController@dailyRewardConfig');
    Route::post('insertReward', 'backend\GameSettingsController@insertReward');
    Route::post('updateReward', 'backend\GameSettingsController@updateReward');
    Route::delete('dailyRewardConfig/{id}', 'backend\GameSettingsController@deleteReward');

    Route::post('question', 'backend\AdvertisementController@question');
    Route::post('insertQuestion', 'backend\AdvertisementController@insertQuestion');
    Route::post('updateQuestion', 'backend\AdvertisementController@updateQuestion');
    Route::delete('question/{id}', 'backend\AdvertisementController@deleteQuestion');

    Route::post('questionAnswer', 'backend\AdvertisementController@questionAnswer');
    Route::post('insertQuestionAnswer', 'backend\AdvertisementController@insertQuestionAnswer');
    Route::post('updateQuestionAnswer', 'backend\AdvertisementController@updateQuestionAnswer');
    Route::delete('questionAnswer/{id}', 'backend\AdvertisementController@deleteQuestionAnswer');

    Route::get('user', 'backend\UserController@user');
    Route::post('userDetail', 'backend\UserController@userDetail');
    Route::post('updateUser', 'backend\UserController@updateUser');
    Route::post('getUserFromId/{id}', 'backend\UserController@getUserFromId');

    Route::get('gameRecord', 'backend\GameRecordController@gameRecord');
    Route::get('gameRecordPagingAjax', 'backend\GameRecordController@gameRecordPagingAjax');
    Route::get('gameRecordPagingAjax/{search}', 'backend\GameRecordController@gameRecordPagingAjax');
    Route::post('getGameFromId/{id}', 'backend\GameRecordController@getGameFromId');

    Route::get('payout', 'backend\CashOutController@managePayout');
    Route::post('manualPayment/{id}', 'backend\CashOutController@manualPayment');
    Route::post('processPayment/{id}', 'backend\CashOutController@payout');
    Route::post('cancelPayment/{id}', 'backend\CashOutController@cancelPayment');
    Route::get('updatePayout', 'backend\CashOutController@updatePayoutStatusFromXfers');

    Route::post('visitorCountry', 'backend\SystemController@visitorCountry');
    Route::post('visitorPerDay', 'backend\SystemController@visitorPerDay');
    Route::post('cashoutAndUser', 'backend\SystemController@cashoutAndUser');
    Route::post('gameStatistic/{time}', 'backend\GameRecordController@getNumberOfGameInTimeInterval');

    Route::post('login', 'backend\SystemController@login');
    Route::post('logout', 'backend\SystemController@logout');
    Route::post('adminChangePassword', 'backend\SystemController@adminChangePassword');
    Route::post('autoApproveBankStatus', 'backend\SystemController@autoApproveBankStatus');
    Route::get('login', function(){
        return view('backend/login');
    });
    Route::get('advertising/create', 'UploadAdvertisingController@index');
    Route::post('advertising/create', 'UploadAdvertisingController@create');
    Route::get('export', 'backend\ExportController@index');
    Route::get('exportGameRecord', 'backend\ExportController@gameRecord');
    Route::get('exportEventGameRecord', 'backend\ExportController@eventGameRecord');
    Route::post('getEventTime', 'backend\ExportController@getEventTime');

    Route::get('mailbox', 'backend\MailBoxController@index');
    Route::POST('mailbox/send', 'backend\MailBoxController@sendMessage');
});