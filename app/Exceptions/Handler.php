<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // if (method_exists($exception, 'getStatusCode')) {
        //     $statusCode = $exception->getStatusCode();
        // } else {
        //     $statusCode = 500;
        // }

        // $response = [];

        // switch ($statusCode) {
        //     case 401:
        //         $response['message'] = 'Unauthorized';
        //         break;
        //     case 403:
        //         $response['message'] = 'Forbidden';
        //         break;
        //     case 404:
        //         $response['message'] = 'URL Not Found';
        //         break;
        //     case 405:
        //         $response['message'] = 'Method Not Allowed';
        //         break;
        //     case 422:
        //         $response['message'] = $exception->original['message'];
        //         $response['errors'] = $exception->original['errors'];
        //         break;
        //     default:
        //         $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
        //         break;
        // }

        // $response['status'] = $statusCode;

        // if (config('app.debug')) {
        //     $response['trace'] = $exception->getTrace();
        //     $response['code'] = $exception->getCode();
        // }

        // return response()->json($response, $statusCode);
        return parent::render($request, $exception);
    }
}
