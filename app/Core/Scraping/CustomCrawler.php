<?php

namespace App\Core\Scraping;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class CustomCrawler
{
    private array $headers = [];

    public HttpBrowser|null $browser;

    private string $baseUrl;

    private string $fullUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->browser = null;
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

    public function buildHttpClient()
    {
        return HttpClient::create([
            'headers' => array_merge($this->headers, $this->defaultHeaders)
        ]);
    }

    public function initBrowser(): self
    {
        $this->browser = (new HttpBrowser($this->buildHttpClient()));
        return $this;
    }

    public function get($url, $params = []): \Symfony\Component\DomCrawler\Crawler
    {
        return $this->browser->request('GET', $this->getFullUrl($url), $params);
    }

    public function post($url, $params = []): \Symfony\Component\DomCrawler\Crawler
    {
        return $this->browser->request('POST', $this->getFullUrl($url), $params);
    }

    private function getFullUrl($url): string
    {
        return $this->baseUrl . $url;
    }
}
