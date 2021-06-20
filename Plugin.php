<?php namespace Crazy\Backend;

use Backend;
use System\Classes\PluginBase;

/**
 * Backend Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Backend',
            'description' => 'No description provided yet...',
            'author'      => 'Crazy',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        \Event::listen("backend.beforeRoute", function () {
            \Route::any(\Backend::uri() . "/backend/auth/{slug?}", function ($slug = '') {
                return \Redirect::to(\Backend::url("crazy/backend/auth/$slug"));
            });
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Crazy\Backend\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'crazy.backend.some_permission' => [
                'tab' => 'Backend',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'backend' => [
                'label'       => 'Backend',
                'url'         => Backend::url('crazy/backend/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['crazy.backend.*'],
                'order'       => 500,
            ],
        ];
    }
}
