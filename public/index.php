<?php
require '../vendor/autoload.php';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/framesmile/public', '', $request);
$request = rtrim($request, '/') ?: '/';

$routes = [
    '/'         => '../pages/home.php',
    '/home'     => '../pages/home.php',
    '/about'    => '../pages/about.php',
    '/contact'  => '../pages/contact.php',
    '/login'    => '../pages/login.php',
    '/sign-up'  => '../pages/sign-up.php',
    '/editor'   => '../pages/editor.php',
    '/orders'   => '../pages/orders.php',
    '/product'  => '../pages/product.php',
];

if (array_key_exists($request, $routes)) {
    require $routes[$request];
} else {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan";
}