<?php

function et_icon(string $name, string $class = ""): string
{
    $paths = [
        "arrow-up-right" => '<path d="M7 17 17 7M7 7h10v10"/>',
        "arrow-right" => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        "arrow-left" => '<path d="M19 12H5m6-6-6 6 6 6"/>',
        "chevron-down" => '<path d="m6 9 6 6 6-6"/>',
        "search" =>
            '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/>',
        "heart-pulse" =>
            '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/><path d="M3 12h5l2-4 3 8 2-4h6"/>',
        "bus-front" =>
            '<rect x="5" y="3" width="14" height="16" rx="3"/><path d="M5 11h14M9 3v8M5 7h14M7 19v2m10-2v2M8 15h1m6 0h1"/>',
        "landmark" =>
            '<path d="m3 9 9-6 9 6H3Zm2 3v7m5-7v7m4-7v7m5-7v7M3 22h18M3 19h18"/>',
        "mail" =>
            '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/>',
        "receipt" =>
            '<path d="M5 3h14v18l-3-2-4 2-4-2-3 2V3Zm4 5h6m-6 4h6m-6 4h3"/>',
        "shield-check" =>
            '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/>',
        "user" =>
            '<circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>',
        "menu" => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        "close" => '<path d="m6 6 12 12M6 18 18 6"/>',
        "globe" =>
            '<circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/>',
        "sparkles" =>
            '<path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5L12 3Z"/>',
        "check" => '<path d="m5 12 4 4L19 6"/>',
        "info" => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v1"/>',
        "clock" => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        "file" =>
            '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 13h8m-8 4h5"/>',
        "bell" =>
            '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>',
        "upload" => '<path d="M12 16V3m-5 5 5-5 5 5M4 16v4h16v-4"/>',
        "log-out" => '<path d="M9 3H4v18h5m6-14 5 5-5 5M8 12h12"/>',
        "help" =>
            '<circle cx="12" cy="12" r="9"/><path d="M9 8a3 3 0 0 1 6 0c0 2-3 2-3 4m0 4v1"/>',
    ];
    $class = htmlspecialchars($class, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    return '<svg class="icon ' .
        $class .
        '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' .
        ($paths[$name] ?? $paths["file"]) .
        "</svg>";
}
