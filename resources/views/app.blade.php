<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ env('APP_NAME', 'HRM System') }}</title>
    <meta name="description" content="Modern HRM system for employee management, attendance, leave and payroll.">
    <meta name="keywords" content="HRM, human resources, attendance, payroll, leave, employees">
    <meta name="author" content="{{ env('APP_NAME', 'HRM System') }}">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ env('APP_NAME', 'HRM System') }}">
    <meta property="og:description" content="Modern HRM system for employee management, attendance, leave and payroll.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ env('APP_NAME', 'HRM System') }}">
    <meta name="twitter:description" content="Modern HRM system for employee management, attendance, leave and payroll.">
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div id="app"></div>
</body>
</html>

