<?php

namespace App\Models;

use App\Domain\Entity\File as EntityFile;
use App\Domain\Entity\FileOrigin;
use App\Domain\ValueObjects\FileId;
use Illuminate\Support\Facades\DB;

class File
{
    public function insertOriginFile(
        FileOrigin $file
    ): int
    {
        $request = DB::table('attachments');
        return $request->insertGetId([
            'uuid' => $file->id()->id(),
            'name' => $file->fileNameWithoutExtension(),
            'original_name' => $file->originNameWithoutExtension(),
            'path' => $file->pathOriginFile(),
            'type' => $file->mimeType(),
            'parent_id' => '0',
            'thumbnail_size' => '0',
            'created_at' => $file->createdAt()
        ]);
    }

    public function insertCropFile(
        FileOrigin $file,
        array      $cropFile
    ): int
    {
        $request = DB::table('attachments');

        return $request->insertGetId([
            'uuid' => $cropFile['uuid'],
            'name' => $cropFile['name'],
            'original_name' => $file->originNameWithoutExtension(),
            'path' => $cropFile['path'],
            'type' => $cropFile['type'],
            'parent_id' => $file->id()->id(),
            'thumbnail_size' => $cropFile['thumbnail_size'],
            'created_at' => $file->createdAt()
        ]);

    }

    public function selectAll(
        ?string $ids = null
    ): array
    {
        $where = "";

        if (isset($ids) && $ids !== null) {
            $ids = implode("','", array_map('strval', explode(",", $ids)));
            $where = "WHERE a1.uuid IN ('" . $ids . "') OR a2.uuid IN ('" . $ids . "')";
        }

        $request = DB::select("SELECT * FROM (SELECT * FROM attachments WHERE parent_id = '0' order by created_at DESC LIMIT 48) as a1
                RIGHT JOIN attachments as a2 ON a2.parent_id = a1.uuid " . $where . " order by a2.created_at DESC ");

        return $request;
    }

    public function select(
        FileId $id
    ): array
    {
        $request = DB::table('attachments');
        $request->select(
            'uuid',
            'name',
            'original_name',
            'path',
            'type',
            'parent_id'
        );

        $request->where('uuid', '=', $id->id());
        $request->limit(1);

        return $request->get()->toArray();
    }

    public function deleteChildFile(
        FileId $idFile
    ): bool
    {
        $request = DB::table('attachments');
        $request->where('parent_id', '=', $idFile->id());
        return $request->delete();
    }

    public function deleteFile(
        FileId $idFile
    ): bool
    {
        $request = DB::table('attachments');
        $request->where('uuid', '=', $idFile->id());
        return $request->delete();
    }

    public function selectChildFiles(
        FileId $idFile
    ): array
    {
        $request = DB::table('attachments');

        $request->select(
            'uuid',
            'name',
            'original_name',
            'path',
            'type',
            'parent_id'
        );

        $request->where('parent_id', '=', $idFile->id());

        return $request->get()->toArray();
    }
}
