<?php
/*
 *  I now I will do it's with DOM and XPath
 *  Like this:
 *
 *   $dom = new DOMDocument;
 *   $dom->loadHTML($html);
 *   $images = $dom->getElementsByTagName('img');
 *
 *   but 21st century on yard ;)
 */

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Url;
use PHPHtmlParser\Dom;
use function  App\stringStartsWith;

class UrlChecker implements IUrlChecker
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Url
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url    = Url::fromString($url);
        $this->client = new Client(['base_url' => $url]);
    }

    /**
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function getBrokenImages(): array
    {
        $doc = new Dom;

        try {
            $doc->load($this->getHTML());

            return array_map(static function ($item) {
                return $item->src;
            }, array_filter($doc->find('img')->toArray(), function ($item) {
                return $this->isImageBroken($item->src);
            }));
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function getAllLinks(): array
    {
        $doc     = new Dom;
        $exclude = ['#', 'javascript:'];

        try {
            $doc->load($this->getHTML());

            return array_map(function ($item) {
                $href = $item->href;
                if (stringStartsWith($href, 'http')) {
                    return $href;
                } else {
                    return $this->url->combine($href)->__toString();
                }
            }, array_filter($doc->find('a')->toArray(), function ($item) use ($exclude) {
                return !in_array($item->href, $exclude, true);
            }));
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @return string
     */
    private function getHTML(): string
    {
        try {
            return $this->client->get($this->url)->getBody();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function isImageBroken(string $url): bool
    {
        try {
            $response = $this->client->get($url);
            if ($response->getStatusCode() === 200) {
                return false;
            }

            return true;
        } catch (\Exception $exception) {
            return true;
        }
    }
}