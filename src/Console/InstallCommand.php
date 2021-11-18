<?php

namespace Timedoor\TmdMembership\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Membership';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $modules = [
            'model'      => 1,
            'controller' => [
                'Admin'      => 'Http/Controllers/Admin',
                'Membership' => 'Http/Controllers/Api/V1/Membership'
            ],
            'request'    => 1,
            'event'      => 1,
            'listener'   => 1,
            'mail'       => 1,
            'helper'     => 1,
            'route'      => ['admin', 'membership'],
            'asset'      => ['assets', 'stisla'],
            'view'       => ['admin', 'components', 'layouts', 'mail'],
        ];

        foreach ($modules as $module => $params) {
            $bar = $this->output->createProgressBar(is_array($params) ? count($params) : 1);
            $bar->start();

            $this->{$module . 'Export'}($bar, $params);
            $this->info("Export {$module}s...");

            $bar->finish();
        }

         $this->newLine();
        $this->info('Membership installed successfully.');
        // $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
    }

    private function modelExport($bar)
    {
        (new Filesystem)->ensureDirectoryExists(app_path('Models'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Models', app_path('Models'));
        $bar->advance();
    }

    private function controllerExport($bar, $controllers)
    {
        foreach ($controllers as $controller => $path) {
            usleep(200000);
            (new Filesystem)->ensureDirectoryExists(app_path($path));

            $dir = __DIR__.'/../../stubs/app/Controllers/';
            $dir = ($controller == 'Membership') ? $dir . 'Api/V1/' : $dir;
            (new Filesystem)->copyDirectory($dir . $controller, app_path($path));
            $bar->advance();
        }
    }

    private function requestExport($bar)
    {
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/Api/V1'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Requests/Api/V1/Membership', app_path('Http/Requests/Api/V1/Membership'));
        $bar->advance();
    }

    private function eventExport($bar)
    {
        (new Filesystem)->ensureDirectoryExists(app_path('Events'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Events', app_path('Events'));
        $bar->advance();
    }

    private function listenerExport($bar)
    {
        (new Filesystem)->ensureDirectoryExists(app_path('Listeners'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Listeners', app_path('Listeners'));
        $bar->advance();
    }

    private function mailExport($bar)
    {
        (new Filesystem)->ensureDirectoryExists(app_path('Mail'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Mail', app_path('Mail'));
        $bar->advance();
    }

    private function helperExport($bar)
    {
        (new Filesystem)->ensureDirectoryExists(app_path('Helpers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Helpers', app_path('Helpers'));
        $bar->advance();
    }
    
    private function routeExport($bar, $routes)
    {
        foreach ($routes as $route) {
            usleep(200000);
            copy(__DIR__.'/../../stubs/routes/' . $route . '.php', base_path('routes/' . $route . '.php'));
            $bar->advance();
        }
    }

    private function assetExport($bar, array $assets)
    {
        foreach ($assets as $asset) {
            usleep(200000);
            (new Filesystem)->ensureDirectoryExists(public_path($asset));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/public/' . $asset, public_path($asset));
            $bar->advance();
        }
    }

    private function viewExport($bar, array $views)
    {
        foreach ($views as $view) {
            usleep(200000);
            (new Filesystem)->ensureDirectoryExists(resource_path('views/' . $view));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/resources/views/' . $view, resource_path('views/' . $view));
            $bar->advance();
        }
    }

    /**
     * Install Breeze's tests.
     *
     * @return void
     */
    protected function installTests()
    {
        (new Filesystem)->ensureDirectoryExists(base_path('tests/Feature/Auth'));

        if ($this->option('pest')) {
            $this->requireComposerPackages('pestphp/pest:^1.16', 'pestphp/pest-plugin-laravel:^1.1');

            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/pest-tests/Feature', base_path('tests/Feature/Auth'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/pest-tests/Unit', base_path('tests/Unit'));
            (new Filesystem)->copy(__DIR__.'/../../stubs/default/pest-tests/Pest.php', base_path('tests/Pest.php'));
        } else {
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/tests/Feature', base_path('tests/Feature/Auth'));
        }
    }

    /**
     * Install the Inertia Vue Breeze stack.
     *
     * @return void
     */
    protected function installInertiaVueStack()
    {
        // Install Inertia...
        $this->requireComposerPackages('inertiajs/inertia-laravel:^0.4.3', 'laravel/sanctum:^2.6', 'tightenco/ziggy:^1.0');

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                '@inertiajs/inertia' => '^0.10.0',
                '@inertiajs/inertia-vue3' => '^0.5.1',
                '@inertiajs/progress' => '^0.2.6',
                '@tailwindcss/forms' => '^0.2.1',
                '@vue/compiler-sfc' => '^3.0.5',
                'autoprefixer' => '^10.2.4',
                'postcss' => '^8.2.13',
                'postcss-import' => '^14.0.1',
                'tailwindcss' => '^2.1.2',
                'vue' => '^3.0.5',
                'vue-loader' => '^16.1.2',
            ] + $packages;
        });

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Auth'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Http/Controllers/Auth', app_path('Http/Controllers/Auth'));

        // Requests...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/Auth'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/App/Http/Requests/Auth', app_path('Http/Requests/Auth'));

        // Middleware...
        $this->installMiddlewareAfter('SubstituteBindings::class', '\App\Http\Middleware\HandleInertiaRequests::class');

        copy(__DIR__.'/../../stubs/inertia-common/app/Http/Middleware/HandleInertiaRequests.php', app_path('Http/Middleware/HandleInertiaRequests.php'));

        // Views...
        copy(__DIR__.'/../../stubs/inertia-common/resources/views/app.blade.php', resource_path('views/app.blade.php'));

        // Components + Pages...
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages'));

        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-vue/resources/js/Components', resource_path('js/Components'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-vue/resources/js/Layouts', resource_path('js/Layouts'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-vue/resources/js/Pages', resource_path('js/Pages'));

        // Tests...
        $this->installTests();

        // Routes...
        copy(__DIR__.'/../../stubs/inertia-common/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__.'/../../stubs/inertia-common/routes/auth.php', base_path('routes/auth.php'));

        // "Dashboard" Route...
        $this->replaceInFile('/home', '/dashboard', resource_path('js/Pages/Welcome.vue'));
        $this->replaceInFile('Home', 'Dashboard', resource_path('js/Pages/Welcome.vue'));
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Tailwind / Webpack...
        copy(__DIR__.'/../../stubs/inertia-common/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/inertia-common/webpack.mix.js', base_path('webpack.mix.js'));
        copy(__DIR__.'/../../stubs/inertia-common/webpack.config.js', base_path('webpack.config.js'));
        copy(__DIR__.'/../../stubs/inertia-common/jsconfig.json', base_path('jsconfig.json'));
        copy(__DIR__.'/../../stubs/inertia-common/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__.'/../../stubs/inertia-vue/resources/js/app.js', resource_path('js/app.js'));

        $this->info('Breeze scaffolding installed successfully.');
        $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
    }

    /**
     * Install the Inertia React Breeze stack.
     *
     * @return void
     */
    protected function installInertiaReactStack()
    {
        // Install Inertia...
        $this->requireComposerPackages('inertiajs/inertia-laravel:^0.3.5', 'laravel/sanctum:^2.6', 'tightenco/ziggy:^1.0');

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                '@headlessui/react' => '^1.2.0',
                '@inertiajs/inertia' => '^0.9.0',
                '@inertiajs/inertia-react' => '^0.6.0',
                '@inertiajs/progress' => '^0.2.4',
                '@tailwindcss/forms' => '^0.3.2',
                'autoprefixer' => '^10.2.4',
                'postcss' => '^8.2.13',
                'postcss-import' => '^14.0.1',
                'tailwindcss' => '^2.1.2',
                'react' => '^17.0.2',
                'react-dom' => '^17.0.2',
                '@babel/preset-react' => '^7.13.13',
            ] + $packages;
        });

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Auth'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Http/Controllers/Auth', app_path('Http/Controllers/Auth'));

        // Requests...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/Auth'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/App/Http/Requests/Auth', app_path('Http/Requests/Auth'));

        // Middleware...
        $this->installMiddlewareAfter('SubstituteBindings::class', '\App\Http\Middleware\HandleInertiaRequests::class');

        copy(__DIR__.'/../../stubs/inertia-common/app/Http/Middleware/HandleInertiaRequests.php', app_path('Http/Middleware/HandleInertiaRequests.php'));

        // Views...
        copy(__DIR__.'/../../stubs/inertia-common/resources/views/app.blade.php', resource_path('views/app.blade.php'));

        // Components + Pages...
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages'));

        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-react/resources/js/Components', resource_path('js/Components'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-react/resources/js/Layouts', resource_path('js/Layouts'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia-react/resources/js/Pages', resource_path('js/Pages'));

        // Tests...
        $this->installTests();

        // Routes...
        copy(__DIR__.'/../../stubs/inertia-common/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__.'/../../stubs/inertia-common/routes/auth.php', base_path('routes/auth.php'));

        // "Dashboard" Route...
        $this->replaceInFile('/home', '/dashboard', resource_path('js/Pages/Welcome.js'));
        $this->replaceInFile('Home', 'Dashboard', resource_path('js/Pages/Welcome.js'));
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Tailwind / Webpack...
        copy(__DIR__.'/../../stubs/inertia-common/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/inertia-common/webpack.mix.js', base_path('webpack.mix.js'));
        copy(__DIR__.'/../../stubs/inertia-common/webpack.config.js', base_path('webpack.config.js'));
        copy(__DIR__.'/../../stubs/inertia-common/jsconfig.json', base_path('jsconfig.json'));
        copy(__DIR__.'/../../stubs/inertia-common/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__.'/../../stubs/inertia-react/resources/js/app.js', resource_path('js/app.js'));

        $this->replaceInFile('.vue()', '.react()', base_path('webpack.mix.js'));
        $this->replaceInFile('.vue', '.js', base_path('tailwind.config.js'));

        $this->info('Breeze scaffolding installed successfully.');
        $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     * @return void
     */
    protected function installMiddlewareAfter($after, $name, $group = 'web')
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (! Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = str_replace(
                $after.',',
                $after.','.PHP_EOL.'            '.$name.',',
                $middlewareGroup,
            );

            file_put_contents(app_path('Http/Kernel.php'), str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $httpKernel
            ));
        }
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}