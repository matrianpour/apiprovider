<?php

namespace Mtrn\ApiService;

use App\Http\Services\MapObject;
use Illuminate\Database\Eloquent\Model;
use Mtrn\ApiService\HasApiGetter;

class ExampleModel extends Model
{
    use HasApiGetter;

}