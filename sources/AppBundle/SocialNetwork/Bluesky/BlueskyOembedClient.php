<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork\Bluesky;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class BlueskyOembedClient
{
    public function __construct(
        #[Autowire('@http_client.bluesky_embed')]
        private HttpClientInterface $httpClient,
        #[Autowire('@cache.system')]
        private CacheItemPoolInterface $cache,
    ) {}

    public function getEmbedHtml(string $url): string
    {
        $cacheItem = $this->cache->getItem('bluesky_oembed_' . md5($url));

        if (!$cacheItem->isHit()) {
            try {
                $response = $this->httpClient->request('GET', '/oembed', [
                    'query' => ['url' => $url, 'format' => 'json'],
                ]);
                $data = $response->toArray();
                // Strip the embed.js <script> tag — we output it once in the template
                $html = preg_replace('/<script\b[^>]*embed\.bsky\.app[^>]*><\/script>/', '', $data['html'] ?? '');
            } catch (\Throwable) {
                $html = '';
            }

            $cacheItem->set($html);
            $this->cache->save($cacheItem);
        }

        return (string) $cacheItem->get();
    }
}
