<?php

namespace App\Core\Scraping;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClient
{
    private array $headers = [];

    public PendingRequest $client;

    private string $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    private array $defaultHeaders = [
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029. Safari/537.3",
        "Accept-Language" => "en-US,en;q=0.5"
    ];

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function initClient(): self
    {
        $this->client = Http::withHeaders($this->defaultHeaders);
        return $this;
    }

    public function get($url, $params = [])
    {
        return $this->client->get($this->getFullUrl($url), $params);
    }

    public function post($url, $params = [])
    {
        return $this->client->post($this->getFullUrl($url), $params);
    }

    private function getFullUrl($url): string
    {
        return $this->baseUrl . $url;
    }
}
