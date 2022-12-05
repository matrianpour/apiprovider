<?php

namespace Mtrn\ApiService;

use Illuminate\Database\Eloquent\Model;
use Mtrn\ApiService\IsApiClient;

class ExampleModel extends Model
{
    use IsApiClient;

    /**
     * @return string
     */
    public function getApiName(): string
    {
        return 'example';
    }

    /**
     * map the api data
     * 
     * @param array $unmappedData
     * @return object
     */
    public function mapApiData(array $unmappedData): object
    {
        $this->setAttribute('name', $unmappedData['first_name'].' '.$unmappedData['last_name']);
        return $this;
    }

    /**
     * convert the mapped data to an array.
     * 
     * @return array
     */
    public function getMappedArray(): array
    {
        return $this->toArray();
    }

}