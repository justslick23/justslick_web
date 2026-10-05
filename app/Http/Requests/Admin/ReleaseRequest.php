<?php

namespace App\Http\Requests\Admin;

use App\Enums\ReleaseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReleaseRequest extends FormRequest
{
    private const EMBED_HOSTS = [
        'youtube.com',
        'm.youtube.com',
        'youtu.be',
        'soundcloud.com',
        'open.spotify.com',
        'mixcloud.com',
    ];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::enum(ReleaseType::class)],
            'release_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'credits' => ['nullable', 'string', 'max:2000'],
            'embed_url' => [
                'nullable',
                'url:https',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $host = preg_replace('/^www\./', '', Str::lower((string) parse_url($value, PHP_URL_HOST)));

                    if (! in_array($host, self::EMBED_HOSTS, true)) {
                        $fail('Use a link from YouTube, SoundCloud, Spotify or Mixcloud.');
                    }
                },
            ],
            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=600,min_height=600,max_width=6000,max_height=6000',
            ],

            'audio' => [
    'nullable',
    'file',
    'mimes:mp3,wav,m4a,aac,ogg',
    'max:51200',
],

'remove_audio' => [
    'nullable',
    'boolean',
],
            'remove_cover' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'links' => ['nullable', 'array', 'max:10'],
            'links.*.label' => ['nullable', 'string', 'max:40', 'required_with:links.*.url'],
            'links.*.url' => ['nullable', 'url:http,https', 'max:500', 'required_with:links.*.label'],
        ];
    }

    public function attributes(): array
    {
        return [
            'links.*.label' => 'platform name',
            'links.*.url' => 'link',
            'embed_url' => 'embed link',
        ];
    }

    /** Scalar fields for the releases table. */
    public function releaseData(): array
    {
        $data = $this->validated();
        $published = $this->boolean('is_published');

        return [
            'title' => $data['title'],
            'type' => $data['type'],
            'release_date' => $data['release_date'] ?? null,
            'description' => $data['description'] ?? null,
            'credits' => $data['credits'] ?? null,
            'embed_url' => $data['embed_url'] ?? null,
            'is_published' => $published,
            // Only a published release can be featured.
            'is_featured' => $published && $this->boolean('is_featured'),
        ];
    }

    /** Complete link rows only; blank rows are ignored. */
    public function linkRows(): array
    {
        return collect($this->validated()['links'] ?? [])
            ->map(fn ($row) => [
                'label' => trim((string) ($row['label'] ?? '')),
                'url' => trim((string) ($row['url'] ?? '')),
            ])
            ->filter(fn ($row) => $row['label'] !== '' && $row['url'] !== '')
            ->values()
            ->all();
    }
}