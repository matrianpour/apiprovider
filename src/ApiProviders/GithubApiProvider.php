<?php
namespace Mtrn\ApiService\ApiProviders;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


class GithubApiProvider extends ApiProvider
{

    /**
     * @return Response
     */
    public function requestFromApi(): Response
    {
        return Http::get($this->url); 
    }

}