<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('postnl-shipments')]
class PostNLUpdatedShipmentsCommand extends Command
{
    public function __construct(private readonly HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseUrl = 'https://api.postnl.nl/shipment/v2/status';
        $customerNumber = $_ENV['POSTNL_CUSTOMER_NUMBER'];
        $apikey = $_ENV['POSTNL_API_KEY'];
        $periodStart = '2023-04-07T20:00:00';
        $periodEnd = '2023-04-08T20:00:00';

        $response = $this->client->request('GET', vsprintf('%s/%s/updatedshipments?period=%s&period=%s',[
            $baseUrl,
            $customerNumber,
            $periodStart,
            $periodEnd,
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