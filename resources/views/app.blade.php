<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#020617">
        <link id="favicon" rel="icon" href="/favicon.ico">

        @if(($page['component'] ?? null) === 'Public/Show' && ($publicUser = $page['props']['user'] ?? null))
            @php
                // Server-rendered social/SEO tags: crawlers never execute
                // the SPA, so sharing previews must not depend on Vue.
                $publicTitle = $publicUser['name'].' (@'.$publicUser['username'].')';
                $publicUrl = rtrim(request()->root(), '/').'/'.$publicUser['username'];
                $publicDescription = $publicUser['bio']
                    ?: 'Check out the links from '.$publicUser['name'].' on '.config('app.name').'.';
                $mediaPath = $publicUser['banner_path'] ?: $publicUser['profile_photo_path'];
                $ogImage = $mediaPath ? rtrim(request()->root(), '/').\Illuminate\Support\Facades\Storage::url($mediaPath) : null;
                $jsonLd = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Person',
                    'name' => $publicUser['name'],
                    'alternateName' => '@'.$publicUser['username'],
                    'url' => $publicUrl,
                ];
            @endphp
            <title inertia>{{ $publicTitle }}</title>
            <meta name="description" content="{{ $publicDescription }}">
            <link rel="canonical" href="{{ $publicUrl }}">
            <meta property="og:type" content="profile">
            <meta property="og:site_name" content="{{ config('app.name') }}">
            <meta property="og:title" content="{{ $publicTitle }}">
            <meta property="og:description" content="{{ $publicDescription }}">
            <meta property="og:url" content="{{ $publicUrl }}">
            @if($ogImage)
                <meta property="og:image" content="{{ $ogImage }}">
            @endif
            <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
            <meta name="twitter:title" content="{{ $publicTitle }}">
            <meta name="twitter:description" content="{{ $publicDescription }}">
            @if($ogImage)
                <meta name="twitter:image" content="{{ $ogImage }}">
            @endif
            <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        @else
            <title inertia>{{ config('app.name', 'LinkHub') }}</title>
        @endif

        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
