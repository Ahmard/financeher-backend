<?php

namespace App\Exceptions;


use App\Helpers\Http\Responder;
use App\Services\MailService;
use GuzzleHttp\Exception\RequestException;
use HttpStatusCodes\StatusCode;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler
{
    public static function handle(Exceptions $exceptions): void
    {
        self::dontReport($exceptions);

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return Responder::new()->notFound($e->getMessage());
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return Responder::new()->notFound($e->getMessage());
        });

        $exceptions->render(function (ResponseException $e, Request $request) {
            return Responder::new()->errorMessage($e->getMessage(), $e->getCode());
        });

        $exceptions->render(function (ValidationException $e) {
            return Responder::new()->validationError($e->errors());
        });

        $exceptions->render(function (ConfigItemNotFoundException $e) {
            return Responder::new()->errorMessage('Internal Server Error');
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e) {
            return Responder::new()->error(
                code: StatusCode::METHOD_NOT_ALLOWED->value,
                message: 'Method Not Allowed'
            );
        });

        $exceptions->render(function (RequestException $e) {
            self::fireEmail($e);
            return Responder::new()->error(
                code: StatusCode::INTERNAL_SERVER_ERROR->value,
                message: 'Internal Server Error'
            );
        });

        $exceptions->render(function (\InvalidArgumentException $e) {
            self::fireEmail($e);
            return Responder::new()->error(
                code: StatusCode::INTERNAL_SERVER_ERROR->value,
                message: 'Internal Server Error'
            );
        });

        $exceptions->render(function (SomethingWentWrongException $e) {
            self::fireEmail($e);
            return Responder::new()->error(
                code: StatusCode::INTERNAL_SERVER_ERROR->value,
                message: $e->getMessage()
            );
        });

        $exceptions->render(function (UnauthorizedException $e) {
            return Responder::new()->error(
                code: StatusCode::UNAUTHORIZED->value,
                message: $e->getMessage()
            );
        });
    }

    protected static function dontReport(Exceptions $exceptions): void
    {
        $exceptions->dontReport([
            WarningException::class,
            ModelNotFoundException::class,
            NotFoundHttpException::class,
            NotFoundException::class,
            MaintenanceException::class,
            ForbiddenException::class,
            SomethingWentWrongException::class,
            UnauthorizedException::class,
        ]);
    }

    private static function fireEmail(Throwable $exception): void
    {
        if (!App::isProduction()) return;

        MailService::new()
            ->setRecipient(config('app.error_reporting_email'))
            ->setSubject('Error Report')
            ->setBody(view('mails.error-local', [
                'exception' => $exception,
                'request' => request(),
            ]))
            ->send();
    }
}
