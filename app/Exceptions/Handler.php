<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Asm89\Stack\CorsService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use apiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
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
    public function render($request, Exception $e)
    {
        $response=$this->handleException($request,$e);
        app(CorsService::class)->addActualRequestHeaders($response,$request);
        return $response;
    }

    public function handleException($request, Exception $exception)
    {
        if($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception,$request);
        }

        if($exception instanceof ModelNotFoundException){
            $model=class_basename($exception->getModel());
            return $this->errorResponse("No existe ninguna instancia de ".$model. " con el id especificado",404);
        }
        if($exception instanceof AuthenticationException){
                return $this->unauthenticated($request, $exception);
        }

        if($exception instanceof AuthorizationException){
            return $this->errorResponse("No posee permisos para realizar esta accion",403);
        }

        if($exception instanceof NotFoundHttpException){
            return $this->errorResponse("No se encontro la url especificada",404);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse("El metodo especificado no es valido",405);
        }
        if($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(),$exception->getStatusCode());
        }
        if($exception instanceof QueryException){
            $codigo=$exception->errorInfo[1];
            if($codigo==1451){
                return $this->errorResponse('No se puede eliminar el recurso por que esta relacionado con otro',409);

            }
            return $this->errorResponse($exception->getMessage(),$exception->getCode());
        }
        if($exception instanceof TokenMismatchException){
            return redirect()->back()->withInput($request->input());
        }
        //if(config(app.debug)){
            return parent::render($request, $exception);
        //}


        //return $this->errorResponse("Falla inesperada",500);

    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
//        if ($request->expectsJson()) {
//            return $this->errorResponse("No autenticado",401);
//        }
        if ($this->isFrontend($request)) {
            return redirect()->guest(route('login'));
        }
        return $this->errorResponse("No autenticado",401);

    }
    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
//        if ($e->response) {
//            return $e->response;
//        }
//
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFrontend($request)) {
            return $request->ajax()?response()->json($errors,422): redirect()->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }
        return $this->errorResponse($errors,422);
//
//        if ($request->expectsJson()) {
//            return $this->errorResponse($errors,422);
//        }
//
//        return redirect()->back()->withInput(
//            $request->input()
//        )->withErrors($errors);
    }

    private  function isFrontend($request){
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
