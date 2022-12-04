<?php 
namespace Mtrn\ApiService;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait HasApiGetter
{

    private array $configs;

    public function __construct()
    {
        $this->configure();
    }


    /**
     * 
     * @param array $configs
     * @return array $config
     */
    private function configure(): array
    {
        $configs = config('apiservice.example');

        $this->configs = $configs;

        return $configs;
    }

     /**
     * @param boolean $mapData
     * @return Response
     */
    public function request($map = true): Response
    {
        $response = $this->requestFromApi();

        if(!$map)
            return $response;

    }


    /**
     * @return Responsea