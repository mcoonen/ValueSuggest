<?php
namespace ValueSuggest\Suggester\Pactols;

use ValueSuggest\Suggester\SuggesterInterface;
use Zend\Http\Client;

class PactolsAll implements SuggesterInterface
{
    /**
     * @var Clientx
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve suggestions from the Opentheso 
     *
     *
     * @param string $query
     * @return array
     */
    public function getSuggestions($query, $lang = null)
    {			
        $response = $this->client
	->setUri('https://pactols.frantiq.fr/opentheso/api/search')
        ->setParameterGet(['q' => $query, 'lang' =>'fr', 'theso' => 'TH_1', 'format' => 'jsonld'])
        ->send();
		
        if (!$response->isSuccess()) {
			file_put_contents($file,"return null",FILE_APPEND | LOCK_EX);
            return [];
        }
		
        // Parse the JSON response.
        $suggestions = [];
        $results = json_decode($response->getBody(),true);
		
        for($i=0;$i<sizeof($results);$i++) {
            $valueLang="";
            for($j=0; $j<sizeof($results[$i]["http://www.w3.org/2004/02/skos/core#prefLabel"]); $j++){


                    if(strcasecmp(trim($results[$i]["http://www.w3.org/2004/02/skos/core#prefLabel"][$j]['@language']),'fr')==0){
                            $valueLang=$results[$i]["http://www.w3.org/2004/02/skos/core#prefLabel"][$j]['@value'];

                            $suggestions[] = [
                                    'value' =>$valueLang,
                                    'data' => [
                                            'uri' => sprintf('%s', $results[$i]['@id']),
                                            'info' =>sprintf('%s', $results[$i]['@type'][0]),
                                    ],
                            ];

                    }
            }
	}
           return $suggestions;
    }
}


