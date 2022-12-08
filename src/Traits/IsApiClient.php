<?php

namespace Mtrn\ApiService\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Mtrn\ApiService\Services\ApiService\ApiProviders\ApiProvider;
use Mtrn\ApiService\Services\ApiService\Decorators\Decorator;

trait IsApiClient
{
    use HasApiProvider;
    use HasClientApiDecorator;

    public Decorator $decorator;

    /**
     * convert the mapped data to an array.
     *
     * @return array
     */
    abstract public function getMappedArray(): array;

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return Str::snake(class_basename($this));
    }

    /**
     * @param string $apiName
     * @param bool   $map
     *
     * @return Response|self
     */
    public function requestFromApi($apiName, $map = true): Response|self
    {
        $decorator = $this->makeDecorator($apiName);

        $response = $decorator->requestFromProvider();

        if (!$map) {
            return $response;
        }

        return $decorator->getMappedApiData();
    }

    /**
     * @param string $apiName
     */
    public function makeDecorator($apiName)
    {
        $clientName = $this->getClientName();
        $provider = App::make(ApiProvider::class, ['apiName' => $apiName]);

        $decorator = App::make(Decorator::class, [
            'api_name'     => $apiName,
            'client_name'  => $clientName,
            'provider'     => $provider,
            'client'       => $this,
        ]);

        return $this->decorator = $decorator;
    }

    /**
     * @return Decorator
     */
    public function getDecorator()
    {
        return $this->decorator;
    }

    /**
     * return the body of response.
     *
     * @return array
     */
    public function getApiBody(): array
    {
        return $this->getDecorator()->getApiBody();
    }
}
