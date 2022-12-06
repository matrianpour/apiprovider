<?php 
namespace Mtrn\ApiService\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Mtrn\ApiService\Services\ApiService\Mappers\Mapper;

trait IsApiClient
{
     
    use HasApiProvider;


    public string $apiName;

    private Mapper $mapper;


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
    * @param string $apiName
    * @param boolean $map
    * @return object
    */
    public function requestFromApi($apiName, $map = true): object
    {
        $this->setApiName($apiName);

        $this->setProvider($apiName);
        
        $response = $this->getProvider()->requestFromProvider();

        if(!$map)
            return $response;

        return $this->mapApiDataProvidedBy($apiName, $this->getProvider()->getData());
    }

    /**
     * return the body of response
     * @return array
     */
    public function getApiBody(): array
    {
        return $this->getProvider()->getData();
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