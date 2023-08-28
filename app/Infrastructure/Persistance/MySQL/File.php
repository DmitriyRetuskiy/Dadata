<?php

namespace App\Infrastructure\Persistance\MySQL;

use App\Domain\Entity\File as EntityFile;
use App\Domain\Entity\FileOrigin;
use App\Domain\ValueObjects\FileId;
use App\Models\File as FileModel;
use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;

class File
{
    public function __construct(
        private readonly FileModel $fileModel
    )
    {
    }

    public function insertAllCropFiles(
        FileOrigin $file
    )
    {
        $this->fileModel->insertOriginFile($file);

        if(!empty($file->cropFiles())){
            foreach ($file->cropFiles() as $cropFile) {
                $this->fileModel->insertCropFile($file, $cropFile);
            }
        }
    }

    /**
     * @throws NotFoundException
     */
    public function selectAllFiles(
        ?string $ids = null
    ): array
    {
        $allFiles = $this->fileModel->selectAll($ids);

        if (empty($allFiles)) {
            throw new NotFoundException('Файлы не загружены');
        }

        return $allFiles;
    }

    /**
     * @throws NotFoundException
     */
    public function selectFile(
        FileId $id
    ): array
    {
        $file = $this->fileModel->select($id);

        if (empty($file)) {
            throw new NotFoundException('Файл не существует');
        }

        return $file;
    }

    /**
     * @throws UnprocessableException
     */
    public function deleteChildFile(
        FileId $id
    ): bool
    {
        return $this->fileModel->deleteChildFile($id);
    }

    /**
     * @throws UnprocessableException
     */
    public function deleteFile(
        FileId $id
    ): bool
    {
        $boolResultByDelete = $this->fileModel->deleteFile($id);

        if ($boolResultByDelete === false) {
            throw new UnprocessableException('Файл не удален');
        }

        return $boolResultByDelete;
    }

    public function selectChildFiles(
        FileId $id
    ): array
    {
        return $this->fileModel->selectChildFiles($id);
    }
}
