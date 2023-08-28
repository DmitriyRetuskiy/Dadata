<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\DadataDTO;
use App\Shared\Infrastructure\Persistence\Http\Response;
use Laravel\Lumen\Http\Request;
use \Illuminate\View\View;
use App\Models\DadataDTO as Dadata;
use App\Core\Application\Service;

class TakeFormDataController extends Controller
{
    public function formData(\Illuminate\Http\Request $request):\Illuminate\Http\Response
    {
        // получить данные с дадата
        $res = Service\DaDataService::daDataJson();
        $res = json_decode($res,JSON_UNESCAPED_UNICODE);
        $dto = DadataDTO::daResDTO($res);

        // передаем в шаблон
        $view = view('test', [
            'Dadata' => $dto,
            'arrUser' => 'asdfasdf'
        ]);

       return \response($view)
           ->header('Content-Type', 'text/html')
           ->header('charset','utf-8');
    }


    public function formDataPost(\Illuminate\Http\Request $request):\Illuminate\Http\Response
    {
        $inn = $request->post('inn')??'';

        if ($inn != '') {
            $res = Service\DaDataService::daDataJson($inn);
            $res = json_decode($res,JSON_UNESCAPED_UNICODE);
            $dto = DadataDTO::daResDTO($res);
        } else {
            $dto = 'нет данных';
        }

        // передаем в шаблон
        $view = view('test', [
            'Dadata' => $dto,
            'arrUser' => 'asdfasdf'
        ]);

        return \response($view)
            ->header('Content-Type', 'text/html')
            ->header('charset','utf-8');
    }


}
