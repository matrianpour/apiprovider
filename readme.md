# ApiService

[![Latest Version on Packagist][ico-version]][https://img.shields.io/packagist/v/mtrn/apiservice.svg?style=flat-square]
[![Total Downloads][ico-downloads]][https://img.shields.io/packagist/dt/mtrn/apiservice.svg?style=flat-square]
[![Build Status][ico-travis]][https://img.shields.io/travis/mtrn/apiservice/master.svg?style=flat-square]
[![StyleCI][ico-styleci]][https://styleci.io/repos/12345678/shield]

This package pdrovides an api-service to ease working with different apis.
It maps api-responses to objects of your choice by using a trait named IsApiClient. In this way you are free to define different clients for a single api. You'r also able to use diffrent apis for a single client.

let's get a look at structur:  

├── [configs](#configs)  
│   └── apiservice.php  
├── app  
│   └── Models  
│       └── [Client.php](#clients)  
│   └── Services  
│       └── ApiService  
│           └── [ApiProviders](#apiProviders)  
│               └── GoogleApiProvider.php  
│           └── [Decorators](#decorators)  
│               └──GoogleClientDecorator.php  


## Configs

The package's config-file is apiservice.php. Its options are:  

##### apis:  
This is an associative array where keys are the name of apis and values are array of configs for the api.  

Each api has three attributes:  

> url: the api's url  
>
> response_type: the type of api response (currently supported type is json)  
>
> data_access_keys: it is an associative array where a key is client-name and its value is access-key to the client-related-data.  

* default access-key would be the name of the client; like 'user' => 'user'.  
* 'user' => ' ' indicates that the whole response-body is related to the client user.  

##### defaults: 
this option controls the default. You're free to change them as you want. Its only value it response_type which is json.  

##### path_to_decorators: 
This value is used to access decorators.  

##### path_to_apiproviders:
This value is used to access to api-providers.  


## Client.php

This is an example client object which get use of trait **IsApiClient**.  
It must implement function getMappedArray() to present its data in array format.  

## ApiProviders

All your api-provider classes should be placed in this directory.  
An api-provider is used to handle api related functionalities; like sending a request to the api, or choosing a way to extract data from response based on its type.  
For each one of your apis you should create an api-provider and name it in format **ApinameApiProvider**.  

GoogleApiProvider is an example of api-provider.  

**Note:** They must extend ApiProvider abstract and implement method requestFromApi().  

## Decorators

All your decorator classes should be placed in this directory.  
A decorator is used to decorate your clients. The nameing format is **ApinameClientnameDecorator**.  

**Note:** They must extend Decorator abstract and implement method mapApiData();  



## Installation

Via Composer

``` bash
$ composer require mtrn/apiservice
```

## Usage

lets take a look at the provided example.  

> GoogleApiProvider
    
    use Mtrn\ApiService\Services\ApiService\ApiProviders\ApiProvider;

    class GoogleApiProvider extends ApiProvider
    {
        public function requestFromApi()
        {
            return Http::get($this->url); 
        }
    }


> Client 

    use Mtrn\ApiService\Traits\IsApiClient;

    class Client
    {
        use IsApiClient

        public function getMappedArray()
        {
            return $this->toArray();
        }
    }

> Decorator 

    use Mtrn\ApiService\Services\ApiService\Decorators\Decorator;

    class GoogleClientDecorator extends Decorator
    {
        /**
         * Define your map rules here.
         *
         * @param array $data
         * @return object
         */
        public function mapApiData(array $data): object
        {
            $this->client->setAttribute('name', $data['first_name'].' '.$data['last_name']);
            return $this->client;
        }
    }


> request to google and get response object:

    $client = new Client();
    $response = $client->requestFromApi($apiname='google', $map=false);

> request to google and get extracted data

    $client = new Client();
    $client->requestFromApi($amiName='google', $map=false);
    $extractedData = $client->getApiBody();

> request to google and get mapped data object

    $client = new Client();
    $mappedData = $client->requestFromApi($apiname='google', $map=true);

> request to google and get mapped array

    $client = new Client();
    $client->requestFromApi('google', true);
    $mappedArray = $client->getMappedArray();

[ico-version]: https://img.shields.io/packagist/v/mtrn/apiservice.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mtrn/apiservice.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/mtrn/apiservice/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/mtrn/apiservice
[link-downloads]: https://packagist.org/packages/mtrn/apiservice
[link-travis]: https://travis-ci.org/mtrn/apiservice
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/mtrn
[link-contributors]: ../../contributors
