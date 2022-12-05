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
    abstract public function requestToApi(): Response;

    /**
    * @return void
    */
    public function setConfig()
    {

        $apiName = Str::snake(Str::remove('ApiProvider', class_basename($this)));
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
     * @return void
     */
    // public function setApiName(): void
    // {
    //     $this->apiName = Str::snake(Str::remove('ApiProvider', class_basename($this)));
    // }

 

}