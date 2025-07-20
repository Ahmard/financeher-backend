<?php

function app_url(?string $path = null): string
{
    $path = $path ? "/$path" : '';
    return config('app.url') . $path;
}

function support_url(?string $path = null): string
{
    $path = $path ? "/$path" : '';
    return config('app.support_url') . $path;
}

function frontend(string $url = ''): string
{
    return config('app.frontend_url') . $url;
}

function include_css_file(string $path): void
{
    echo '<style>';
    require base_path("/$path");
    echo '</style>';
}

function load_image_base64(string $path): string
{
    $fileName = public_path("/$path");
    $imageData = getimagesize($fileName);
    $data = file_get_contents($fileName);
    return "data:{$imageData['mime']};base64," . base64_encode($data);
}
