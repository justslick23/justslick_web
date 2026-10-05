@if (config('site.sample_content'))
    <span {{ $attributes->merge(['class' => 'sample-badge']) }}>{{ $slot->isEmpty() ? 'Sample content' : $slot }}</span>
@endif