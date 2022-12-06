<?php

namespace Mtrn\ApiService;

use Illuminate\Database\Eloquent\Model;
use Mtrn\ApiService\IsApiClient;

class ExampleClient extends Model
{
    use IsApiClient;
    
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