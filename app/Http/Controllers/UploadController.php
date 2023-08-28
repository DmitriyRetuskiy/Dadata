<?php

namespace App\Http\Controllers;

use App\Core\Application\Command\Attachment\Create\CreateAttachmentCommand;
use App\Core\Application\Command\Attachment\Create\CreateAttachmentCommandHandler;
use App\Core\Application\Command\Attachment\Remove\RemoveAttachmentCommand;
use App\Core\Application\Command\Attachment\Remove\RemoveAttachmentCommandHandler;
use App\Core\Application\Query\Create\CreateAttachmentQuery;
use App\Core\Application\Query\Create\CreateAttachmentQueryHandler;
use App\Infrastructure\Persistance\DTO\Factory\File as FileDataFactory;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;

class UploadController extends Controller
{

    /**
     * @throws NotFoundException
     * @throws UnprocessableException
     */
    public function uploadAttachment(
        Request $request
    ): Response
    {
        $this->validateFile($request);
////         var_dump($request->file('userFile'));
        $command = CreateAttachmentCommand::fromRequest($request);
        $handler = CreateAttachmentCommandHandler::make();

//        var_dump($handler);
        $this->response->data($handler($command));
        $this->response->status(200);
        $this->response->message('Файлы успешно загружены.');
        return response($this->response->get(), $this->response->status);
    }

    /**
     * @throws UnprocessableException
     * @throws NotFoundException
     */
    private function validateFile(
        Request $request
    )
    {
        $files = $request->file('userFile');

        if (!$files) {
            throw new NotFoundException('Файл отсутствует');
        }
//файлы массив или один файл?
//        if (count($files) > 10) {
//            throw new UnprocessableException('Можно загрузить не более 10 файлов за один раз');
//        }
    }

    /**
     * @throws UnprocessableException
     */
    public function getAttachment(
        string $key
    ): Response
    {
        $query = CreateAttachmentQuery::fromRequest($key);
        $handler = CreateAttachmentQueryHandler::make();

        $this->response->data($handler($query));
        $this->response->status(200);
        $this->response->message('Файл получен.');

        return response($this->response->get(), $this->response->status);
    }

    /**
     * @throws UnprocessableException
     */
    public function getAllAttachments(
        Request $request
    ): Response
    {
        $query = CreateAttachmentQuery::fromRequest(null, $request->get('identifiers'), $request->get('offset'));
        $handler = CreateAttachmentQueryHandler::make();

        $this->response->data($handler($query));
        $this->response->status(200);
        $this->response->message('Файлы получены.');

        return response($this->response->get(), $this->response->status);
    }

    /**
     * @throws NotFoundException
     * @throws UnprocessableException
     */
    public function deleteAttachments(
        Request $request
    ): Response
    {

        $command = RemoveAttachmentCommand::fromRequest($request);
        $handler = RemoveAttachmentCommandHandler::make();

        $this->response->data($handler($command));
        $this->response->status(200);
        $this->response->message('Файлы успешно удалены.');

        return response($this->response->get(), $this->response->status);
    }
}
