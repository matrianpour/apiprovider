<?php 
namespace Mtrn\ApiService\Services\ApiService\Decorators;

use Mtrn\ApiService\Models\Client;

class GithubClientDecorator extends Decorator
{
    /**
     * @param array $data
     * @return Client
     */
    public function mapApiData(array $data): Client
    {
        // ['forename' => 'sergey', 'surname' => 'lazarev']
        $this->client->setAttribute('name', $data['forename'].' '.$data['surname']);
        return $this->client;
    }
}