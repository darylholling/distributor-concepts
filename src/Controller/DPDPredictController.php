<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/dpd-predict')]
class DPDPredictController extends AbstractController
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    #[Route('/{waybill}')]
    public function index(string $waybill)
    {
        //$response = $this->httpClient->request('GET', sprintf('https://dpdpredict.nl/?locale=nl&pknr=%s', $waybill));
        $response = $this->httpClient->request('GET', 'https://dpdpredict.nl/?locale=nl&pknr=05162063763968');

        $crawler = new Crawler($response->getContent());
        //*[contains(text(),'ABC')]
        //*[text()[contains(.,'ABC')]]


        $filtered = $crawler->filterXPath("//*[text()[contains(.,'Er is geen informatie over jouw pakket gevonden.')]]");
        echo $filtered->text(). '<br>';

        $spanElements = $crawler->filter('#TargetHTML')->filter('span');
        //->reduce(function (Crawler $node, $i) {
        //        return str_contains($node->text(),'Je pakket wordt vandaag tussen');
        //});

        foreach ($spanElements as $element) {
            echo $element->textContent;
        }

        return new Response();
    }
}

/**
 *

 */