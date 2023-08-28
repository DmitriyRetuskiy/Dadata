<?php

namespace App\Core\Application\Service;

use App\Application\File as FileService;
use App\Core\Application\Creator\AttachmentCreator;
use App\Core\Domain\Model\Attachment;
use App\Domain\Entity\FileOrigin;
use App\Infrastructure\Persistance\MySQL\File as FileModel;
use App\Shared\Infrastructure\ValueObject\UuidValueObject;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Utils\Exception\UnprocessableException;
use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
//    public function __construct(
//        private readonly FileService $fileService,
//        private readonly FileModel $fileModel
//    )
//    {
//    }

    /**
     * @throws Exception
     */
    public function uploadFileImage(
        Attachment $file
    ): Attachment
    {
        return match ($file->extension()) {
            'jpeg', 'JPEG', 'jpg', 'JPG', 'png', 'PNG', 'webp', 'WEBP' => $this->addImageToServe($file),
            default => throw new UnprocessableException('Тип файла не подходит'),
        };
    }

    /**
     * @throws Exception
     */
    public function addImageToServe(
        Attachment $attachment
    ): Attachment
    {
        //Save origin file on disk
        Storage::disk('public')->putFileAs($attachment->dirPath(), $attachment->uploadFile(), $attachment->originMd5Name());
        //Crop Image and convert to Webp. Save on disk
        //$imageFiles = $this->fileService->addCropFiles($attachment, $this->cropImage($attachment));
        //$this->save($imageFiles);
        $this->cropImage($attachment);
        return $attachment;
    }

    /**
     * @throws Exception
     */
    public function cropImage(
        Attachment $attachment
    ): void
    {
        $arrayOfImageSize = config('app.imageSize');
        $arrayOfCropFilesPath = [];

        foreach ($arrayOfImageSize as $keySlugBySize => $itemSize) {
            $this->cropAndSendImageToServ(
                attachment: $attachment,
                slugBySize: $keySlugBySize,
                width: $itemSize['width'] ?? null,
                height: $itemSize['height'] ?? null,
                suffix: $itemSize['suffix'] ?? '_other'
            );
        }

        //return $arrayOfCropFilesPath;
    }

    /**
     * @throws Exception
     */
    private function cropAndSendImageToServ(
        Attachment $attachment,
        string     $slugBySize,
        ?int       $width,
        ?int       $height,
        string     $suffix
    ): void
    {
        $this->imageMagick(
            attachment: $attachment,
            slugBySize: $slugBySize,
            width: $width,
            height: $height,
            suffix: $suffix
        );
    }

    private function imageMagick(
        Attachment $attachment,
        string     $slugBySize,
        ?int       $width,
        ?int       $height,
        string     $suffix
    ): void
    {

        Image::configure(['driver' => 'imagick']);
        $image = Image::make($attachment->tmpPath());

        $fileName = $attachment->name() . $suffix;
        $fullFileName = $fileName . '.webp';
        $cropPhoto = false;

        if ($slugBySize != 'origin') {
            $originalHeight = (int)getimagesize($attachment->tmpPath())[1];
            $originalWidth = (int)getimagesize($attachment->tmpPath())[0];
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

        Storage::disk('public')->put($attachment->dirPath() . "/" . $fullFileName, $imageRes);

        $thumbnail = new Attachment(
            uuid: UuidValueObject::create(),
            name: $fileName,
            originName: $attachment->originName(),
            type: $type,
            createdAt: $attachment->createdAt()
        );
        $thumbnail->addParent($attachment);
        $thumbnail->addPathThumbnail($attachment->dirPath() . "/" . $fullFileName);
        $thumbnail->addThumbnailSize($slugBySize);

        $attachment->addThumbnail(
            $thumbnail
        );

    }

    /**
     * @throws Exception
     */
    public function uploadFile(
        Attachment $attachment
    ): Attachment
    {
        return $this->addFileToServe($attachment);
    }

    /**
     * @throws Exception
     */
    public function addFileToServe(
        Attachment $attachment
    ): Attachment
    {
        //Save origin file on disk
        Storage::disk('public')->putFileAs($attachment->dirPath(), $attachment->uploadFile(), $attachment->originMd5Name());

        //$this->save($file);

        return $attachment;
    }

    private function save(
        FileOrigin $files
    ): void
    {
        $this->fileModel->insertAllCropFiles($files);
    }
}
