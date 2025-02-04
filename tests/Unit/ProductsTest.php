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

    $response = $products->sendRequest($url,null, null, 'GET');

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

describe('test crud operations', function () {
    $url = 'https://nova.staging.astroselling.com/jupiter/v1/';
    $token = 'vmvlMPhDorWX0pjCRk6XLizh0TcAoKgNkbpgUngHg6aObAI5XtmrTpJWF4M9';
    $products = new Products($url, $token);

    $fakeProduct = new StdClass();
    $fakeProduct->price = 1000000;
    $fakeProduct->stock = 1;
    $fakeProduct->id_in_erp = 'created_from_test';
    $fakeProduct->sku = 'created_from_test';
    $fakeProduct->title = 'Created from Test';
    $fakeProduct->currency = 'USD';

    $channelId = $products->getChannels()->data->{1}->id;


    it('create product', function () use ($products, $fakeProduct, $channelId) {
        $response  = $products->createProduct($channelId, $fakeProduct);
        expect($response)->toBeTrue();
    });

    it('get product', function () use ($products, $fakeProduct, $channelId) {
        $response  = $products->getProduct($channelId, $fakeProduct->id_in_erp);
        expect($response->id_in_erp)->toEqual($fakeProduct->id_in_erp);
    });

    it('update product', function () use ($products, $fakeProduct, $channelId) {
        $response  = $products->updateProduct($channelId, $fakeProduct);
        expect($response)->toBeTrue();
    });

    it('delete product', function () use ($products, $fakeProduct, $channelId) {
        $response  = $products->deleteProduct($fakeProduct->id_in_erp, $channelId);
        expect($response)->toBeTrue();
    });
});