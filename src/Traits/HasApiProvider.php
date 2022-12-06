<?php 
namespace Mtrn\ApiService\Traits;

use Illuminate\Support\Facades\App;
use Mtrn\ApiService\Services\ApiService\ApiProviders\ApiProvider;

trait HasApiProvider
{
    protected ApiProvider $provider;

    /**
     * @return void
     */
    public function setProvider($apiName): void
    {
        $provider = App::make(ApiProvider::class, ['apiName' => $apiName]);
        $this->provider = $provider;
    }

    /**
     * @return ApiProvider
     */
    public function getProvider(): ApiProvider
    {
        return $this->provider;
    }


}