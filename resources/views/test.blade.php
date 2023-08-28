<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dadata org</title>
</head>
<body>

<style>
    div{
        border: 0px solid black;
        padding: 10px 10px 10px 10px;
    }
    .type-name{
        float:left;
    }
</style>

<form action="/dadata" method="POST">
    <input type="text" name="inn" size="44" />
    <input type="submit" name="dadata" value="send" />
</form>

@if($Dadata != 'нет данных')
    <div>
       <div class="type-name"> Наименование оргранизации:  </div> <div> {{$Dadata->getOrgName()}}</div>
    </div>
    <div>
        <div class="type-name">{{$Dadata->getManagmentPost()}} :  </div> <div> {{$Dadata->getManagmentName()}}  </div>
    </div>
    <div>
        <div class="type-name"> Aдрес:   </div> <div>{{$Dadata->getAddressValue()}}</div>
    </div>
    <div>
        <div class="type-name"> Полный:   </div> <div>{{$Dadata->getUnrestrictedValue()}}</div>
    </div>
@else
    <div> Введите ИНН, <br /> возможно такого ИНН нет, <br />
    Попробуйте 7803002209</div>
@endif
{{-- @if (Dadata->getOrgName())--}}



{{-- @endif--}}

</body>
</html>
