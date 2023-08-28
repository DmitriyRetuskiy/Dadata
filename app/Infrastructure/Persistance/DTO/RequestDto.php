<?php

namespace App\Infrastructure\Persistance\DTO;

use Illuminate\Http\UploadedFile;

class RequestDto
{
    /**
     * @param string|null $id
     * @param UploadedFile|null $userFile
     */
    public function __construct(
        public readonly ? string $id,
        public readonly ? UploadedFile $userFile
    ){}

    public function toArray()
    {
        return [
            'id'=> $this->id,
            'userFile' => (array)$this->userFile
        ];
    }
}
