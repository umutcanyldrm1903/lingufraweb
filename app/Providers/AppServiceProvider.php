<?php

namespace App\Providers;

use App\Enums\ThemeList;
use App\Support\SettingBag;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
            $seo_setting = (object) [];
        } else {
            try {
                /** Cache settings */
                $setting = $this->makeSettingBag(
                    Cache::rememberForever('setting', fn() => Setting::pluck('value', 'key')->all())
                );
                $marketing_setting = Cache::rememberForever('marketing_setting', fn() => (object) MarketingSetting::pluck('value', 'key')->all());
                $seo_setting = Cache::rememberForever('seo_setting', fn() => (object) SeoSetting::all()->groupBy('page_name')->mapWithKeys(function ($group, $pageName) {
                    return [$pageName => $group->first()];
                }));

                set_wasabi_config();
                set_aws_config();
            } catch (\Throwable $th) {
                info($th);
                $setting = $this->makeSettingBag();
                $marketing_setting = (object) [];
                $seo_setting = (object) [];
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

    private function makeSettingBag(array $attributes = []): object
    {
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
