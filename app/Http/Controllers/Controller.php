<?php

namespace App\Http\Controllers;

use App\Shared\Infrastructure\Persistence\Http\Response as HttpResponse;
//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
//use Illuminate\Foundation\Bus\DispatchesJobs;
//use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response as ResponseIlluminate;
use Illuminate\Validation\ValidationException;
use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Infrastructure\Utils\Response;

class Controller extends BaseController
{
    protected HttpResponse $response;

    public function __construct(
    ) {
        $this->response = HttpResponse::make();
    }

//    protected function catchException(
//        ValidationException |
//        NotFoundException |
//        UnprocessableException $exception
//    ): ResponseIlluminate {
//        $status = function_exists($exception->getCode()) ? $exception->getCode() : 422;
//
//        $this->response->status($status);
//        $this->response->data([]);
//        $this->response->error($exception->getMessage());
//
//        return $this->response->get();
//    }
}
