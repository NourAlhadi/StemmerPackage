<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ISRI Stemmer</title>

    <style>
        *{
            margin: 0;
            @if(Config::get('app.locale') == 'ar')
                direction: rtl;
            @endif
        }

        body{
            background-color: #e6e6e6;
        }

        #content{
            font-family: 'Nunito', Tahosans-serif, Tahoma ,sans-serif;
            background-color: #fff;
            text-align: center;
            margin: 70px auto;
            min-height: 50px;
            padding: 20px;
            width: 60%;
        }
    </style>

    @yield('css')
</head>
<body>
    <div id="content">
        @yield('body')
    </div>
</body>
</html>
