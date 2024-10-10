<html lang="en">

<head>
    <title>Invoice</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        @page {
            margin: 390px 10, 10, 10;
            header: page-header;
            footer: page-footer;
        }

        html,
        body {
            font-family: roboto;
        }
    </style>
</head>

<body>
    {{ $slot }}
</body>

</html>
