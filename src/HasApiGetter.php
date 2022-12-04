<?php 
namespace Mtrn\ApiService;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait HasApiGetter
{

    public string $apiName;
    private array $configs;

    public function __construct()
    {
        $this->setApiName();
        $this->configure();
    }

    /**
     * @return string
     */
    abstract public function getApiName(): string;

    /**
     * @return void
     */
    private function setApiName()
    {
        $this->apiName = $this->getApiName();
    }

    /**
     * 
     * @param array $configs
     * @return array $config
     */
    private function configure(): array
    {
        $configs = config('apiservice.example');

        return $this->configs = $configs;

    }

     /**
     * @param boolean $mapData
     * @return Response
     */
    public function request($map = true): Response
    {
        $response = $this->requestFromApi();

        if(!$map)
            return $response;

    }


    /**
     * @return Response
     */
    private function requestFromApi(): Response
    {
        $response = Http::get($this->configs['url']);
        
        return $response;
    }

}