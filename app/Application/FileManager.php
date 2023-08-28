<?php

namespace App\Application;

use App\Domain\Entity\FileOrigin;
use App\Domain\ValueObjects\FileId;
use App\Infrastructure\Persistance\DTO\FileId as FileIdDTO;
use App\Infrastructure\Persistance\DTO\RequestDto;

use App\Infrastructure\Persistance\MySQL\File as FileModel;
use DateTimeImmutable;

use Exception;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;

class FileManager
{
    public function __construct(
        private readonly ImageService $imageService,
        private readonly FileModel $fileModel
    )
    {
    }

    /**
     * @throws Exception
     */
    public function uploadFileByType(
        RequestDto $requestDto
    ): array
    {
        if (!$requestDto->userFile) {
            throw new NotFoundException('Файл отсутствует');
        }

        $file = FileManager::createFile($requestDto);

        $fileFinal = match ($file->fileType()) {
            'image' => $this->imageService->uploadFileImage($file),
            default => $this->imageService->uploadFile($file)
        };

        return $fileFinal->toArray();
    }

    private static function createFile(
        $requestDto
    ): FileOrigin
    {
        $file = FileOrigin::create($requestDto);

        $unixTime = (new DateTimeImmutable())->getTimestamp();
        $fileName = explode(".", $file->originName())[0] . $unixTime;

        $name = md5($fileName);

        $file->newMd5FileName($name);
        $file->addOriginMd5Name();
        $file->addDirPath();

        return $file;
    }

    /**
     * @throws NotFoundException
     */
    public function getAllFiles(
        ?string $ids = null
    ): array
    {
        $all = $this->fileModel->selectAllFiles($ids);

        foreach ($all as $item) {
            if($item->created_at){
                $item->created_at = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item->created_at)->getTimestamp();
            }

            if ($item->parent_id == '0') {
                $arr[$item->uuid]['main'] = $item;
            } else {
                $arr[$item->parent_id]['thumbnails'][$item->thumbnail_size] = $item;

            }
        }

        return $arr;
    }

    /**
     * @throws NotFoundException
     * @throws UnprocessableException
     */
    public function deleteFileById(
        FileIdDTO $idFile
    ): bool
    {
        $id = $this->createIdFile($idFile);

        $file = $this->fileModel->selectFile($id);
        $dir = pathinfo($file[0]->path)['dirname'];

        $this->deleteChildFiles($file, $id);

        Storage::delete($file[0]->path);//OriginFile

        $this->recursiveCheckEmptyDirectory($dir);

        return $this->fileModel->deleteFile($id);
    }

    /**
     * @throws UnprocessableException
     */
    public function deleteChildFiles(
        array  $file,
        FileId $id
    )
    {
        if ($file[0]->parent_id == 0) {
            $childFiles = $this->fileModel->selectChildFiles($id);

            $pathToFiles = array_map(function ($value) {
                return $value->path;
            }, $childFiles);

            Storage::delete($pathToFiles);

            $this->fileModel->deleteChildFile($id);
        }
    }

    public function recursiveCheckEmptyDirectory($dir)
    {
        Storage::allFiles($dir);

        if (empty(Storage::allFiles($dir))) {
            Storage::deleteDirectory($dir);

            $dirs = preg_split('/\//', $dir);
            array_pop($dirs);
            $dir = implode("/", $dirs);

            if (count($dirs) > 1) {
                $this->recursiveCheckEmptyDirectory($dir);
            }
        }
    }

    /**
     * @param FileIdDTO $idFile
     *
     * @return FileId
     **/
    public function createIdFile(
        FileIdDTO $idFile
    ): FileId
    {
        $id = $idFile->id;
        return FileId::create($id);
    }
}
