<?php

namespace App\Controller;

use App\Service\CacheClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AppController extends AbstractController
{

    #[Route('/', name: 'app_homepage')]
    public function homepage(CacheClient $cacheClient): Response
    {

        // http://ri.uaemex.mx/oai/request?verb=ListRecords&metadataPrefix=oai_dc
// Quick and easy 'build' method
//        $myEndpoint = \Phpoaipmh\Endpoint::build('https://demo.opencultureconsulting.com/oai_pmh');
//        $myEndpoint = \Phpoaipmh\Endpoint::build('http://ri.uaemex.mx/oai/request');
        $serviceUrl = 'http://ri.uaemex.mx/oai/request';

        $client = new \Phpoaipmh\Client($serviceUrl, $cacheClient);
        $myEndpoint = new \Phpoaipmh\Endpoint($client);

        $identity = $myEndpoint->identify();

// Results will be iterator of SimpleXMLElement objects
        $results = $myEndpoint->listMetadataFormats();
        $metadataPrefixes = [];
        foreach ($results as $item) {
            $metadataPrefix = (string)$item->metadataPrefix;
            $metadataPrefixes[] = $metadataPrefix;
        }
        $metadataPrefix = 'oai_dc';  // dublin core is a requirement
            $recs = $myEndpoint->listRecords($metadataPrefix);
            foreach ($recs as $idx => $rec) {
                $header = $rec->header;

                $detailedRecord = $myEndpoint->getRecord((string)$header->identifier, $metadataPrefix);
                if ($idx > 5) {
                    break;
                }
            }

// The iterator will continue retrieving items across multiple HTTP requests.
// You can keep running this loop through the *entire* collection you
// are harvesting.  All OAI-PMH and HTTP pagination logic is hidden neatly
// behind the iterator API.


        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
            'identity' => $identity,
            'metadataPrefixes' => $metadataPrefixes,
            'recs' => $recs
        ]);
    }
}
