<?php
namespace Mtrn\ApiService\ApiProviders;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


class ExampleApiProvider extends ApiProvider
{

    /**
     * @return Response
     */
   public function requestToApi(): Response
   {
       return Http::get($this->url); 
   }

}