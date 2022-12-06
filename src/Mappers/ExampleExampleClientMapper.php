<?php 
namespace Mtrn\ApiService\Mappers;

class ExampleExampleClientMapper
{
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param array $data
     * @return object
     */
    public function mapApiData(array $data): object
    {
        //an instaance of example-api-data for user
        // ['first_name' => 'john', 'last_name' => 'doe']
        $this->client->setAttribute('name', $data['first_name'].' '.$data['last_name']);
        return $this->client;
    }
}