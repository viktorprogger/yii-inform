<?php

declare(strict_types=1);

use Viktorprogger\YiisoftInform\SubDomain\Telegram\Infrastructure\Web\Controller\TelegramHookController;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\Request\Body\RequestBodyParser;
use Yiisoft\Router\Route;

return [
    Route::post('/telegram/hook')
        ->middleware(FormatDataResponseAsJson::class)
        ->middleware(RequestBodyParser::class)
        ->action([TelegramHookController::class, 'hook']),
];
