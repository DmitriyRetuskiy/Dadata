<?php

namespace App\Models;

use App\Core\Domain\Model\Attachment;
use Illuminate\Support\Facades\DB;

class AttachmentModel
{
    static function insertDateTable(Attachment $attachment): bool
    {
        $request = DB::table('attachments');
        try {
            $request->insert([
                "uuid" => $attachment->uuid(),
                "name" => $attachment->name(),
                "original_name" => $attachment->path(),
                "parent_id" => $attachment->parentId(),
                "path" => $attachment->path(),
                "type" => $attachment->type(),
                "thumbnail_size" => $attachment->thumbnailSize(),
                "created_at" => $attachment->createdAt()
            ]);
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }

    }


}
