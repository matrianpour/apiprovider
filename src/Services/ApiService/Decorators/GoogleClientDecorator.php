<?php

namespace Mtrn\ApiService\Services\ApiService\Decorators;

class GoogleClientDecorator extends Decorator
{
    /**
     * @param array $data
     *
     * @return object
     */
    public function mapApiData(array $data): object
    {
        //array data: ['first_name' => 'john', 'last_name' => 'doe']
        $this->client->setAttribute('name', $data['first_name'].' '.$data['last_name']);

        return $this->client;
    }
}
