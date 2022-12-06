<?php 
namespace Mtrn\ApiService\ApiProviders;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class ApiProvider
{
    public array $configs;
    protected string $url;
    // private string $apiName;

    public function __construct()
    {
        // $this->setApiName();
        $this->setConfig();

    }
    
    /**
     * @return Response
     */
    abstract public function requestFromProvider(): Response;

    /**
    * @return void
    */
    public function setConfig(): void
    {
        $apiName = $this->getApiName();
        $configs = config('apiservice.apis.'.$apiName);
        $configs['api_name'] = $apiName;
        $this->configs = $configs;
        $this->url = $configs['url'];
    }

    /**
     * @return mixed
     */
    public function getConfig($key=null): mixed
    {
        if( $key === null )
            return $this->configs;
        
        return Arr::get($this->configs, $key);
    }

    /**
     * @return string
     */
    private function getApiName(): string
    {
        return Str::snake(Str::remove('ApiProvider', class_basename($this)));
    }

 

}