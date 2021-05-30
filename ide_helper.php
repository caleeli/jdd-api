<?php

namespace {

    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return mixed|\Illuminate\Contracts\Foundation\Application
     */
    function app($abstract = null, array $parameters = [])
    {
    }

    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|\Illuminate\Config\Repository
     */
    function config($key = null, $default = null)
    {
    }

    /**
     * Get the configuration path.
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '')
    {
    }

    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     * @return string
     */
    function public_path($path = '')
    {
    }

    /**
     * Get the path to the resources folder.
     *
     * @param  string  $path
     * @return string
     */
    function resource_path($path = '')
    {
    }

    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return \Illuminate\Http\Request|string|array|null
     */
    function request($key = null, $default = null)
    {
    }
}

namespace Illuminate\Support {

    use Closure;
    use Illuminate\Console\Application as Artisan;

    abstract class ServiceProvider
    {
        /**
         * The application instance.
         *
         * @var \Illuminate\Contracts\Foundation\Application
         */
        protected $app;

        /**
         * All of the registered booting callbacks.
         *
         * @var array
         */
        protected $bootingCallbacks = [];

        /**
         * All of the registered booted callbacks.
         *
         * @var array
         */
        protected $bootedCallbacks = [];

        /**
         * The paths that should be published.
         *
         * @var array
         */
        public static $publishes = [];

        /**
         * The paths that should be published by group.
         *
         * @var array
         */
        public static $publishGroups = [];

        /**
         * Create a new service provider instance.
         *
         * @param  \Illuminate\Contracts\Foundation\Application  $app
         * @return void
         */
        public function __construct($app)
        {
            $this->app = $app;
        }

        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
        }

        /**
         * Register a booting callback to be run before the "boot" method is called.
         *
         * @param  \Closure  $callback
         * @return void
         */
        public function booting(Closure $callback)
        {
        }

        /**
         * Register a booted callback to be run after the "boot" method is called.
         *
         * @param  \Closure  $callback
         * @return void
         */
        public function booted(Closure $callback)
        {
        }

        /**
         * Call the registered booting callbacks.
         *
         * @return void
         */
        public function callBootingCallbacks()
        {
        }

        /**
         * Call the registered booted callbacks.
         *
         * @return void
         */
        public function callBootedCallbacks()
        {
        }

        /**
         * Merge the given configuration with the existing configuration.
         *
         * @param  string  $path
         * @param  string  $key
         * @return void
         */
        protected function mergeConfigFrom($path, $key)
        {
        }

        /**
         * Load the given routes file if routes are not already cached.
         *
         * @param  string  $path
         * @return void
         */
        protected function loadRoutesFrom($path)
        {
        }

        /**
         * Register a view file namespace.
         *
         * @param  string|array  $path
         * @param  string  $namespace
         * @return void
         */
        protected function loadViewsFrom($path, $namespace)
        {
        }

        /**
         * Register the given view components with a custom prefix.
         *
         * @param  string  $prefix
         * @param  array  $components
         * @return void
         */
        protected function loadViewComponentsAs($prefix, array $components)
        {
        }

        /**
         * Register a translation file namespace.
         *
         * @param  string  $path
         * @param  string  $namespace
         * @return void
         */
        protected function loadTranslationsFrom($path, $namespace)
        {
        }

        /**
         * Register a JSON translation file path.
         *
         * @param  string  $path
         * @return void
         */
        protected function loadJsonTranslationsFrom($path)
        {
        }

        /**
         * Register database migration paths.
         *
         * @param  array|string  $paths
         * @return void
         */
        protected function loadMigrationsFrom($paths)
        {
        }

        /**
         * Register Eloquent model factory paths.
         *
         * @deprecated Will be removed in a future Laravel version.
         *
         * @param  array|string  $paths
         * @return void
         */
        protected function loadFactoriesFrom($paths)
        {
        }

        /**
         * Setup an after resolving listener, or fire immediately if already resolved.
         *
         * @param  string  $name
         * @param  callable  $callback
         * @return void
         */
        protected function callAfterResolving($name, $callback)
        {
        }

        /**
         * Register paths to be published by the publish command.
         *
         * @param  array  $paths
         * @param  mixed  $groups
         * @return void
         */
        protected function publishes(array $paths, $groups = null)
        {
        }

        /**
         * Ensure the publish array for the service provider is initialized.
         *
         * @param  string  $class
         * @return void
         */
        protected function ensurePublishArrayInitialized($class)
        {
        }

        /**
         * Add a publish group / tag to the service provider.
         *
         * @param  string  $group
         * @param  array  $paths
         * @return void
         */
        protected function addPublishGroup($group, $paths)
        {
        }

        /**
         * Get the paths to publish.
         *
         * @param  string|null  $provider
         * @param  string|null  $group
         * @return array
         */
        public static function pathsToPublish($provider = null, $group = null)
        {
        }

        /**
         * Get the paths for the provider or group (or both).
         *
         * @param  string|null  $provider
         * @param  string|null  $group
         * @return array
         */
        protected static function pathsForProviderOrGroup($provider, $group)
        {
        }

        /**
         * Get the paths for the provider and group.
         *
         * @param  string  $provider
         * @param  string  $group
         * @return array
         */
        protected static function pathsForProviderAndGroup($provider, $group)
        {
        }

        /**
         * Get the service providers available for publishing.
         *
         * @return array
         */
        public static function publishableProviders()
        {
        }

        /**
         * Get the groups available for publishing.
         *
         * @return array
         */
        public static function publishableGroups()
        {
        }

        /**
         * Register the package's custom Artisan commands.
         *
         * @param  array|mixed  $commands
         * @return void
         */
        public function commands($commands)
        {
        }

        /**
         * Get the services provided by the provider.
         *
         * @return array
         */
        public function provides()
        {
        }

        /**
         * Get the events that trigger this service provider to register.
         *
         * @return array
         */
        public function when()
        {
        }

        /**
         * Determine if the provider is deferred.
         *
         * @return bool
         */
        public function isDeferred()
        {
        }
    }
};

namespace Illuminate\Support\Facades{
    /**
     * @method static \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard guard(string|null $name = null)
     * @method static void shouldUse(string $name);
     * @method static bool check()
     * @method static bool guest()
     * @method static \Illuminate\Contracts\Auth\Authenticatable|null user()
     * @method static int|null id()
     * @method static bool validate(array $credentials = [])
     * @method static void setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
     * @method static bool attempt(array $credentials = [], bool $remember = false)
     * @method static bool once(array $credentials = [])
     * @method static void login(\Illuminate\Contracts\Auth\Authenticatable $user, bool $remember = false)
     * @method static \Illuminate\Contracts\Auth\Authenticatable loginUsingId(mixed $id, bool $remember = false)
     * @method static bool onceUsingId(mixed $id)
     * @method static bool viaRemember()
     * @method static void logout()
     * @method static \Symfony\Component\HttpFoundation\Response|null onceBasic(string $field = 'email',array $extraConditions = [])
     * @method static bool|null logoutOtherDevices(string $password, string $attribute = 'password')
     * @method static \Illuminate\Contracts\Auth\UserProvider|null createUserProvider(string $provider = null)
     * @method static \Illuminate\Auth\AuthManager extend(string $driver, \Closure $callback)
     * @method static \Illuminate\Auth\AuthManager provider(string $name, \Closure $callback)
     *
     * @see \Illuminate\Auth\AuthManager
     * @see \Illuminate\Contracts\Auth\Factory
     * @see \Illuminate\Contracts\Auth\Guard
     * @see \Illuminate\Contracts\Auth\StatefulGuard
     */
    class Auth
    {
    }
}
