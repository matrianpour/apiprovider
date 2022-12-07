<?php 
namespace Mtrn\ApiService\Services\ApiService\Decorators;

use Mtrn\ApiService\Models\Client;

class GoogleClientDecorator extends Decorator
{
    /**
     * @param array $data
     * @return Client
     */
    public function mapApiData(array $data): Client
    {
        //array data: ['first_name' => 'john', 'last_name' => 'doe']
        $this->client->setAttribute('name', $data['first_name'].' '.$data['last_name']);
        
        return $this->client;
    }
    
}