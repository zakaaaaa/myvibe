<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>
  <meta name="description" content="{{ $description }}">

  {{-- Open Graph (Facebook, WhatsApp, LinkedIn, Telegram) --}}
  <meta property="og:type" content="profile">
  <meta property="og:site_name" content="MyVibe">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:image" content="{{ $image }}">
  <meta property="og:image:width" content="400">
  <meta property="og:image:height" content="400">
  <meta property="og:url" content="{{ $url }}">
  <meta property="profile:username" content="{{ $username }}">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $title }}">
  <meta name="twitter:description" content="{{ $description }}">
  <meta name="twitter:image" content="{{ $image }}">

  {{-- Favicon --}}
  <link rel="icon" href="https://myvibeapp.co/favicon.ico">

  {{-- Canonical --}}
  <link rel="canonical" href="{{ $url }}">

  {{-- Auto-redirect human users to SPA setelah meta dibaca --}}
  <meta http-equiv="refresh" content="0;url={{ $url }}">

  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(155deg, #2d1b69 0%, #1a0d3d 60%, #0d0626 100%);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      text-align: center;
    }
    .card {
      padding: 32px;
      max-width: 360px;
    }
    .avatar {
      width: 80px; height: 80px;
      border-radius: 50%;
      border: 2px solid rgba(255,255,255,0.2);
      object-fit: cover;
      margin-bottom: 14px;
    }
    h1 { font-size: 22px; margin: 0 0 6px; }
    p { color: rgba(255,255,255,0.6); font-size: 14px; margin: 0 0 20px; }
    a {
      display: inline-block;
      padding: 12px 24px;
      background: rgba(108,92,231,0.85);
      color: #fff;
      text-decoration: none;
      border-radius: 14px;
      font-weight: 600;
    }
  </style>
</head>
<body>
  {{-- Fallback content jika redirect gagal (crawler tetap baca meta tags di atas) --}}
  <div class="card">
    <img src="{{ $image }}" alt="" class="avatar" onerror="this.style.display='none'">
    <h1>{{ $name }}</h1>
    <p>@{{ $username }} • {{ $totalPosts }} {{ $totalPosts === 1 ? 'vibe' : 'vibes' }}</p>
    <a href="{{ $url }}">Open in MyVibe</a>
  </div>

  <script>
    // Redirect lebih cepat untuk human user (bots tidak jalankan JS)
    (function() {
      var ua = navigator.userAgent.toLowerCase();
      var isBot = /bot|crawl|spider|facebookexternalhit|whatsapp|telegram|twitterbot|slack|discord|linkedinbot|skype/i.test(ua);
      if (!isBot) {
        window.location.replace({!! json_encode($url) !!});
      }
    })();
  </script>
</body>
</html>