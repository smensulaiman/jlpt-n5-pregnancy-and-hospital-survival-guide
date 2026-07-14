<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JLPT N5 Pregnancy & Hospital Survival Guide')</title>
    <meta name="description" content="Practical Japanese for foreign parents living in Japan: real hospital conversations for pregnancy, labor, childbirth, and postpartum care.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Zen+Kaku+Gothic+New:wght@500;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/book.css'])
</head>
<body class="book-body">
@yield('content')
</body>
</html>
