<?php

declare(strict_types=1);

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Web\Controller\TelegramHookController;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\Request\Body\RequestBodyParser;
use Yiisoft\Router\Route;
use Yiisoft\Yii\Sentry\SentryMiddleware;

return [
    Route::post('/telegram/hook')
        ->middleware(SentryMiddleware::class)
        ->middleware(RequestBodyParser::class)
        ->middleware(FormatDataResponseAsJson::class)
        ->action([TelegramHookController::class, 'hook']),
];
