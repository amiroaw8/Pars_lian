<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CaptureScrollPosition
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $this->shareRestoreFromCookie($request);
        }

        $response = $next($request);

        if ($request->isMethod('GET') && $request->hasCookie('pars_scroll_restore')) {
            $response->headers->setCookie(Cookie::forget('pars_scroll_restore'));
        }

        if (! $request->isMethod('POST') || ! $response->isRedirection()) {
            return $response;
        }

        $sourcePath = $this->normalizePath((string) $request->input('_scroll_path', ''));
        if ($sourcePath === '') {
            $referer = (string) $request->headers->get('referer', '');
            $sourcePath = $this->normalizePath(ltrim((string) (parse_url($referer, PHP_URL_PATH) ?? ''), '/'));
        }

        $y = max(0, (int) $request->input('_scroll_y', 0));

        if ($y < 1) {
            $y = $this->scrollYFromCookie($request, $sourcePath);
        }

        if ($y < 1) {
            return $response;
        }

        $location = $response->headers->get('Location');
        $targetPath = $this->normalizePath($this->pathFromLocation($location));

        if ($targetPath === '' || $sourcePath !== $targetPath) {
            return $response;
        }

        $scrollMode = (string) $request->input('_scroll_mode', 'main');
        if (! in_array($scrollMode, ['main', 'win'], true)) {
            $scrollMode = 'main';
        }

        $restorePayload = [
            'y' => $y,
            'path' => $targetPath,
            'mode' => $scrollMode,
            'rowId' => (string) $request->input('_scroll_row_id', ''),
            'rowRelTop' => (int) $request->input('_scroll_row_rel', 0),
            'anchorTop' => (int) $request->input('_scroll_anchor', 0),
            'formAction' => (string) $request->input('_scroll_form', ''),
        ];

        $request->session()->flash('restore_scroll_y', $y);
        $request->session()->flash('restore_scroll_path', $targetPath);
        $request->session()->flash('restore_scroll_mode', $scrollMode);
        $request->session()->flash('restore_scroll_meta', $restorePayload);

        $response->headers->setCookie(cookie(
            'pars_scroll_restore',
            json_encode($restorePayload, JSON_UNESCAPED_UNICODE),
            2,
            '/',
            null,
            false,
            false,
            false,
            'Lax'
        ));

        if ($location) {
            $response->headers->set('Location', $this->locationWithScrollRestore($location, $y));
        }

        return $response;
    }

    private function shareRestoreFromCookie(Request $request): void
    {
        $raw = $request->cookie('pars_scroll_restore');
        if (! is_string($raw) || $raw === '') {
            return;
        }

        $data = json_decode(urldecode($raw), true);
        if (! is_array($data)) {
            $data = json_decode($raw, true);
        }
        if (! is_array($data) || (int) ($data['y'] ?? 0) < 1) {
            return;
        }

        if ($this->normalizePath((string) ($data['path'] ?? '')) !== $this->normalizePath($request->path())) {
            return;
        }

        View::share('parsScrollRestore', $data);
    }

    private function pathFromLocation(?string $location): string
    {
        if ($location === null || $location === '') {
            return '';
        }

        return parse_url($location, PHP_URL_PATH) ?? '';
    }

    private function locationWithScrollRestore(string $location, int $y): string
    {
        $parts = parse_url($location);
        $query = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        unset($query['_rs']);
        $query['_rs'] = (string) $y;
        $queryString = http_build_query($query);
        $path = $parts['path'] ?? '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        if (isset($parts['host'])) {
            $scheme = $parts['scheme'] ?? 'http';
            $port = isset($parts['port']) ? ':'.$parts['port'] : '';

            return $scheme.'://'.$parts['host'].$port.$path
                .($queryString !== '' ? '?'.$queryString : '')
                .$fragment;
        }

        return $path.($queryString !== '' ? '?'.$queryString : '').$fragment;
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');

        if (preg_match('#^automation/(service-orders|repairs)/(\d+)$#', $path, $matches)) {
            return 'automation/order/'.$matches[2];
        }

        return $path;
    }

    private function scrollYFromCookie(Request $request, string $sourcePath): int
    {
        $raw = $request->cookie('pars_scroll_restore');
        if (! is_string($raw) || $raw === '') {
            return 0;
        }

        $data = json_decode(urldecode($raw), true);
        if (! is_array($data)) {
            $data = json_decode($raw, true);
        }
        if (! is_array($data)) {
            return 0;
        }

        if ($this->normalizePath((string) ($data['path'] ?? '')) !== $sourcePath) {
            return 0;
        }

        return max(0, (int) ($data['y'] ?? 0));
    }
}
