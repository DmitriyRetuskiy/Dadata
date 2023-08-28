<?php

namespace App\Domain\Entity;

use App\Domain\ValueObjects\FileId;
use Illuminate\Support\Facades\Storage;

class File
{
    private ? array $cropFilesArray;

    /**
     * @param FileId $id
     * @param string $name
     * @param string $type
     * @param string $originalName
     * @param string $originFile
     */
    public function __construct(
        private readonly FileId $id,
        private readonly string $name,
        private readonly string $type,
        private readonly string $originalName,
        private readonly string  $originFile
    )
    {

    }

    public static function create(
        FileId $id,
        string $name,
        string $type,
        string $originalName,
        string $originFile
    ): File
    {
        return new File(
            id: $id,
            name: $name,
            type: $type,
            originalName: $originalName,
            originFile: $originFile
        );
    }

    public function addCropFiles(
        FileId $parentFileId,
        array $cropFile
    ){

        $cropFileId = FileId::create()->id();

        $this->cropFilesArray[$cropFileId] = [
            'id' => $cropFileId,
            'name' => $cropFile['fileName'],
            'type' => $cropFile['type'],
            //'originalName' => $this->originalName,
            'path' => $cropFile['path'],
            'parentId' => $parentFileId->id()

        ];
    }

    public function pathOriginFile(): string
    {
        return $this->originFile;
    }

    public function id(): FileId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function originalName(): string
    {
        return $this->originalName;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function originFile(): string
    {
        return $this->originFile;
    }

    public function cropFiles():array
    {
        return $this->cropFilesArray;
    }

    public function toArray(
    ): array
    {
        return [
            'id' => $this->id->id(),
            'name' => $this->name,
            'type' => $this->type,
            'originalName' => $this->originalName,
            'originFilePath' => $this->pathOriginFile(),
            'cropFiles' => $this->cropFilesArray
        ];

    }
}
