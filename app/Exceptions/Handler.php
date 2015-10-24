<?php

namespace Madokami\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        if($request->acceptsJson() && $request->ajax()) {
            // Provide JSON response
            $response = [ 'success' => false, 'error' => '' ];

            // Set response HTTP code and headers
            if($e instanceof HttpExceptionInterface) {
                $code = $e->getStatusCode();
                $headers = $e->getHeaders();
            }
            else {
                $code = 500;
                $headers = [ ];
            }

            if($e instanceof MaxUploadSizeException) {
                $response['error'] = 'File too big.';
            }
            elseif($e instanceof DisallowedFileTypeException) {
                $response['error'] = 'Disallowed file type.';
            }
            else {
                $response['error'] = 'Server error.';
            }

            // Add exception to response if debug mode is turned on
            if(config('app.debug') === true) {
                $response['exception'] = [
                    'exception' => get_class($e),
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                    'file' => $e->getFile(),
                    'file_line' => $e->getLine(),
                ];
            }

            return response()->json($response, $code, $headers);

        }
        else {
            // Normal HTML error page
            return parent::render($request, $e);
        }
    }
}
