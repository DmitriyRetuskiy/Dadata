<?php

namespace App\Shared\Infrastructure\Persistence\Data;

use Spatie\LaravelData\Data;

class AbstractData extends Data
{
    /**
     * @return string[]
     **/
    public static function messages(): array {
        return [
            'uuid' => 'Значение `:attribute` должно соответствовать стандарту UUID.',
            'required' => 'Атрибут `:attribute` является обязательным.',
            'string' => 'Значение `:attribute` должно быть строковым.',
            'integer' => 'Значение `:attribute` должно быть числовым.',
            'between' => 'Значение `:attribute` должно содержать от :min до :max символов.',
            'regex' => 'Значение `:attribute` не должно содержать спец. символов и заглавных букв, кроме тире.',
            'array' => 'Атрибут `:attribute` должен быть массивом.'
        ];
    }

}
