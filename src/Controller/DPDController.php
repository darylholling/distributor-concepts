<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/dpd')]
class DPDController extends AbstractController
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    #[Route('/')]
    public function index()
    {
        $response = $this->httpClient->request('GET', 'https://www.dpdgroup.com/nl/mydpd/my-parcels/incoming/?parcelNumber=05162063759766');

        $crawler = new Crawler($response->getContent());
        //$data = $crawler->filterXPath("//meta[@name='_csrf']")->extract('content');
        $data = $crawler->filterXpath("//meta[@name='_csrf']")->extract(['content']);

        if ([] === $data) {
            throw new \RunTimeException('no csrf found');
        }

        $csrfToken = $data[0];

        // Search
        $searchResponse = $this->httpClient->request('POST', 'https://www.dpdgroup.com/nl/mydpd/my-parcels/search', [
            'body' => [
                '_csrf' => $csrfToken,
                'value' => '05162063759766'
            ],
        ]);

        $response = $this->httpClient->request('GET', 'https://www.dpdgroup.com/nl/mydpd/my-parcels/incoming?parcelNumber=05162063759766');

        // Protection
        $protectionResponse = $this->httpClient->request('POST', 'https://www.dpdgroup.com/nl/mydpd/my-parcels/details/protection', [
            'body' => [
                '_csrf' => $csrfToken,
                'parcelType' => 'INCOMING',
                'verificationCode' => '05162063759766',
                'recaptchaResponse' => '05162063759766',
                'shipmentType' => '05162063759766',
                'number' => '05162063759766',
                'validate' => 'Bevestigen',
            ],
        ]);

        $response = $this->httpClient->request('GET', 'https://www.dpdgroup.com/nl/mydpd/my-parcels/incoming?parcelNumber=05162063759766');

        return new Response();
    }
}