<?php 
namespace Mtrn\ApiService\Services\ApiService\Decorators;

class GithubClientDecorator extends Decorator
{
    /**
     * @param array $data
     * @return object
     */
    public function mapApiData(array $data): object
    {
        // ['forename' => 'sergey', 'surname' => 'lazarev']
        $this->client->setAttribute('name', $data['forename'].' '.$data['surname']);
        return $this->client;
    }
}