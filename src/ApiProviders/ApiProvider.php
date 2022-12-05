<?php 
namespace Mtrn\ApiService\ApiProviders;
use Illuminate\Http\Client\Response;
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
     * @return array $configs
     */
    public function getConfig(): array
    {
        return $this->configs;
    }

    /**
     * @return void
     */
    // public function setApiName(): void
    // {
    //     $this->apiName = Str::snake(Str::remove('ApiProvider', class_basename($this)));
    // }

 

}