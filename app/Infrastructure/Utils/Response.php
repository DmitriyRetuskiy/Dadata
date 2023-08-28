<?php

namespace Infrastructure\Utils;

use Illuminate\Http\Response as R;

class Response {
    private int $status = 200;

    private mixed $data = null;

    private ? string $message = null;

    private ? string $error = null;

    public static function make(): Response {
        return new Response();
    }

    public function status(
        int $status
    ): void {
        $this->status = $status;
    }

    public function data(
        mixed $data
    ): void {
        $this->data = $data;
    }

    public function message(
        string $message
    ): void {
        $this->message = $message;
    }

    public function error(
        string $error
    ): void {
        $this->error = $error;
    }

    public function get(): R {
        return response(
            array_filter(get_object_vars($this), fn(mixed $e) => isset($e)),
            $this->status
        );
    }
}
