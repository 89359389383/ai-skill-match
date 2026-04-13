<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class OgpService
{
    /**
     * OGP 情報を取得する。
     *
     * @return array{
     *     title:string,
     *     description:string,
     *     image:?string,
     *     url:string,
     *     site_name:string
     * }
     */
    public function fetch(string $inputUrl): array
    {
        $normalizedUrl = $this->normalizeAndValidateUrl($inputUrl);
        $cacheKey = 'ogp:' . sha1($normalizedUrl);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($normalizedUrl) {
            try {
                $html = $this->fetchHtml($normalizedUrl);

                if (trim($html) === '') {
                    return $this->fallbackPayload($normalizedUrl);
                }

                return $this->parseHtml($html, $normalizedUrl);
            } catch (Throwable $e) {
                Log::warning('[OgpService] OGP取得失敗', [
                    'url' => $normalizedUrl,
                    'message' => $e->getMessage(),
                ]);

                return $this->fallbackPayload($normalizedUrl);
            }
        });
    }

    protected function normalizeAndValidateUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            throw ValidationException::withMessages([
                'url' => 'URLを入力してください。',
            ]);
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages([
                'url' => 'URL形式が正しくありません。',
            ]);
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw ValidationException::withMessages([
                'url' => 'http または https のURLのみ対応しています。',
            ]);
        }

        if ($host === '') {
            throw ValidationException::withMessages([
                'url' => 'ホスト名を判別できませんでした。',
            ]);
        }

        if ($this->isBlockedHost($host)) {
            throw ValidationException::withMessages([
                'url' => 'このURLは取得できません。',
            ]);
        }

        return $this->normalizeUrl($url);
    }

    protected function isBlockedHost(string $host): bool
    {
        if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
            return true;
        }

        if (str_ends_with($host, '.local')) {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) === false;
        }

        return false;
    }

    protected function normalizeUrl(string $url): string
    {
        $parts = parse_url($url);

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host'] ?? '');
        $port = $parts['port'] ?? null;
        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        if ($path === '') {
            $path = '/';
        }

        $defaultPort = ($scheme === 'https') ? 443 : 80;
        $portPart = ($port !== null && (int) $port !== $defaultPort) ? ':' . $port : '';

        return $scheme . '://' . $host . $portPart . $path . $query;
    }

    protected function fetchHtml(string $url): string
    {
        $client = new Client([
            'timeout' => 3,
            'connect_timeout' => 3,
            'http_errors' => false,
            'allow_redirects' => [
                'max' => 5,
                'track_redirects' => true,
            ],
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; AI-Skill-Match/1.0)',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'ja,en-US;q=0.8,en;q=0.7',
            ],
            'verify' => true,
        ]);

        $response = $client->get($url);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('HTTP status error: ' . $response->getStatusCode());
        }

        return (string) $response->getBody();
    }

    protected function parseHtml(string $html, string $sourceUrl): array
    {
        $dom = new DOMDocument();
        $previousUseErrors = libxml_use_internal_errors(true);

        $converted = mb_convert_encoding(
            $html,
            'HTML-ENTITIES',
            'UTF-8, SJIS, SJIS-win, EUC-JP, ISO-8859-1, Windows-1252'
        );

        @$dom->loadHTML($converted, LIBXML_NOWARNING | LIBXML_NOERROR);

        libxml_clear_errors();
        libxml_use_internal_errors($previousUseErrors);

        $xpath = new DOMXPath($dom);

        $title = $this->firstMetaContent($xpath, ['og:title', 'twitter:title']);
        $description = $this->firstMetaContent($xpath, ['og:description', 'twitter:description', 'description']);
        $image = $this->firstMetaContent($xpath, ['og:image', 'twitter:image', 'twitter:image:src']);
        $canonical = $this->firstMetaContent($xpath, ['og:url']);
        $siteName = $this->firstMetaContent($xpath, ['og:site_name']);

        if ($title === null || trim($title) === '') {
            $title = trim((string) $xpath->evaluate('string(//title)'));
        }

        if ($description === null) {
            $description = '';
        }

        if ($canonical === null || trim($canonical) === '') {
            $canonical = $this->firstCanonicalLink($xpath) ?: $sourceUrl;
        }

        if ($siteName === null || trim($siteName) === '') {
            $siteName = $this->hostFromUrl($canonical ?: $sourceUrl);
        }

        if ($title === '') {
            $title = $siteName ?: $this->hostFromUrl($sourceUrl);
        }

        $canonical = $this->normalizeUrl($this->resolveUrl($sourceUrl, $canonical ?: $sourceUrl));

        if ($image !== null && trim($image) !== '') {
            $image = $this->resolveUrl($canonical, $image);
        } else {
            $image = null;
        }

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'url' => $canonical,
            'site_name' => $siteName,
        ];
    }

    protected function firstMetaContent(DOMXPath $xpath, array $keys): ?string
    {
        $metaNodes = $xpath->query('//meta');

        if ($metaNodes === false) {
            return null;
        }

        foreach ($metaNodes as $metaNode) {
            $name = strtolower(trim((string) $metaNode->getAttribute('name')));
            $property = strtolower(trim((string) $metaNode->getAttribute('property')));
            $content = trim((string) $metaNode->getAttribute('content'));

            if ($content === '') {
                continue;
            }

            foreach ($keys as $key) {
                $key = strtolower($key);

                if ($name === $key || $property === $key) {
                    return html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        }

        return null;
    }

    protected function firstCanonicalLink(DOMXPath $xpath): ?string
    {
        $linkNodes = $xpath->query('//link');

        if ($linkNodes === false) {
            return null;
        }

        foreach ($linkNodes as $node) {
            $rel = strtolower(trim((string) $node->getAttribute('rel')));
            $href = trim((string) $node->getAttribute('href'));

            if ($href === '') {
                continue;
            }

            if ($rel === 'canonical') {
                return html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return null;
    }

    protected function resolveUrl(string $baseUrl, string $maybeRelativeUrl): string
    {
        $maybeRelativeUrl = html_entity_decode(trim($maybeRelativeUrl), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($maybeRelativeUrl === '') {
            return $baseUrl;
        }

        if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $maybeRelativeUrl)) {
            return $maybeRelativeUrl;
        }

        if (str_starts_with($maybeRelativeUrl, '//')) {
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https';
            return $scheme . ':' . $maybeRelativeUrl;
        }

        $baseParts = parse_url($baseUrl);
        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? '';
        $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
        $root = $scheme . '://' . $host . $port;

        if (str_starts_with($maybeRelativeUrl, '/')) {
            return $root . $maybeRelativeUrl;
        }

        $basePath = $baseParts['path'] ?? '/';
        $baseDir = preg_replace('#/[^/]*$#', '/', $basePath) ?: '/';
        $joined = $baseDir . $maybeRelativeUrl;

        $segments = [];
        foreach (explode('/', $joined) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                array_pop($segments);
                continue;
            }

            $segments[] = $segment;
        }

        return $root . '/' . implode('/', $segments);
    }

    protected function hostFromUrl(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return '';
        }

        return preg_replace('/^www\./i', '', (string) $host) ?: (string) $host;
    }

    protected function fallbackPayload(string $url): array
    {
        $host = $this->hostFromUrl($url);

        return [
            'title' => $host ?: $url,
            'description' => 'リンク先のOGP情報を取得できませんでした。',
            'image' => null,
            'url' => $url,
            'site_name' => $host ?: $url,
        ];
    }
}
