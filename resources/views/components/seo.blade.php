@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'url' => null,
])

@php
    $pageTitle = $title ?? ($storeName ?? 'Fashion Mart');
    $pageDescription = $description ?? ($storeTagline ?? 'Discover curated fashion at Fashion Mart.');
    $pageUrl = $url ?? url()->current();
    $pageImage = $image ?? asset('images/placeholder-product.svg');
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ Str::limit(strip_tags($pageDescription), 160) }}">
<link rel="canonical" href="{{ $pageUrl }}">

<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($pageDescription), 200) }}">
<meta property="og:url" content="{{ $pageUrl }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:site_name" content="{{ $storeName ?? 'Fashion Mart' }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($pageDescription), 200) }}">
<meta name="twitter:image" content="{{ $pageImage }}">

@stack('meta')
