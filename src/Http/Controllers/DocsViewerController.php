<?php

namespace Orbit\DevDocsViewer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DocsViewerController extends Controller
{
    private const APP_SUBGROUP_TEMPLATES = [
        'services' => [
            'label' => 'Services',
            'directory' => 'Services',
            'readme' => 'README.md',
            'pinned_files' => ['SshConfigService.md'],
        ],
        'commands' => [
            'label' => 'Commands',
            'directory' => 'Commands',
            'readme' => 'README.md',
            'pinned_files' => ['orbit_codex_exec.md'],
        ],
    ];

    /**
     * Docs ビューアートップ
     */
    public function __invoke(): View
    {
        $rootRelative = $this->rootRelative();
        $rootPrefix = $this->rootPrefix();
        $docsRoot = base_path($rootRelative);
        $normalizedRoot = str_replace('\\', '/', $docsRoot . DIRECTORY_SEPARATOR);
        $appDocsConfig = $this->getAppDocsConfig();

        $defaultCategories = [
            'app' => '設計書',
            'dashboards' => 'Dashboards',
            'workflow' => 'Workflow',
            'guide' => 'Guide',
        ];
        $developmentLabel = 'Development';

        $existingDirectories = collect(File::isDirectory($docsRoot) ? File::directories($docsRoot) : [])
            ->map(static fn (string $path) => basename($path))
            ->filter()
            ->values();

        $orderedCategories = collect($defaultCategories)
            ->mapWithKeys(static fn (string $label, string $key) => [$key => $label])
            ->toArray();

        $additionalCategories = $existingDirectories
            ->reject(static fn (string $directory) => array_key_exists($directory, $orderedCategories) || $directory === 'development')
            ->sort()
            ->mapWithKeys(static function (string $directory) {
                $label = Str::headline(str_replace(['_', '-'], ' ', $directory));

                return [$directory => $label];
            })
            ->toArray();

        $orderedCategories = array_merge($orderedCategories, $additionalCategories, [
            'development' => $developmentLabel,
        ]);

        $groups = collect($orderedCategories)
            ->mapWithKeys(function (string $label, string $category) use ($docsRoot, $normalizedRoot, $appDocsConfig, $rootPrefix) {
                if ($category === 'app') {
                    if (! $appDocsConfig['exists']) {
                        return [
                            'app' => [
                                'label' => $label,
                                'files' => [],
                                'groups' => null,
                            ],
                        ];
                    }

                    $normalizedAppRoot = str_replace('\\', '/', $appDocsConfig['full_path'] . DIRECTORY_SEPARATOR);

                    $files = collect(File::allFiles($appDocsConfig['full_path']))
                        ->map(static function ($file) use ($normalizedAppRoot, $appDocsConfig) {
                            $relative = Str::after(str_replace('\\', '/', $file->getPathname()), $normalizedAppRoot);

                            return $appDocsConfig['relative_prefix'] . ltrim($relative, '/');
                        })
                        ->sort()
                        ->values()
                        ->all();

                    return [
                        'app' => [
                            'label' => $label,
                            'files' => $files,
                            'groups' => $this->buildSubGroups($category, $files, $appDocsConfig, $rootPrefix),
                        ],
                    ];
                }

                $categoryPath = $docsRoot . DIRECTORY_SEPARATOR . $category;

                if (! File::isDirectory($categoryPath)) {
                    return [
                        $category => [
                            'label' => $label,
                            'files' => [],
                            'groups' => null,
                        ],
                    ];
                }

                $files = collect(File::allFiles($categoryPath))
                    ->map(static function ($file) use ($normalizedRoot, $rootPrefix) {
                        $relative = Str::after(str_replace('\\', '/', $file->getPathname()), $normalizedRoot);

                        return $rootPrefix . ltrim($relative, '/');
                    })
                    ->sort()
                    ->values()
                    ->all();

                return [
                    $category => [
                        'label' => $label,
                        'files' => $files,
                        'groups' => $this->buildSubGroups($category, $files, $appDocsConfig, $rootPrefix),
                    ],
                ];
            })
            ->all();

        $developmentGroup = $groups['development'] ?? null;
        $appGroup = $groups['app'] ?? null;

        $totalCount = collect($groups)
            ->sum(static fn (array $group) => count($group['files']));

        if ($developmentGroup !== null) {
            unset($groups['development']);
        }

        if ($appGroup !== null) {
            unset($groups['app']);
        }

        return view('docs-viewer::dev.docs-viewer', [
            'groups' => $groups,
            'totalCount' => $totalCount,
            'developmentGroup' => $developmentGroup,
            'appGroup' => $appGroup,
            'routeNames' => $this->routeNames(),
            'links' => $this->navigationLinks(),
            'rootPrefix' => $rootPrefix,
            'readmePath' => config('docs_viewer.readme_relative', 'README.md'),
        ]);
    }

