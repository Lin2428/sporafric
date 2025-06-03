<?php

namespace App\Services;

use GuzzleHttp\Client;

class OdooService
{
    protected $client;
    protected $url;
    protected $db;
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        $this->url = rtrim(config('services.odoo.url') ?? env('ODOO_URL'), '/');
        $this->db = config('services.odoo.db') ?? env('ODOO_DB');
        $this->username = config('services.odoo.username') ?? env('ODOO_USERNAME');
        $this->apiKey = config('services.odoo.api_key') ?? env('ODOO_API_KEY');
        $this->client = new Client(['base_uri' => $this->url]);
    }

    public function authenticate()
    {
        $response = $this->client->post('/jsonrpc', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'call',
                'params' => [
                    'service' => 'common',
                    'method' => 'authenticate',
                    'args' => [
                        $this->db,
                        $this->username,
                        $this->apiKey,
                        [],
                    ]
                ],
                'id' => time(),
            ]
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        return $body['result'] ?? null;
    }

    public function searchRead($model, $domain = [], $fields = [])
    {
        $uid = $this->authenticate();

        if (!$uid) {
            throw new \Exception("Authentication failed");
        }

        $response = $this->client->post('/jsonrpc', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'call',
                'params' => [
                    'service' => 'object',
                    'method' => 'execute_kw',
                    'args' => [
                        $this->db,
                        $uid,
                        $this->apiKey,
                        $model,
                        'search_read',
                        [$domain],
                        ['fields' => $fields]
                    ]
                ],
                'id' => time(),
            ]
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        return $body['result'] ?? [];
    }

    public function getCompany()
    {
        return $this->searchRead('res.partner', [], [ 'name', 'phone']);
    }
}