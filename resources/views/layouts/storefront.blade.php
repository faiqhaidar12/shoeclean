<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Order Online' }} - Shoe Clean</title>
    <meta name="description" content="{{ $description ?? 'Order layanan cuci sepatu online' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-manrope { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-artisan-background via-[#fbf8f2] to-artisan-surface-low">
    {{ $slot }}
</body>
</html>