    public function developmentGroup(string $group): View
    {
        $groupKey = strtolower($group);

        $developmentGroups = $this->developmentGroups();

        if (! array_key_exists($groupKey, $developmentGroups)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $groupConfig = $developmentGroups[$groupKey];
        $docsRoot = base_path($groupConfig['path']);

        if (! File::isDirectory($docsRoot)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $files = collect(File::allFiles($docsRoot))
            ->map(function ($file) use ($groupConfig) {
                $relative = str_replace('\\', '/', $file->getPathname());
                $relative = Str::after($relative, str_replace('\\', '/', base_path()) . '/');

                return [
                    'path' => $relative,
                    'name' => $file->getFilename(),
                    'updated_at' => Carbon::createFromTimestamp($file->getMTime())
                        ->timezone($this->timezone())
                        ->format('Y-m-d H:i:s'),
                ];
            })
            ->sortBy('path')
            ->values()
            ->all();

        return view('docs-viewer::dev.docs-development-group', [
            'groupKey' => $groupKey,
            'groupLabel' => $groupConfig['label'],
            'files' => $files,
            'routeNames' => $this->routeNames(),
            'links' => $this->navigationLinks(),
            'rootPrefix' => $this->rootPrefix(),
        ]);
    }

    /**
     * @param  string  $category
     * @param  array<int, string>  $files
     * @param  array{mode: string, relative_prefix: string, full_path: string, exists: bool, root_relative: string}  $appDocsConfig
     * @param  string  $rootPrefix
     * @return array<string, array<string, mixed>>|null
     */
    private function buildSubGroups(string $category, array $files, array $appDocsConfig, string $rootPrefix): ?array
    {
        if ($category === 'app') {
            return $this->buildAppGroups($files, $appDocsConfig);
        }

        if ($category !== 'development') {
            return null;
        }

        $targets = $this->developmentGroups();

        $subGroups = [];

        foreach ($targets as $folder => $config) {
            $prefix = $rootPrefix . 'development/' . $folder . '/';
            $items = collect($files)
                ->filter(static fn (string $path) => Str::startsWith($path, $prefix))
                ->values()
                ->all();

            $subGroups[$folder] = [
                'label' => $config['label'],
                'files' => $items,
                'route' => route($this->routeName('development.group'), ['group' => $folder]),
            ];
        }

        return $subGroups;
    }

    /**
     * @param  array<int, string>  $files
     * @param  array{mode: string, relative_prefix: string, full_path: string, exists: bool, root_relative: string}  $appDocsConfig
     * @return array<string, array<string, mixed>>|null
     */
    private function buildAppGroups(array $files, array $appDocsConfig): ?array
    {
        $subGroups = [];

        foreach (self::APP_SUBGROUP_TEMPLATES as $key => $config) {
            $prefix = $appDocsConfig['relative_prefix'] . $config['directory'] . '/';

            $items = collect($files)
                ->filter(static fn (string $path) => Str::startsWith($path, $prefix))
                ->values()
                ->all();

            if ($items === []) {
                continue;
            }

            $pinnedFiles = $config['pinned_files'] ?? [];
            $routePath = isset($config['readme']) ? $prefix . $config['readme'] : null;

            $route = $routePath !== null
                ? route($this->routeName('viewer.file'), ['path' => $routePath])
                : null;

            $subGroups[$key] = [
                'label' => $config['label'],
                'files' => $items,
                'preview' => $this->buildPreviewFiles($items, array_map(
                    static fn (string $path) => $prefix . $path,
                    $pinnedFiles
                )),
                'route' => $route,
            ];
        }

        return $subGroups !== [] ? $subGroups : null;
    }

    /**
     * App ページ用のサブグループ情報を組み立てる.
     *
     * @param  array<int, string>  $files
     * @param  array{mode: string, relative_prefix: string, full_path: string, exists: bool, root_relative: string}  $appDocsConfig
     * @return array<string, array<string, mixed>>
     */
    private function buildAppPageGroups(array $files, array $appDocsConfig): array
    {
        $subGroups = $this->buildAppGroups($files, $appDocsConfig) ?? [];

        $usedFiles = collect($subGroups)
            ->flatMap(static fn (array $group) => $group['files'] ?? [])
            ->values()
            ->all();

        $others = array_values(array_diff($files, $usedFiles));

        if ($others !== []) {
            $subGroups['others'] = [
                'label' => 'Others',
                'files' => $others,
                'preview' => $this->buildPreviewFiles($others),
                'route' => null,
            ];
        }

        return $subGroups;
    }

    public function app(): View
    {
        $appDocsConfig = $this->getAppDocsConfig();

        if (! $appDocsConfig['exists']) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $normalizedRoot = str_replace('\\', '/', $appDocsConfig['full_path'] . DIRECTORY_SEPARATOR);

        $files = collect(File::allFiles($appDocsConfig['full_path']))
            ->map(static function ($file) use ($normalizedRoot, $appDocsConfig) {
                $relative = Str::after(str_replace('\\', '/', $file->getPathname()), $normalizedRoot);

                return $appDocsConfig['relative_prefix'] . ltrim($relative, '/');
            })
            ->sort()
            ->values()
            ->all();

        $subGroups = $this->buildAppPageGroups($files, $appDocsConfig);

        $totalCount = collect($subGroups)
            ->sum(static fn (array $group) => count($group['files']));

        return view('docs-viewer::dev.docs-app', [
            'subGroups' => $subGroups,
            'totalCount' => $totalCount,
            'routeNames' => $this->routeNames(),
            'links' => $this->navigationLinks(),
            'rootPrefix' => $this->rootPrefix(),
            'readmePath' => config('docs_viewer.readme_relative', 'README.md'),
        ]);
    }

    public function development(): View
    {
        $developmentGroups = $this->developmentGroups();
        $rootPrefix = $this->rootPrefix();

        $subGroups = [];

        foreach ($developmentGroups as $folder => $config) {
            $docsRoot = base_path($config['path']);
            $normalizedRoot = str_replace('\\', '/', $docsRoot . DIRECTORY_SEPARATOR);

            if (! File::isDirectory($docsRoot)) {
                $subGroups[$folder] = [
                    'label' => $config['label'],
                    'files' => [],
                    'preview' => ['files' => [], 'extra_count' => 0],
                    'route' => route($this->routeName('development.group'), ['group' => $folder]),
                ];

                continue;
            }

            $files = collect(File::allFiles($docsRoot))
                ->map(static function ($file) use ($normalizedRoot, $rootPrefix, $folder) {
                    $relative = Str::after(str_replace('\\', '/', $file->getPathname()), $normalizedRoot);

                    return $rootPrefix . 'development/' . $folder . '/' . ltrim($relative, '/');
                })
                ->sort()
                ->values()
                ->all();

            $subGroups[$folder] = [
                'label' => $config['label'],
                'files' => $files,
                'preview' => $this->buildPreviewFiles($files),
                'route' => route($this->routeName('development.group'), ['group' => $folder]),
            ];
        }

        $totalCount = collect($subGroups)
            ->sum(static fn (array $group) => count($group['files']));

        return view('docs-viewer::dev.docs-development', [
            'subGroups' => $subGroups,
            'totalCount' => $totalCount,
            'routeNames' => $this->routeNames(),
            'links' => $this->navigationLinks(),
            'rootPrefix' => $this->rootPrefix(),
        ]);
    }

    /**
     * @param  array<int, string>  $files
     * @param  array<int, string>  $pinned
     * @param  int|null  $limit
     * @return array{files: array<int, string>, extra_count: int}
     */
    private function buildPreviewFiles(array $files, array $pinned = [], ?int $limit = null): array
    {
        if ($files === []) {
            return [
                'files' => [],
                'extra_count' => 0,
            ];
        }

        $limit ??= count($files);

        $ordered = [];

        foreach ($pinned as $target) {
            if (in_array($target, $files, true) && ! in_array($target, $ordered, true)) {
                $ordered[] = $target;
            }
        }

        foreach ($files as $file) {
            if (count($ordered) >= $limit) {
                break;
            }

            if (in_array($file, $ordered, true)) {
                continue;
            }

            $ordered[] = $file;
        }

        $ordered = array_slice($ordered, 0, $limit);

        return [
            'files' => $ordered,
            'extra_count' => max(count($files) - count($ordered), 0),
        ];
    }

    /**
     * @return array{mode: string, relative_prefix: string, full_path: string, exists: bool, root_relative: string}
     */
    private function getAppDocsConfig(): array
    {
        $mode = config('docs_viewer.mode', 'laravel');
        $rootRelative = $this->rootRelative();
        $relative = $this->normalizePathWithinRoot(
            (string) config('docs_viewer.app_docs_path', $rootRelative . '/app'),
            $rootRelative,
            $rootRelative . '/app'
        );

        $fullPath = base_path($relative);

        return [
            'mode' => $mode,
            'relative_prefix' => $relative . '/',
            'full_path' => $fullPath,
            'exists' => File::isDirectory($fullPath),
            'root_relative' => $rootRelative,
        ];
    }

    /**
     * @return array<string, array{label: string, path: string}>
     */
    private function developmentGroups(): array
    {
        $root = $this->rootRelative();
        $configured = config('docs_viewer.development_groups', []);

        $groups = [];

        foreach ($configured as $key => $config) {
            $label = $config['label'] ?? Str::headline($key);
            $path = $this->normalizePathWithinRoot(
                $config['path'] ?? '',
                $root,
                $root . '/development/' . $key
            );

            $groups[$key] = [
                'label' => $label,
                'path' => $path,
            ];
        }

        return $groups;
    }

    private function timezone(): string
    {
        return config('docs_viewer.timezone') ?? config('app.timezone') ?? 'UTC';
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

    private function normalizePathWithinRoot(string $path, string $root, string $default): string
    {
        $root = trim($root, '/');
        $path = trim($path, '/');
        $default = trim($default, '/');

        if ($path === '') {
            return $default;
        }

        if ($root === '') {
            return $path;
        }

        if (Str::startsWith($path, $root . '/')) {
            return $path;
        }

        if (Str::startsWith($path, 'docs/')) {
            return $root . '/' . Str::after($path, 'docs/');
        }

        return $root . '/' . $path;
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
        $dashboardLabel = config('docs_viewer.links.dashboard.label', '開発ダッシュボードに戻る');

        $homeUrl = config('docs_viewer.links.home.url', '/');
        $homeLabel = config('docs_viewer.links.home.label', 'トップページへ');

        $links = [
            'dashboard' => $this->buildLink($dashboardUrl, $dashboardLabel),
            'home' => $this->buildLink($homeUrl, $homeLabel),
        ];

        return $links;
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
}
