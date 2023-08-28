<?php

namespace App\Core\Application\Service;
use Illuminate\Support\Facades\Http;


class DaDataService
{
    public const TOKEN = '3ca3b3846f7e797563b15f45e09fa8888e2e91e2';
    public const HTTP = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';

    public static function daDataJson(?string $org = '7803002209')
    {

        try {
            $response = Http::withHeaders([
                    'Authorization' => 'Token ' . self::TOKEN,
                    'Content-Type' => "application/json"
                ]

            )   ->withBody('{"query": "' .$org . '"}','application/json')
                ->post(self::HTTP);

        } catch (\Exception $e) {

            $response = $e->getMessage();
            var_dump($response);
        }

        return $response->body();
    }
}
