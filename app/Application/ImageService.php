<?php

namespace App\Application;

use App\Domain\Entity\FileOrigin;
use App\Domain\ValueObjects\FileId;
use http\Env\Response;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

use DateTimeImmutable;

use Illuminate\Support\Facades\Log;

use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;
use Intervention\Image\ImageManager;

use Illuminate\Support\Facades\Storage;

use App\Domain\Entity\File as FileEntity;
use App\Application\File as FileService;

use App\Infrastructure\Persistance\MySQL\File as FileModel;

use Exception;

use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
    public function __construct(
        private readonly FileService $fileService,
        private readonly FileModel $fileModel
    )
    {
    }

    /**
     * @throws Exception
     */
    public function uploadFileImage(
        FileOrigin $file
    ): FileOrigin
    {
        return match ($file->extension()) {
            'jpeg', 'JPEG', 'jpg', 'JPG', 'png', 'PNG', 'webp', 'WEBP' => $this->addImageToServe($file),
            default => throw new UnprocessableException('Тип файла не подходит'),
        };
    }

    /**
     * @throws Exception
     */
    public function uploadFile(
        FileOrigin $file
    ): FileOrigin {
        return $this->addFileToServe($file);
    }

    /**
     * @throws Exception
     */
    public function addFileToServe(
        FileOrigin $file
    ): FileOrigin
    {
        //Save origin file on disk
        Storage::disk('public')->putFileAs($file->dirPath(), $file->uploadFile(), $file->originMd5Name());

        $this->save($file);

        return $file;
    }
    /**
     * @throws Exception
     */
    public function addImageToServe(
        FileOrigin $file
    ): FileOrigin
    {
        //Save origin file on disk
        Storage::disk('public')->putFileAs($file->dirPath(), $file->uploadFile(), $file->originMd5Name());

        //Crop Image and convert to Webp. Save on disk
        $imageFiles = $this->fileService->addCropFiles($file, $this->cropImage($file));

        $this->save($imageFiles);

        return $imageFiles;
    }

    private function save(
        FileOrigin $files
    ): void
    {
        $this->fileModel->insertAllCropFiles($files);
    }

    /**
     * @throws Exception
     */
    public function cropImage(
        FileOrigin $file
    ): array
    {
        $arrayOfImageSize = config('app.imageSize');
        $arrayOfCropFilesPath = [];

        foreach ($arrayOfImageSize as $keySlugBySize => $itemSize) {
            $arrayOfCropFilesPath[] = $this->cropAndSendImageToServ(
                file: $file,
                slugBySize: $keySlugBySize,
                width: $itemSize['width'] ?? null,
                height: $itemSize['height'] ?? null,
                suffix: $itemSize['suffix'] ?? '_other'
            );
        }

        return $arrayOfCropFilesPath;
    }

    /**
     * @throws Exception
     */
    private function cropAndSendImageToServ(
        FileOrigin $file,
        string     $slugBySize,
        ?int       $width,
        ?int       $height,
        string     $suffix
    ): array
    {
        return $this->imageMagick(
            file: $file,
            slugBySize: $slugBySize,
            width: $width,
            height: $height,
            suffix: $suffix
        );
    }

    private function imageMagick(
        FileOrigin $file,
        string     $slugBySize,
        ?int       $width,
        ?int       $height,
        string     $suffix
    ): array
    {
        Image::configure(['driver' => 'imagick']);
        $image = Image::make($file->tmpPath());

        $fileName = $file->fileNameWithoutExtension() . $suffix;
        $fullFileName = $fileName . '.webp';
        $cropPhoto = false;

        if ($slugBySize != 'origin') {
            $originalHeight = (int)getimagesize($file->tmpPath())[1];
            $originalWidth = (int)getimagesize($file->tmpPath())[0];
            $mainSideRule = ($originalWidth < $originalHeight) ? $originalWidth : $originalHeight;

            if ($width == 0 && $height != 0) {
                $pr = $height / $originalHeight;

                $width = $originalWidth * $pr;
            } elseif ($width != 0 && $height == 0) {
                $pr = $width / $originalWidth;

                $height = $originalHeight * $pr;
            } elseif ($width == $height) {
                $mainSide = $mainSideRule;

                $pr = $width / $mainSide;

                $cropWidth = $width;
                $cropHeight = $cropWidth;

                $width = $originalWidth * $pr;
                $height = $originalHeight * $pr;

                $cropPhoto = true;
            } else {
                $mainSide = ($width > $height) ? $width : $height;
                $mainSideOrigin = $mainSideRule;

                $pr = $mainSide / $mainSideOrigin;

                $cropWidth = $width;
                $cropHeight = $height;

                $width = $originalWidth * $pr;
                $height = $originalHeight * $pr;

                $cropPhoto = true;
            }

            $image->resize($width, $height);
            if ($cropPhoto == true) {
                $image->crop($cropWidth, $cropHeight, (int)(($width - $cropWidth) / 2), (int)(($height - $cropHeight) / 2));
            }
        }

        $imageRes = Image::make($image);
        $imageRes->encode('webp', 75);

        $type = Image::make((string)$imageRes)->mime;

        Storage::disk('public')->put($file->dirPath() . "/" . $fullFileName, $imageRes);

        return [
            'filenameWithoutExc' => $fileName,
            'fileName' => $fullFileName,
            'type' => $type,
            'path' => $file->dirPath() . "/" . $fullFileName,
            'slugBySize' => $slugBySize,
            'createdAt' => $file->createdAt()
        ];
    }
}
