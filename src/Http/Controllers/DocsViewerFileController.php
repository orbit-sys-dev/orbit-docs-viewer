<?php

namespace Orbit\DevDocsViewer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DocsViewerFileController extends Controller
{
    private string $locale = 'ja';
    public function __invoke(Request $request): View
    {
        $this->locale = $this->resolveLocale($request);
        $langQuery = $this->langQuery();
        $t = $this->translations($this->locale);

        $requestedPath = $request->query('path');

        if (! is_string($requestedPath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $normalized = str_replace('\\', '/', $requestedPath);

        $rootRelative = $this->rootRelative();
        $rootPrefix = $rootRelative === '' ? '' : $rootRelative . '/';

        $docsRootReal = realpath(base_path($rootRelative));
        $projectRootReal = realpath(base_path());

        if ($docsRootReal === false || $projectRootReal === false) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if (Str::startsWith($normalized, $rootPrefix)) {
            $rootPath = $docsRootReal;
            $relativePath = ltrim(Str::after($normalized, $rootPrefix), '/');
            $displayPath = $rootPrefix . ($relativePath === '' ? '' : $relativePath);
        } elseif (Str::lower($normalized) === Str::lower(config('docs_viewer.readme_relative', 'README.md'))) {
            $rootPath = $projectRootReal;
            $relativePath = config('docs_viewer.readme_relative', 'README.md');
            $displayPath = $relativePath;
        } else {
            abort(Response::HTTP_NOT_FOUND);
        }

        $fullPath = realpath($rootPath . DIRECTORY_SEPARATOR . $relativePath);

        $normalizedRoot = rtrim(str_replace('\\', '/', $rootPath), '/') . '/';
        $normalizedFull = $fullPath !== false ? str_replace('\\', '/', $fullPath) : false;

        if ($normalizedFull === false || ! Str::startsWith(rtrim($normalizedFull, '/') . '/', $normalizedRoot)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if (! File::exists($fullPath) || ! File::isFile($fullPath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $content = File::get($fullPath);
        $sizeBytes = File::size($fullPath);
        $lastModified = File::lastModified($fullPath);

        $segments = $relativePath === '' ? [] : explode('/', $relativePath);

        $renderOptions = [
            'html_input' => 'allow',
            'allow_unsafe_links' => true,
            'max_nesting_level' => 100,
        ];

        return view('docs-viewer::dev.docs-viewer-file', [
            'fullPath' => $fullPath,
            'relativePath' => $displayPath,
            'fileName' => basename($fullPath),
            'directorySegments' => $segments,
            'content' => $content,
            'markdownHtml' => Str::markdown($this->rewriteDocsLinks($content), $renderOptions),
            'sizeBytes' => $sizeBytes,
            'lastModified' => $lastModified,
            'lang' => $this->locale,
            'langQuery' => $langQuery,
            't' => $t,
            'routeNames' => $this->routeNames(),
            'links' => $this->navigationLinks(),
        ]);
    }

    private function rewriteDocsLinks(string $markdown): string
    {
        $prefix = trim(config('docs_viewer.route.prefix', 'dev/docs'), '/');
        $rootPrefix = $this->rootPrefix();

        $pattern = sprintf('/\[(?P<label>[^\]]+)]\((?P<url>https?:\/\/localhost(?:\:\\d+)?\/%s\/[^\s)]+)\)/', preg_quote($prefix, '/'));

        $langQuery = $this->langQuery();

        return preg_replace_callback($pattern, function (array $matches) use ($prefix, $rootPrefix, $langQuery): string {
            $label = $matches['label'];
            $url = $matches['url'];

            $relativePath = Str::after($url, '/' . $prefix . '/');

            if (! Str::startsWith($relativePath, $rootPrefix)) {
                return $matches[0];
            }

            return sprintf('[%s](%s)', $label, route($this->routeName('viewer.file'), array_merge($langQuery, ['path' => $relativePath])));
        }, $markdown) ?? $markdown;
    }

    private function rootRelative(): string
    {
        $relative = trim(config('docs_viewer.root_dir', 'docs'), '/');

        return $relative === '' ? 'docs' : $relative;
    }

    private function rootPrefix(): string
    {
        $relative = $this->rootRelative();

        return $relative === '' ? '' : $relative . '/';
    }

    private function routeName(string $suffix): string
    {
        $prefix = config('docs_viewer.route.name_prefix', 'dev.docs.');
        $prefix = rtrim($prefix, '.');
        $suffix = ltrim($suffix, '.');

        return $prefix === '' ? $suffix : $prefix . '.' . $suffix;
    }

    /**
     * @return array<string, string>
     */
    private function routeNames(): array
    {
        return [
            'viewer' => $this->routeName('viewer'),
            'app' => $this->routeName('app'),
            'development' => $this->routeName('development'),
            'development.group' => $this->routeName('development.group'),
            'viewer.file' => $this->routeName('viewer.file'),
        ];
    }

    /**
     * @return array<string, array{label: string, url: string} | null>
     */
    private function navigationLinks(): array
    {
        $dashboardUrl = config('docs_viewer.links.dashboard.url', config('docs_viewer.dashboard_url'));
        $dashboardLabel = config('docs_viewer.links.dashboard.label');

        $homeUrl = config('docs_viewer.links.home.url', '/');
        $homeLabel = config('docs_viewer.links.home.label');

        return [
            'dashboard' => $this->buildLink($dashboardUrl, $dashboardLabel ?? $this->trans('nav.dashboard')),
            'home' => $this->buildLink($homeUrl, $homeLabel ?? $this->trans('nav.home')),
        ];
    }

    /**
     * @return array{label: string, url: string}|null
     */
    private function buildLink(?string $url, ?string $label): ?array
    {
        $label = $label ?? '';
        $url = $url ?? '';

        if ($label === '' || $url === '') {
            return null;
        }

        return [
            'label' => $label,
            'url' => $url,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function translations(string $locale): array
    {
        return trans('docs-viewer::messages', [], $locale);
    }

    private function trans(string $key, array $replace = [], ?string $default = null): string
    {
        $fullKey = 'docs-viewer::messages.' . $key;
        $value = trans($fullKey, $replace, $this->locale);

        if ($value === $fullKey) {
            return $default ?? $value;
        }

        return $value;
    }

    private function resolveLocale(Request $request): string
    {
        $queryKey = config('docs_viewer.locale_query_key', 'lang');
        $available = config('docs_viewer.locales', ['ja', 'en']);
        $available = array_values(array_filter(array_map(static fn ($v) => is_string($v) ? strtolower($v) : null, $available)));

        $requested = $request->query($queryKey);

        if (is_string($requested)) {
            $candidate = strtolower($requested);

            if (in_array($candidate, $available, true)) {
                return $candidate;
            }
        }

        $default = strtolower((string) config('docs_viewer.locale', 'ja'));

        if ($default !== '' && in_array($default, $available, true)) {
            return $default;
        }

        return $available[0] ?? 'ja';
    }

    private function langQuery(): array
    {
        $queryKey = config('docs_viewer.locale_query_key', 'lang');

        return [$queryKey => $this->locale];
    }
}
