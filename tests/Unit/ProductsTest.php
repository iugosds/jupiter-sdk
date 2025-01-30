<?php

use astroselling\Jupiter\Products;

test('can initialize products', function () {
    $products = new Products();
    expect($products)
        ->toBeInstanceOf(Products::class)
        ->and($products->version())->toBeString();
});

test('can send request', function () {
    $products = new Products();
    $url = 'https://api.restful-api.dev/objects';

    $response = $products->sendRequest($url,'', '', 'GET');

    expect($response)->toBeObject();
});


test('getChannels: throw exceptions when unauthenticated', function () {
    $url = 'https://nova.astroselling.com/jupiter/v1/';

    $products = new Products($url, 'test');

    $products->getChannels();
})->throws(\Exception::class, 'Unauthenticated');


test('createProducts: throw exceptions when unauthenticated', function () {
    $url = 'https://nova.astroselling.com/jupiter/v1/';

    $products = new Products($url, 'test');

    $product = new StdClass();
    $products->createProduct('1234', $product);

})->throws(\Exception::class, 'Unauthenticated');

test('updateProducts: throw exceptions when unauthenticated', function () {
    $url = 'https://nova.astroselling.com/jupiter/v1/';

    $products = new Products($url, 'test');

    $product = new StdClass();
    $products->createProduct('1234', $product);

})->throws(\Exception::class, 'Unauthenticated');

test('getProducts: throw exceptions when unauthenticated', function () {
    $url = 'https://nova.astroselling.com/jupiter/v1/';

    $products = new Products($url, 'test');

    $products->getProducts('1234');

})->throws(\Exception::class, 'Unauthenticated');

test('getProduct: throw exceptions when unauthenticated', function () {
    $url = 'https://nova.astroselling.com/jupiter/v1/';

    $products = new Products($url, 'test');

    $products->getProduct('1234', '1234');

})->throws(\Exception::class, 'Unauthenticated');

test('deleteProduct: throw exceptions when unauthenticated', function () {
    $url = 'https://nova.astroselling.com/jupiter/v1/';

    $products = new Products($url, 'test');

    $products->deleteProduct('1234', '1234');

})->throws(\Exception::class, 'Unauthenticated');

