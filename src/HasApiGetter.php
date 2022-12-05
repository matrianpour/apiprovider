<?php 
namespace Mtrn\ApiService;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait HasApiGetter
{

    public string $apiName;
    private array $configs;

    public function __construct()
    {
        $this->setApiName();
        $this->configure();
    }

    /**
     * @return string
     */
    abstract public function getApiName(): string;

    /**
     * @return void
     */
    private function setApiName()
    {
        $this->apiName = $this->getApiName();
    }

    /**
     * @return array $config
     */
    private function configure(): array
    {
        $configs = config('apiservice.'.$this->getApiName());

        return $this->configs = $configs;

    }

     /**
     * @param boolean $mapData
     * @return Response
     */
    public function request($map = true): Response
    {
        $response = $this->requestFromApi();
        $this->setData($response);

        if(!$map)
            return $response;

    }


    /**
     * @return Response
     */
    private function requestFromApi(): Response
    {
        $response = Http::get($this->configs['url']);
        
        return $response;
    }

        /**
     * @param Response $data
     * @return array $dataBody
     */
    private function setData(Response $data):array
    {
        
        switch ($this->configs['response_type'] ?? 'json') {
            case 'json':
                $dataBody = $this->extraxtDataFromJson($data->body());
                break;
            
            // case 'xml':
            //     $dataBody = $this->extraxtDataFromXml($data->body());
            //     break;

        }
        
        $this->dataBody = $dataBody;
        return $dataBody;
    }

    /**
     * return the data of response
     * @return array
     */
    public function getData(): array
    {
        return $this->dataBody;
    }
    
    /**
     * @param string $data
     * @return array $dataArray
     */
    private function extraxtDataFromJson($data)
    {
        if(!Str::of($data)->isJson())
            throw new \Exception(
                __METHOD__.
                ': Resoonse data type is expected to be json '
               . gettype($data).' is given.'
            );

        $dataArray = json_decode($data, true);

        $dataAccessKey = $this->configs['data_access_key'] ?? 'data';
        if($dataAccessKey !== '')
            $dataArray = Arr::get($dataArray, $dataAccessKey);

        return $dataArray;
    }

}