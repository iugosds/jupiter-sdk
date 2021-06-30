# jupiter-sdk

PHP package para que los sistemas ERP se puedan integrar a la plataforma de gestión de tiendas online Astroselling - https://astroselling.com/


para instalarlo:

- composer require astroselling/jupiter-sdk

--------
En la página de Astroselling, dentro del módulo “Mi cuenta” -https://nova-back.astroselling.com/admin/account- el usuario puede generar su propia API Key para acceder a estos servicios.

Por cualquier inconveniente, el equipo de soporte está siempre a las órdenes: soporte@astroselling.com
--------

Caso sea necesario hacer la integración directamente, usando  otro lenguaje, esta es la documentación de las API´s:


# Astroselling Jupiter - API

A continuación se podrán observar un conjunto de endpoints para poder integrar productos a Astroselling. En particular, se detallará cómo crear, actualizar y eliminar productos, obtener los canales asociados a un usuario y sus productos respectivos y visualizar el estado de la plataforma.

En la página de Astroselling, dentro del módulo "Mi cuenta" -https://nova-back.astroselling.com/admin/account- el usuario puede generar su propia API Key para acceder a estos servicios.

Por cualquier inconveniente, el equipo de soporte está siempre a las órdenes: soporte@astroselling.com


## Indice


  * [Autenticación](#authentication)
  * [Create Product](#1-create-product)
  * [Delete Product](#2-delete-product)
  * [Get Channel Products](#3-get-channel-products)
  * [Get Channel Products Info](#4-get-channel-products-info)
  * [Get Channels](#5-get-channels)
  * [Healthcheck](#6-healthcheck)
  * [Update Product](#7-update-product)


--------

# Authentication
Para consumir los endpoints se requiere autenticarse con un usuario de Astroselling. El token se puede obtener desde https://nova-back.astroselling.com, ingresando con el usuario correspondiente y luego ingresando a la sección "Mi Cuenta". Desde allí se podrá, además, generar un nuevo token, invalidando inmediatamente el token anterior.

Existen dos maneras de enviar el TOKEN para autenticar un request:
## Autenticación por QueryString:

**Atención: este método es inseguro ya que el token es enviado en la URL.

En la URL del request, añadir el querystring
```&api_token={{api_token}}```

Ejemplo:
```https://nova-back.astroselling.com/jupiter/v1/channels?api_token={{api_token}}```


## Autenticación por Bearer Token:
Agregar un header con el nombre "Authentication" y el valor "Bearer {{api_token}}". Ejemplo:

Ejemplo llamado CURL:
```
curl --location --request GET 'https://nova-back.astroselling.com/jupiter/v1/channels' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
```

Ejemplo en PHP con cliente Guzzle:
```
$response = $client->request('POST', '/api/user', [
    'headers' => [
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json',
    ],
]);
```

### 1. Create Product


Este endpoint se utiliza para dar de alta un producto en Astroselling. En el body del POST se debe de mandar toda la información del producto.
- Esta información también pueden ser las variaciones.



***Endpoint:***

```bash
Method: POST
Type: RAW
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/channels/{{CHANNEL_ID}}/products
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Content-Type | application/json |  |
| Accept | application/json |  |



***Body:***

```js
{
	"id_in_erp": "10200240",
	"sku": "10200240",
	"title": "Pantalon J\u00fanior",
	"description": "Pantalon J\u00c3\u00banior",
	"currency": "USD",
	"price": 790,
	"stock": 120,
	"variations": [
		{
			"id_in_erp": "10200240 001 06",
			"stock": 1,
			"price": 790,
			"sku": "10200240 001 06",
			"title": "Pantalon J\u00fanior",
			"attributes": [
				{
					"id": "attr_id_1",
					"name": "Color",
					"value": "Verde"
					},
				{
					"id": "attr_id_1",
					"name": "Talle",
					"value": "M"
				}
			],
			"images": [
				{
					"path": "http://via.placeholder.com/300/09f/fff.png?text=a"
				},
				{
					"path": "http://via.placeholder.com/300/09f/fff.png?text=b"
				},
				{
					"path": "http://via.placeholder.com/300/09f/fff.png?text=c"
				}
			]
		},
		{
			"id_in_erp": "10200240 002 06",
			"stock": 0,
			"price": 790,
			"sku": "10200240 002 06",
			"title": "Pantalon J\u00fanior",
			"attributes": [
				{
					"id": "attr_id_1",
					"name": "Color",
					"value": "Verde"
					},
				{
					"id": "attr_id_1",
					"name": "Talle",
					"value": "M"
				}
			],
			"images": [
				{
					"path": "http://via.placeholder.com/300/09f/fff.png?text=1"
				},
				{
					"path": "http://via.placeholder.com/300/09f/fff.png?text=2"
				},
				{
					"path": "http://via.placeholder.com/300/09f/fff.png?text=3"
				}
			]
		}
	],
	"images": [
		{
			"path": "https://contents.mediadecathlon.com/p1484240/ab565f3675dbdd7e3c486175e2c16583/p1484240.jpg"
		},
		{
			"path": "https://contents.mediadecathlon.com/p1484210/8ae4fe12797325bc4b98b6af45bc208b/p1484210.jpg"
		}
	]
}
```



### 2. Delete Product


Este endpoint se utiliza para eliminar un producto de un canal.


***Endpoint:***

```bash
Method: DELETE
Type: RAW
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/channels/{{CHANNEL_ID}}/products/{{ID_IN_ERP}}
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Content-Type | application/json |  |
| Accept | application/json |  |



### 3. Get Channel Products


El endpoint en cuestión retorna todos los artículos relacionados a un canal en particular.
En particular, veremos los siguientes datos:
- channel_id
- id_in_erp
- sku
- title
- price
- currency
- stock
- description
- extra_info
- variations
- images


***Endpoint:***

```bash
Method: GET
Type:
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/channels/{{CHANNEL_ID}}/products
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Accept | application/json |  |



***Query params:***

| Key | Value | Description |
| --- | ------|-------------|
| limit | 20 |  |
| offset | 0 |  |



### 4. Get Channel Products Info


Retorna la información de un canal y producto en particular.
Específicamente retorna los siguientes atributos
- channel_id
- id_in_erp
- sku
- title
- price
- currency
- stock
- description
- extra_info
- variations
- images


***Endpoint:***

```bash
Method: GET
Type:
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/channels/{{CHANNEL_ID}}/products/{{ID_IN_ERP}}
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Accept | application/json |  |


### 5. Get Channels


Entrega una lista de los Canales (con sus respectivos identificadores) que tiene el usuario. A partir de los IDs de la respuesta se podrán ejecutar los próximos endpoints.
En particular, retorna el id del Canal, el nombre interno, el método de sincronización (el cual generalmente es "push") y el tipo de Canal.


***Endpoint:***

```bash
Method: GET
Type:
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/channels
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Accept | application/json |  |


### 6. Healthcheck


El endpoint en cuestión permite obtener el estado de la plataforma.
- Si la API está saludable, retorna un HTTP Code 200.


***Endpoint:***

```bash
Method: GET
Type:
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/healthcheck
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Accept | application/json |  |



### 7. Update Product


Este endpoint se utiliza para actualizar la información de un artículo de un canal. Se debe pasar únicamente los atributos que se quieran actualizar.
Luego de hacer el PUT, se recibirá como retorno toda la información del producto de Astroselling actualizado.
- El atributo imágenes debes ser un arreglo.


***Endpoint:***

```bash
Method: PUT
Type: RAW
URL: http://nova-back.astroselling.com/jupiter/v1/jupiter/v1/channels/{{CHANNEL_ID}}/products/{{ID_IN_ERP}}
```


***Headers:***

| Key | Value | Description |
| --- | ------|-------------|
| Content-Type | application/json |  |
| Accept | application/json |  |


***Body:***

```js
{
	"price": 103,
	"stock": 8,
	"images": [
		"https://contents.mediadecathlon.com/p1484240/ab565f3675dbdd7e3c486175e2c16583/p1484240.jpg"
	]
}
```



***Available Variables:***

| Key | Value | Type |
| --- | ------|-------------|
| base_url | http://nova-back.astroselling.com/jupiter/v1 |  |
| api_token | XXXnBeVSIFFyldmxphoZTpYxfaMRm8xtUocYGpeiA4ftEKcXeP8aJif9AZZZ |  |
| channel_id | 9999 |  |



---
[Back to top](#astroselling-jupiter---api)


