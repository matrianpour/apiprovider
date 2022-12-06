<?php 
namespace Mtrn\ApiService\Mappers;

use Mtrn\ApiService\IsApiClient AS client;

abstract class Mapper
{
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct( $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $data
     * @return Client
     */
    abstract public function mapApiData(array $data);
    
}