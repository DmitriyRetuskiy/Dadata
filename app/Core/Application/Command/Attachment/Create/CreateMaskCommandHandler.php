<?php

namespace App\Core\Application\Command\Attachment\Create;

use App\Core\Application\Service\AttachmentService;
use App\Core\Application\Service\MaskService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateMaskCommandHandler
{

    public function __invoke(CreateMaskCommand $command): UploadedFile
    {
        $objMaskService = new MaskService();
        $imgPath = $objMaskService->releaseImageFromMask($command);
        $objUploade = new UploadedFile($imgPath, 'result_image.jpg', 'image/webp'); // возвращаем UploadedFile
        return $objUploade;
    }

}
