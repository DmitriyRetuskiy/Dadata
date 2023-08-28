<?php

namespace App\Http\Controllers;

use App\Domain\Entity\FileOrigin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Exception;

use App\Infrastructure\Persistance\DTO\FileId;
use App\Infrastructure\Persistance\DTO\Factory\File as FileDataFactory;
use App\Application\FileManager as ServiceFileManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;

class UploadControllerOld extends Controller
{
    public function __construct(
        private readonly ServiceFileManager $serviceFileManager
    )
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function uploadFile(
        Request $request
    ): Response
    {
        try {
            $this->validateFile($request);

            $result = $this->serviceFileManager->uploadFileByType(FileDataFactory::fromRequest($request));

            $this->response->status(200);
            $this->response->data($result);

            return $this->response->get();
        }  catch (
        ValidationException | NotFoundException | UnprocessableException $exception
        ) {
            return $this->catchException($exception);
        }
    }

    /**
     * @return Response
     *
     * @throws Exception
     */
    public function getAllFiles(
        Request $request
    ): Response
    {
        try {
            $result = $this->serviceFileManager->getAllFiles($request->get('ids'));

            $this->response->status(200);
            $this->response->data($result);

            return $this->response->get();
        }  catch (
        ValidationException | NotFoundException | UnprocessableException $exception
        ) {
            return $this->catchException($exception);
        }
    }

    /**
     * @param string $key
     * @return Response
     *
     */
    public function deleteFile(
        string $key
    ): Response
    {
        try {
            $idFile = $this->validateId($key);

            $result = $this->serviceFileManager->deleteFileById($idFile);

            $this->response->status(200);
            $this->response->data($result);

            return $this->response->get();
        }  catch (
        ValidationException | NotFoundException | UnprocessableException $exception
        ) {
            return $this->catchException($exception);
        }
    }

    /**
     * @throws Exception
     */
    private function validateFile(
        Request $request
    )
    {
        if (!$request->file('userFile')) {
            throw new NotFoundException('Файл отсутствует');
        }
    }

    /**
     * @throws UnprocessableException
     */
    private function validateId(
        string $idFile
    ): FileId
    {
        if (!is_string($idFile) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $idFile) !== 1)) {
            throw new UnprocessableException('Параметр `uuid` файла введен некорректно');
        }

        return FileId::create($idFile);
    }
}
