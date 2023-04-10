<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('postnl-barcode')]
class PostNLBarcodeCommand extends Command
{
    public function __construct(private readonly HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    //     BARCODE
    //    $barcode = '3SFBFX991461944';
        $barcode = '3SFBFX157998470';
        $baseUrl = 'https://api.postnl.nl/shipment/v2/status';
        $apikey = $_ENV['POSTNL_API_KEY'];

        $response = $this->client->request('GET', vsprintf('%s/barcode/%s?detail=true',[
            $baseUrl,
            $barcode
        ]), [
            'headers' => [
                'apikey' => $apikey,
                'accept' => 'application/json',
            ],
        ]);

        $data = (new JsonDecode())->decode($response->getContent(), 'json', [
            JsonDecode::ASSOCIATIVE => true,
        ]);

        return Command::SUCCESS;

    }
}