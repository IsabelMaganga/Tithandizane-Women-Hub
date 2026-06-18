<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>404 - Page Not Found</title>
<style>
    :root {
        --indigo: #4f46e5;
        --violet: #7c3aed;
        --ink: #1e1b3a;
        --muted: #6b7280;
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: radial-gradient(circle at top, #eef1fb 0%, #e3e7f7 100%);
        color: var(--ink);
    }

    .error-card {
        position: relative;
        width: 100%;
        max-width: 440px;
        background: #fff;
        border-radius: 20px;
        padding: 48px 36px 40px;
        text-align: center;
        box-shadow: 0 20px 45px -15px rgba(30, 27, 58, 0.25);
        overflow: hidden;
    }

    .error-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--indigo), var(--violet));
    }

    .icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #ede9fe;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--violet);
    }

    h1 {
        font-size: clamp(64px, 16vw, 104px);
        line-height: 1;
        margin: 0;
        font-weight: 800;
        background: linear-gradient(135deg, var(--indigo), var(--violet));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    h2 {
        margin: 8px 0 12px;
        font-size: 20px;
        font-weight: 600;
        color: var(--ink);
    }

    p.message {
        margin: 0 0 18px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.5;
    }

    .url-chip {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 28px;
        padding: 6px 14px;
        background: #f4f4fb;
        border: 1px solid #e6e6f2;
        border-radius: 999px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12px;
        color: #534f6b;
        word-break: break-all;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        background: var(--indigo);
        color: #fff;
        text-decoration: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        transition: background .15s ease, transform .15s ease;
    }

    .btn:hover { background: #4338ca; transform: translateY(-1px); }
    .btn:focus-visible { outline: 2px solid var(--violet); outline-offset: 3px; }
</style>
</head>
<body>
    <div class="error-card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>

        <h1>404</h1>
        <h2>Page not found</h2>
        <p class="message">{{ $message ?? 'The page you are looking for does not exist.' }}</p>

        @php($requestedUrl = $url ?? request()->url())
        @if($requestedUrl)
            <div class="url-chip">{{ $requestedUrl }}</div>
        @endif

        <a href="/" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to home
        </a>
    </div>
</body>
</html>
