<?php

namespace App\Shared\Infrastructure\Persistence\Http;

class Response
{
    /**
     * @var int
     */
    public int $status = 200;

    /**
     * @var mixed|null
     */
    private mixed $data = null;

    /**
     * @var string|null
     */
    private ?string $message = null;

    /**
     * @var string|null
     */
    private ?string $error = null;

    /**
     * @return Response
     */
    public static function make(): Response
    {
        return new Response();
    }

    /**
     * @param int $status
     *
     * @return void
     */
    public function status(
        int $status
    ): void
    {
        $this->status = $status;
    }

    /**
     * @param mixed $data
     *
     * @return void
     */
    public function data(
        mixed $data
    ): void
    {
        $this->data = $data;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function message(
        string $message
    ): void
    {
        $this->message = $message;
    }

    /**
     * @param string $error
     *
     * @return void
     */
    public function error(
        string $error
    ): void
    {
        $this->error = $error;
    }

    /**
     * @return string[]
     */
    public function get(): array
    {
        $data = [];

        $data['status'] = $this->status;

        isset($this->data) && $data['data'] = $this->data;
        isset($this->message) && $data['message'] = $this->message;
        isset($this->error) && $data['error'] = $this->error;

        return $data;
    }
}
