<?php 
namespace Mtrn\ApiService;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Mtrn\ApiService\ApiProviders\ApiProvider;

trait IsApiClient
{
    public string $apiName;
    private array $configs;
    private array $dataBody;
    public ApiProvider $provider;

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
     * @return void
     */
    private function setApiName($apiName): void
    {
        $this->apiName = $apiName;
    }

    /**
     * @return string
     */
    public function getApiName(): string
    {
        return $this->apiName;
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
    public function setProvider($apiName): void
    {
        $this->setApiName($apiName);
        $this->setConfigs();
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


    /**
    * @param string $apiName
    * @param boolean $map
    * @return object
    */
    public function requestFromApi($apiName, $map = true): object
    {
        
        $this->setProvider($apiName);
        $response = $this->getProvider()->requestFromProvider();
        $this->setData($response);

        if(!$map)
            return $response;

        return $this->mapApiDataProvidedBy($apiName, $this->getData());
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

    /**
     * @param string $apiName
     * @param array $data
     * @return object
     */
    public function mapApiDataProvidedBy(string $apiName, array $data): object
    {
        //strategy pattern to choose proper mapper based on client and provider
        $clientName = class_basename($this);
        $mapperName = Str::studly($apiName).$clientName.'Mapper';
        $pathToMapper = "Mtrn\\ApiService\\Mappers\\".$mapperName;

        $mapper = new $pathToMapper($this);
        return $mapper->mapApiData($data);
    }
}