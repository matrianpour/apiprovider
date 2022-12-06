<?php 
namespace Mtrn\ApiService\Mappers;

use Mtrn\ApiService\Client;

class GoogleClientMapper extends Mapper
{
    /**
     * @param array $data
     * @return Client
     */
    public function mapApiData(array $data)
    {
        
        //array data: ['first_name' => 'john', 'last_name' => 'doe']
        $this->client->setAttribute('name', $data['first_name'].' '.$data['last_name']);
        
        return $this->client;
    }
    
}