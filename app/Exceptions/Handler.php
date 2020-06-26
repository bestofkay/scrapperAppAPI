<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        ############# VALIDATION ERROR MESSAGES ##########
        if($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

         ############# MODEL ERROR MESSAGES ##########
        if($exception instanceof ModelNotFoundException){
            $modelname= strtolower(class_basename($exception->getModel()));
            return response()->json(['error'=>"{$modelname} does not exist with the identified specificator", 'code'=>404], 404);
        }

        if($exception instanceof BindingResolutionException){
           // $modelname= strtolower(class_basename($exception->getModel()));
            return response()->json(['error'=>"requested URL cannot be found", 'code'=>404], 404);
        }


         ############# HTTP ERROR MESSAGES ##########
         if($exception instanceof NotFoundHttpException){
            return response()->json(['error'=>"The specified URL cannot be found", 'code'=>404], 404);
        }

         ############# HTTP ERROR MESSAGES ##########
         if($exception instanceof RouteNotFoundException){
            return response()->json(['error'=>"Login to access requested page", 'code'=>401], 401);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            return response()->json(['error'=>"The specified method for the request is invalid", 'code'=>405], 405);
        }

         ############# OTHER HTTP ERROR MESSAGES ##########
         if($exception instanceof HttpException){
            return response()->json(['error'=>$exception->getMessage(), 'code'=>$exception->getStatusCode()], $exception->getStatusCode());
        }

          ############# OTHER DATABASE ERROR MESSAGES ##########
          if($exception instanceof QueryException){
            $errorCode= $exception->errorInfo[1];
            $errorMsg= $exception->errorInfo[2];
            return response()->json(['error'=>"The request can not be executed at the moment", 'code'=>409, 'dcode'=>$errorCode, 'msg'=>$errorMsg], 409);
        }

        ########## Must be set to false on production#########
        if(config('app.debug')){
            return parent::render($request, $exception);
        }

        return response()->json(['error'=>"Unexpected exception,service unavailable. Try again", 'code'=>500, 'dcode'=>$errorCode], 500);

    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors= $e->validator->errors()->getMessages();
        return response()->json(['error'=>$errors, 'code'=>422], 422);
    }
}
