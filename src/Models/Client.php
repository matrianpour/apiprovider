<?php

namespace Mtrn\ApiService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mtrn\ApiService\Traits\IsApiClient ;

class Client extends Model
{
    use IsApiClient;
    
    /**
     * By default the class_basename would consider as client_name of api
     * If your class has a different name you should provide it here.
     * @return string
     */
    public function getClientName(): string
    {
        return Str::snake(class_basename($this));
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