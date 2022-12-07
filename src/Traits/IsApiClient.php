<?php 
namespace Mtrn\ApiService\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Mtrn\ApiService\Services\ApiService\Mappers\Mapper;
use Mtrn\ApiService\Services\ApiService\ApiProviders\ApiProvider;
use Mtrn\ApiService\Services\ApiService\Decorators\Decorator;

trait IsApiClient
{
     
    use HasApiProvider;
    use HasClientApiDecorator;
    

    //excute strategy to get use of proper decorator
    public function exeStrategyToChooseDecorator($apiName)
    {
        $client = $this;
        $clientName = class_basename($client);
        $provider = App::make(ApiProvider::class, ['apiName' => $apiName]);

        $decoratorName = Str::studly($apiName).$clientName.'Decorator';
        $decorator = config('apiservice.path_to_decorators').$decoratorName;
        return $this->decorator = new $decorator($this, $provider);
    }

    /**
     * @return Decorator
     */
    public function getDecorator()
    {
        return $this->decorator;
    }



    public string $apiName;

    private Mapper $mapper;

    public Decorator $decorator;


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
     * @return string
     */
    public function getClientName(): string
    {
        return Str::snake(class_basename($this));
    }

    /**
    * @param string $apiName
    * @param boolean $map
    * @return object
    */
    public function requestFromApi($apiName, $map = true): object
    {
        $decorator = $this->exeStrategyToChooseDecorator($apiName);

        $response = $decorator->requestFromProvider();
        

        // $this->setApiName($apiName);

        // $this->setProvider($apiName);
        
        // $response = $this->getProvider()->requestFromProvider();

        if(!$map)
            return $response;

        return $decorator->getMappedApiData();
        // return $this->mapApiDataProvidedBy($apiName, $this->getProvider()->getData());
    }

    /**
     * return the body of response
     * @return array
     */
    public function getApiBody(): array
    {
        return $this->getDecorator()->getApiBody();
    }


    /**
     * @param string $apiName
     * @param array $data
     * @return object
     */
    public function mapApiDataProvidedBy(string $apiName, array $data): object
    {
        $this->setMapper($apiName);
        return $this->getMapper()->mapApiData($data);
    }

    /**
     * @param string $apiName
     */
    private function setMapper(string $apiName)
    {
        $mapper = App::make(Mapper::class, ['apiName' => $apiName, 'client' => $this]);
        $this->mapper = $mapper;
    }

    /**
     * @return Mapper
     */
    private function getMapper():Mapper
    {
        return $this->mapper;
    }
}