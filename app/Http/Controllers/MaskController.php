<?php

namespace App\Http\Controllers;

use App\Application\ImageService;
use App\Core\Application\Command\Attachment\Create\CreateMaskCommand;
use App\Core\Application\Command\Attachment\Create\CreateMaskCommandHandler;
use App\Core\Application\Service\AttachmentService;
use App\Shared\Infrastructure\Persistence\Http\Response;
use http\Exception\BadQueryStringException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\TextUI\Command;


class MaskController extends Controller
{
    public function getMaskDate(
        Request $request
    ): \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
    {
        $command = CreateMaskCommand::fromMaskRequest($request);
        $hendler = new CreateMaskCommandHandler();
        // возвращает attachment
        $fileStorageObject = $hendler($command);
//        return \response("true", 200)->header('Content-Type', 'text/html');
        // загружаем файлы миниатюр, добавляем записи в базу (//)
        $attachmentServ = new AttachmentService();
        $file = $attachmentServ->uploadFileByType($fileStorageObject);
        return \response($file->toArray(), 200)->header('Content-Type', 'text/html');
    }

    public function getMaskResult(
        Request $request
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $diskPath = Storage::path('');
        $imgPath = $diskPath . 'result_image.jpg';
        return \response()->file($imgPath);
    }


    public function getSQLDate(Request $request): \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
    {

        $users = DB::table('users')->get();
        try {
            DB::connection()->getPDO();
            dump('Database connected: ' . DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            dump('Database connected: ' . 'None' . '\n' . $e);
        }
        return \response("asdf", 200)->header('Content-Type', 'text/html');
    }

}
