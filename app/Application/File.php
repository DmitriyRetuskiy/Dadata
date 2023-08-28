<?php

namespace App\Application;

use App\Domain\Entity\File as FileEntity;
use App\Domain\Entity\FileOrigin;

class File
{
    public function __construct()
    {}

    public function addCropFiles(
        FileOrigin $file,
        array $cropFiles
    ): FileOrigin
    {
        foreach ($cropFiles as $cropFile){
            $file->addCropFiles($file->id(), $cropFile);
        }

        return $file;
    }
}
