<?php

namespace App\Core\Application\Query\Create;

use App\Core\Application\Query\AttachmentQueryHandler;
use App\Core\Domain\Model\Attachment;
use Infrastructure\Utils\Exception\UnprocessableException;

class CreateAttachmentQueryHandler extends AttachmentQueryHandler
{
    public function __invoke(
        ?CreateAttachmentQuery $query = null
    ): array
    {

        if(isset($query->uuid) && $query->uuid != null){
            $attachment = $this->attachmentRepository->find($query->uuid);

            if ($attachment == null) throw new UnprocessableException('Файл не существует');

            return $attachment->toArray();
        }else{
            if($query->ids) $ids = "'".implode("','", array_map('strval',explode(",", $query->ids)))."'";
            if($query->offset) $offset = $query->offset;

            $count = $this->attachmentRepository->countOfAttachments();
            if(!empty($count)){
                $total = $count[0][1];
            }

            $attachments = $this->attachmentRepository->findAllAttachment($ids??null, $offset??null);
            $new_array = [];

            if($query->ids){
                $arrayFromQuery = explode(",", $query->ids);
                foreach ($arrayFromQuery as $uuidRule){
                    foreach ($attachments??[] as $obj){
                        $uuid = $obj->uuid()->uuid();
                        if($uuid == $uuidRule) $new_array[] = $obj;
                    }
                }

                $attachments = array_filter($new_array, function($element) {
                    return isset($element);
                });
            }

            if ($attachments == null) throw new UnprocessableException('Список файлов пуст');

            return [
                'total' => $total ?? null,
                'attachments' => array_reduce($attachments, fn($a, Attachment $e) => [...$a, $e->toArray()], [])
            ];
        }
    }
}
