<?php

namespace App\Domain\Entity;

use App\Domain\ValueObjects\FileId;
use App\Infrastructure\Persistance\DTO\RequestDto;
use Illuminate\Http\Request;

use DateTimeImmutable;

use Illuminate\Http\UploadedFile;

class FileOrigin
{
    private ?string $originMd5Name;
    private ?string $newDirPath;
    private ?string $newOriginNameWithoutExt;
    private ?array $cropFilesArray;

    public function __construct(
        private readonly UploadedFile $uploadFileObject,
        private readonly FileId $id,
        private readonly string $originName,
        private readonly string $mimeType,
        private readonly string $fileType,
        private readonly string $tmpPath,
        private readonly string $extension,
        private readonly DateTimeImmutable $createdAt
    )
    {

    }

    public static function create(
        RequestDto $request
    )
    {
        $fileObject = $request->userFile;
        $createdAt = new DateTimeImmutable();

        return new FileOrigin(
            uploadFileObject: $fileObject,
            id: $request->id ? FileId::create($request->id) : FileId::create(),
            originName: $fileObject->getClientOriginalName(),
            mimeType: $fileObject->getClientMimeType(),
            fileType: self::setTypeFile($fileObject),
            tmpPath: $fileObject->getPathname(),
            extension: $fileObject->getClientOriginalExtension(),
            createdAt:  $createdAt
        );
    }

    public static function addFileName(){

    }

    /**
     * @return DateTimeImmutable
     **/
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function uploadFile(): UploadedFile
    {
        return $this->uploadFileObject;
    }

    public function id(): FileId
    {
        return $this->id;
    }

    public function fileType(): string
    {
        return $this->fileType;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function originName(): string
    {
        return $this->originName;
    }

    public function originNameWithoutExtension(): string
    {
        return pathinfo($this->originName, PATHINFO_FILENAME);
    }

    public function originMd5Name(): string
    {
        return $this->originMd5Name;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function dirPath(): string
    {
        return $this->newDirPath;
    }

    public function tmpPath(): string
    {
        return $this->tmpPath;
    }

    public function fileNameWithoutExtension(): string
    {
        return $this->newOriginNameWithoutExt;
    }

    public function pathOriginFile(): string
    {
        return $this->dirPath() . "/" . $this->originMd5Name();
    }

    public function newMd5FileName(
        string $name
    )
    {
        $this->newOriginNameWithoutExt = $name;
    }

    public function addOriginMd5Name()
    {
        $this->originMd5Name = $this->newOriginNameWithoutExt.".".$this->extension;
    }

    public function addDirPath()
    {
        $arrayForDir = array_slice(str_split($this->newOriginNameWithoutExt, 2), 0, 3);
        $dir = "";

        foreach ($arrayForDir as $item) {
            $dir .= "/" . $item;
        }

        $this->newDirPath = $dir;
    }

//    public function addFileNameWithoutExtension(string $fileName)
//    {
//        $this->newOriginNameWithoutExt = $fileName ?? null;
//    }


    private static function setTypeFile(
        object $fileObject
    ): string
    {
        $pieces = explode("/", $fileObject->getClientMimeType());
        return $pieces[0];
    }

    public function addCropFiles(
        FileId $parentFileId,
        array  $cropFile
    )
    {

        $cropFileId = FileId::create()->id();

        $this->cropFilesArray[$cropFile['slugBySize']] = [
            'uuid' => $cropFileId,
            'name' => $cropFile['filenameWithoutExc'],
            'original_name' => $this->originName(),
            'parent_id' => $parentFileId->id(),
            'path' => $cropFile['path'],
            'type' => $cropFile['type'],
            'thumbnail_size' => $cropFile['slugBySize'],
            //'created_at' => $this->createdAt()->format("Y-m-d H:i:s")
            'created_at' => $this->createdAt()->getTimestamp()
        ];
    }

    public function cropFiles(): array
    {
        return $this->cropFilesArray ?? [];
    }

    public function toArray(): array
    {
        return [
            'main' => [
                'uuid' => $this->id->id(),
                'name' => $this->fileNameWithoutExtension(),
                'original_name' => $this->originName(),
                'parent_id' => '0',
                'path' => $this->pathOriginFile(),
                'type' => $this->mimeType,
                'thumbnail_size'=> '0',
                'created_at' => $this->createdAt()->getTimestamp(),
            ],
            'cropFiles' => $this->cropFilesArray ?? null
        ];
    }

}
