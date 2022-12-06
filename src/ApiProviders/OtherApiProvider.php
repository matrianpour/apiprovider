<?php
namespace Mtrn\ApiService\ApiProviders;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


class OtherApiProvider extends ApiProvider
{

    /**
     * @return Response
     */
    public function requestFromProvider(): Response
    {
        return Http::get($this->url); 
    }

}