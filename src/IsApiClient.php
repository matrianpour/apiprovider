<?php 
namespace Mtrn\ApiService;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mtrn\ApiService\ApiProviders\ApiProvider;

trait IsApiClient
{
    public string $apiName;
    private array $configs;
    private array $dataBody;
    public ApiProvider $provider;

    public function __construct()
    {
        $this->setApiName();
        $this->setConfigs();
        $this->setProvider();
    }

    /**
     * map the api data
     * 
     * @param array $unmappedData
     * @return object
     */
    abstract public function mapApiData(array $unmappedData): object;

    /**
     * convert the mapped data to an array.
     * 
     * @return array
     */
    abstract public function getMappedArray(): array;

    /**
     * @return string
     */
    abstract public function getApiName(): string;

    /**
     * @return void
     */
    private function setApiName(): void
    {
        $this->apiName = $this->getApiName();
    }

    /**
     * @return  void
     */
    private function setConfigs():void
    {
        $configs = config('apiservice.apis.'.$this->getApiName());
        $configs['defaults'] = config('apiservice.defaults');

        $this->configs = $configs;
    }

    /**
     * Get the specific value of config
     * @return mixed
     */
    private function getConfig($key): mixed
    {
        return Arr::get($this->configs, $key);
    }
    
    /**
     * @return void
     */
    public function setProvider(): void
    {
        $providerName = Str::studly($this->getApiName()).'ApiProvider';
        $pathToProvider = "Mtrn\\ApiService\\ApiProviders\\".$providerName;
        $this->provider = new $pathToProvider();
    }

     /**
     * @param boolean $map
     * @return object
     */
    public function requestFromApi($map = true): object
    {
        $response = $this->requestFromProvider();
        $this->setData($response);

        if(!$map)
            return $response;

        return $this->mapApiData($this->getData());
    }


    /**
     * @return Response
     */
    private function requestFromProvider(): Response
    {
        
        $response = Http::get($this->getConfig('url'));
        
        return $response;
    }

    /**
     * @param Response $data
     * @return void
     */
    private function setData(Response $data): void
    {
        $responseType = $this->getConfig('response_type') ?? $this->getConfig('defaults.response_type');

        switch ($responseType) {
            case 'json':
                $dataBody = $this->extraxtDataFromJson($data->body());
                break;
            
            // case 'xml':
            //     $dataBody = $this->extraxtDataFromXml($data->body());
            //     break;

        }
        
        $this->dataBody = $dataBody;
    }

    /**
     * return the data of response
     * @return array
     */
    public function getData(): array
    {
        return $this->dataBody;
    }
    
    /**
     * @param string $data
     * @return array $dataArray
     */
    private function extraxtDataFromJson($data)
    {
        if(!Str::of($data)->isJson())
            throw new \Exception(
                __METHOD__.
                ': Resoonse data type is expected to be json '
               . gettype($data).' is given.'
            );

        $dataArray = json_decode($data, true);
        $dataAccessKey = $this->getConfig('data_access_key') ?? $this->getConfig('defaults.data_access_key');
        if($dataAccessKey !== '')
            $dataArray = Arr::get($dataArray, $dataAccessKey);

        return $dataArray;
    }

}