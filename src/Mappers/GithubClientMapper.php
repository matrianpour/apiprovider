<?php 
namespace Mtrn\ApiService\Mappers;

use Mtrn\ApiService\Client;

class GithubClientMapper extends Mapper
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