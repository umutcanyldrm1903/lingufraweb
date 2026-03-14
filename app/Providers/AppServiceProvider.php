<?php

namespace App\Providers;

use App\Enums\ThemeList;
use App\Support\SettingBag;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Modules\GlobalSetting\app\Models\MarketingSetting;
use Modules\GlobalSetting\app\Models\SeoSetting;
use Modules\GlobalSetting\app\Models\Setting;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->ensureModelLoaded('App\\Models\\StudentPlan', app_path('Models/StudentPlan.php'));
        $this->ensureModelLoaded('App\\Models\\UserPlan', app_path('Models/UserPlan.php'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        if (app()->runningUnitTests()) {
            $setting = $this->makeSettingBag();
            $marketing_setting = (object) [];
            $seo_setting = $this->makeSeoSettingArray();
        } else {
            try {
                /** Cache settings */
                $setting = $this->makeSettingBag(
                    Cache::rememberForever('setting', fn() => Setting::pluck('value', 'key')->all())
                );
                $marketing_setting = Cache::rememberForever('marketing_setting', fn() => (object) MarketingSetting::pluck('value', 'key')->all());
                $seo_setting = $this->makeSeoSettingArray(
                    Cache::rememberForever('seo_setting', fn() => SeoSetting::all()->toArray())
                );

                set_wasabi_config();
                set_aws_config();
            } catch (\Throwable $th) {
                info($th);
                $setting = $this->makeSettingBag();
                $marketing_setting = (object) [];
                $seo_setting = $this->makeSeoSettingArray();
            }
        }

        // Several blade templates access these values via Cache::get(...).
        Cache::forever('setting', $setting);
        Cache::forever('marketing_setting', $marketing_setting);
        Cache::forever('seo_setting', $seo_setting);

        /** Share settings to all views */
        View::composer('*', function ($view) use ($setting, $marketing_setting, $seo_setting) {
            $view->with(['setting' => $setting, 'marketing_setting' => $marketing_setting, 'seo_setting' => $seo_setting]);
        });

        // set timezone
        date_default_timezone_set($setting->timezone ?? config('app.timezone'));

        /** Register custom blade directives */
        $this->registerBladeDirectives();

        // Use Bootstrap 4 pagination
        Paginator::useBootstrapFour();

        // Avoid redefining constant during repeated application boots in tests.
        if (!defined('DEFAULT_HOMEPAGE')) {
            define('DEFAULT_HOMEPAGE', $setting?->site_theme ?? ThemeList::MAIN->value);
        }
    }

    private function makeSettingBag(mixed $attributes = []): object
    {
        if ($attributes instanceof SettingBag) {
            $attributes = get_object_vars($attributes);
        } elseif ($attributes instanceof \stdClass) {
            $attributes = (array) $attributes;
        } elseif (!is_array($attributes)) {
            $attributes = [];
        }

        $defaults = [
            'timezone' => config('app.timezone'),
            'site_theme' => ThemeList::MAIN->value,
            'app_name' => config('app.name', 'LinguFranca'),
            'recaptcha_status' => 'inactive',
            'recaptcha_site_key' => null,
            'recaptcha_secret_key' => null,
        ];

        return new SettingBag(array_merge($defaults, $attributes));
    }

    private function makeSeoSettingArray(mixed $attributes = []): array
    {
        $defaults = [
            'home_page' => [
                'seo_title' => config('app.name', 'LinguFranca'),
                'seo_description' => config('app.name', 'LinguFranca'),
            ],
            'about_page' => ['seo_title' => '', 'seo_description' => ''],
            'course_page' => ['seo_title' => '', 'seo_description' => ''],
            'blog_page' => ['seo_title' => '', 'seo_description' => ''],
            'contact_page' => ['seo_title' => '', 'seo_description' => ''],
        ];

        if (is_object($attributes) && method_exists($attributes, 'toArray')) {
            $attributes = $attributes->toArray();
        } elseif ($attributes instanceof \stdClass) {
            $attributes = (array) $attributes;
        }

        if (!is_array($attributes) || $attributes === []) {
            return $defaults;
        }

        $normalized = [];

        foreach ($attributes as $key => $item) {
            $row = $this->normalizeSeoRow($item, is_string($key) ? $key : null);

            if ($row === null) {
                continue;
            }

            $normalized[$row['page_name']] = [
                'seo_title' => $row['seo_title'],
                'seo_description' => $row['seo_description'],
            ];
        }

        return array_replace_recursive($defaults, $normalized);
    }

    private function normalizeSeoRow(mixed $row, ?string $fallbackPageName = null): ?array
    {
        if (is_object($row) && method_exists($row, 'toArray')) {
            $row = $row->toArray();
        } elseif ($row instanceof \stdClass) {
            $row = (array) $row;
        }

        if (!is_array($row)) {
            return null;
        }

        $pageName = isset($row['page_name']) && is_string($row['page_name'])
            ? $row['page_name']
            : $fallbackPageName;

        if (!is_string($pageName) || $pageName === '') {
            return null;
        }

        return [
            'page_name' => Str::of($pageName)->trim()->toString(),
            'seo_title' => (string) ($row['seo_title'] ?? ''),
            'seo_description' => (string) ($row['seo_description'] ?? ''),
        ];
    }

    protected function registerBladeDirectives() {
        Blade::directive('adminCan', function ($permission) {
            return "<?php if(auth()->guard('admin')->user()->can({$permission})): ?>";
        });

        Blade::directive('endadminCan', function () {
            return '<?php endif; ?>';
        });

        // Blade directive for checking the current theme
        Blade::directive('theme', function ($themes) {
            return "<?php if(in_array(DEFAULT_HOMEPAGE, {$themes})): ?>";
        });

        Blade::directive('endtheme', function () {
            return '<?php endif; ?>';
        });
    }

    private function ensureModelLoaded(string $class, string $expectedPath): void
    {
        if (class_exists($class, false)) {
            return;
        }

        if (!is_file($expectedPath)) {
            $modelsDir = dirname($expectedPath);
            if (is_dir($modelsDir)) {
                foreach (glob($modelsDir . '/*.php') as $candidate) {
                    if (strcasecmp(basename($candidate), basename($expectedPath)) === 0) {
                        $expectedPath = $candidate;
                        break;
                    }
                }
            }
        }

        if (is_file($expectedPath)) {
            require_once $expectedPath;
        }
    }
}
