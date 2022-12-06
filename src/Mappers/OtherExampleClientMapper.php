<?php 
namespace Mtrn\ApiService\Mappers;
class OtherExampleClientMapper
{
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param array $data
     * @return object
     */
    public function mapApiData(array $data): object
    {
        //an instaance of ether-api-data for user
        // ['forename' => 'sergey', 'surname' => 'lazarev']
        $this->client->setAttribute('name', $data['forename'].' '.$data['surname']);
        return $this->client;
    }
}