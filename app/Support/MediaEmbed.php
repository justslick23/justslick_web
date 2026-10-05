<?php

namespace App\Support;

class MediaEmbed
{
    public static function resolve(?string $url): ?array
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);

        if (
            $parts === false
            || strtolower($parts['scheme'] ?? '') !== 'https'
            || isset($parts['user'])
            || isset($parts['pass'])
            || (isset($parts['port']) && $parts['port'] !== 443)
        ) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        // YouTube: watch, share, Shorts, live and embed URLs.
        if (in_array($host, [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtu.be',
        ], true)) {
            $id = null;

            if ($host === 'youtu.be') {
                $id = $path;
            } elseif ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } elseif (preg_match(
                '#^(?:embed|shorts|live)/([A-Za-z0-9_-]{11})$#',
                $path,
                $matches
            )) {
                $id = $matches[1];
            }

            if (! is_string($id)
                || ! preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
                return null;
            }

            return [
                'provider' => 'YouTube',
                'src' => 'https://www.youtube.com/embed/'.$id.'?autoplay=0',
                'video' => true,
                'height' => null,
            ];
        }

        // Spotify: individual tracks, albums, playlists, shows and episodes.
        if ($host === 'open.spotify.com') {
            $path = preg_replace('#^intl-[a-zA-Z-]+/#', '', $path);
            $path = preg_replace('#^embed/#', '', $path);

            if (! preg_match(
                '#^(track|album|playlist|episode|show)/([A-Za-z0-9]{22})$#',
                $path,
                $matches
            )) {
                return null;
            }

            return [
                'provider' => 'Spotify',
                'src' => 'https://open.spotify.com/embed/'
                    .$matches[1].'/'.$matches[2],
                'video' => false,
                'height' => 352,
            ];
        }

        // SoundCloud: track and playlist permalinks.
        if (in_array($host, [
            'soundcloud.com',
            'www.soundcloud.com',
        ], true)) {
            if (! preg_match(
                '#^[A-Za-z0-9_-]+/(?:sets/)?[A-Za-z0-9_-]+(?:/s-[A-Za-z0-9]+)?$#',
                $path
            )) {
                return null;
            }

            $query = http_build_query([
                'url' => $url,
                'auto_play' => 'false',
                'hide_related' => 'true',
                'show_comments' => 'false',
                'visual' => 'false',
            ], '', '&', PHP_QUERY_RFC3986);

            return [
                'provider' => 'SoundCloud',
                'src' => 'https://w.soundcloud.com/player/?'.$query,
                'video' => false,
                'height' => 300,
            ];
        }

        // Mixcloud: individual show permalinks.
        if (in_array($host, [
            'mixcloud.com',
            'www.mixcloud.com',
        ], true)) {
            if (! preg_match(
                '#^[A-Za-z0-9_-]+/[A-Za-z0-9_-]+$#',
                $path
            )) {
                return null;
            }

            $query = http_build_query([
                'feed' => '/'.$path.'/',
                'hide_cover' => '0',
                'mini' => '0',
                'autoplay' => '0',
            ], '', '&', PHP_QUERY_RFC3986);

            return [
                'provider' => 'Mixcloud',
                'src' => 'https://www.mixcloud.com/widget/iframe/?'.$query,
                'video' => false,
                'height' => 180,
            ];
        }

        return null;
    }
}