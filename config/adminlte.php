<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '',
    'logo_img' => 'images/logo-muni-jlo.png',
    'logo_img_class' => 'brand-image',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'images/logo-muni-jlo.png',
            'alt' => 'Logo RSU',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images/logo-muni-jlo.png',
            'alt' => 'Logo Municipalidad JLO',
            'effect' => 'animation__pulse',
            'width' => 160,
            'height' => 90,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Custom CSS
    |--------------------------------------------------------------------------
    |
    | Custom CSS styles for the project color scheme
    |
    */
    'injected_css' => '
        :root {
            --primary-color: #002b5a;
            --secondary-color: #0086cd;
            --primary-hover: #001a3a;
            --secondary-hover: #006ba3;
        }
        
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }
        
        .btn-secondary:hover {
            background-color: var(--secondary-hover) !important;
            border-color: var(--secondary-hover) !important;
        }
        
        .btn-info {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }
        
        .btn-info:hover {
            background-color: var(--secondary-hover) !important;
            border-color: var(--secondary-hover) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .bg-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .text-secondary {
            color: var(--secondary-color) !important;
        }
        
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: var(--primary-color) !important;
        }
        
        .navbar-primary {
            background-color: var(--primary-color) !important;
        }
        
        .navbar-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        /* Dropdown items override - Máxima prioridad */
        .dropdown-item:hover,
        .dropdown-item:focus,
        .dropdown-item.active {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
        
        .dropdown-item {
            color: #002b5a !important;
            background-color: #ffffff !important;
        }
        
        /* Sidebar nav-treeview active items override */
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active,
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus,
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
        
        .card-primary .card-header {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .card-secondary .card-header {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }
        
        .small-box.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .small-box.bg-info {
            background-color: var(--secondary-color) !important;
        }
        
        .badge-primary {
            background-color: var(--primary-color) !important;
        }
        
        .badge-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        .border-primary {
            border-color: var(--primary-color) !important;
        }
        
        .border-secondary {
            border-color: var(--secondary-color) !important;
        }
        
        a {
            color: var(--primary-color);
        }
        
        a:hover {
            color: var(--primary-hover);
        }
        
        .form-control:focus {
            border-color: var(--secondary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 134, 205, 0.25) !important;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        /* FUERZA BRUTA - OVERRIDE ABSOLUTO */
        html body .dropdown-item:hover,
        html body .dropdown-item:focus,
        html body .dropdown-item.active,
        html body .navbar .dropdown-item:hover,
        html body .navbar .dropdown-item:focus {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
        
        html body .dropdown-item {
            color: #002b5a !important;
            background-color: #ffffff !important;
        }
        
        html body [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active,
        html body [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus,
        html body [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
        
        /* Override específico para sidebar-dark submenu */
        html body .main-sidebar.sidebar-dark-primary .nav-treeview .nav-item .nav-link.active,
        html body .sidebar-dark .nav-treeview .nav-item .nav-link.active,
        html body .sidebar-dark-primary .nav-treeview .nav-item .nav-link.active {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
        
        /* Override ultra específico para cualquier submenu activo */
        html body .nav-treeview .nav-link.active {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
        
        /* OVERRIDE EXACTO DEL SELECTOR PROBLEMÁTICO */
        html body [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active,
        html body [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus,
        html body [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
            background-color: #0086cd !important;
            color: #ffffff !important;
        }
    ',

    /*
    |--------------------------------------------------------------------------
    | JavaScript Injection
    |--------------------------------------------------------------------------
    */
    
    'injected_js' => '
        document.addEventListener("DOMContentLoaded", function() {
            // Crear múltiples estilos para asegurar override
            const style1 = document.createElement("style");
            style1.setAttribute("id", "force-override-adminlte");
            style1.innerHTML = `
                [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active,
                [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus,
                [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
                    background-color: #0086cd !important;
                    color: #ffffff !important;
                }
                .dropdown-item:hover,
                .dropdown-item:focus,
                .dropdown-item.active {
                    background-color: #0086cd !important;
                    color: #ffffff !important;
                }
            `;
            document.head.appendChild(style1);
            
            // Agregar CSS con mayor especificidad
            const style2 = document.createElement("style");
            style2.innerHTML = `
                html body .main-sidebar [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active,
                html body .main-sidebar [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus,
                html body .main-sidebar [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
                    background-color: #0086cd !important;
                    color: #ffffff !important;
                }
            `;
            document.head.appendChild(style2);
            
            // Fuerza bruta con setTimeout para asegurar que se aplique al final
            setTimeout(function() {
                const elements = document.querySelectorAll("[class*=sidebar-dark-] .nav-treeview .nav-item .nav-link.active");
                elements.forEach(function(el) {
                    el.style.setProperty("background-color", "#0086cd", "important");
                    el.style.setProperty("color", "#ffffff", "important");
                });
            }, 100);
        });
    ',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-primary navbar-dark',
    'classes_brand' => 'navbar-primary',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'admin',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:

        ['header' => 'DASHBOARD'],
        [
            'text' => 'Dashboard',
            'route' => 'admin.index',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        [
            'text' => 'Gestión de Vehículos',
            'icon' => 'fas fa-car',
            'submenu' => [
                 [
                    'text' => 'Color',
                    'route'  => 'admin.colors.index',
                    'icon' => 'fas fa-fw fa-palette',
                ],
                [
                    'text' => 'Marcas',
                    'url'  => 'brands',
                    'icon' => 'fas fa-fw fa-tags'
                ],
                [
                    'text' => 'Modelos',
                    'url'  => 'brandmodels',
                    'icon' => 'fas fa-fw fa-wrench'
                ],
                [
                    'text' => 'Tipo de Vehículo',
                    'url'  => 'vehicletypes',
                    'icon' => 'fas fa-car'
                ],
                [
                    'text' => 'Vehículo',
                    'route'  => 'admin.vehicles.index',
                    'icon' => 'fas fa-car-side',
                ],
                [
                    'text'    => 'Mantenimiento',
                    'route'  => 'admin.maintenances.index',
                    'icon'    => 'fas fa-fw fa-tools',
                ],
            ],
        ],
        [
            'text'    => 'Gestión de Empleados',
            'icon'    => 'fas fa-users',
            'submenu' => [
                [
                    'text' => 'Dashboard Personal',
                    'route'  => 'admin.personnel.dashboard',
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                ],
                [
                    'text' => 'Tipo de Empleados',
                    'route'  => 'admin.personnel.employee-types.index',
                    'icon' => 'fas fa-fw fa-user-tie',
                ],
                [
                    'text' => 'Empleados',
                    'route'  => 'admin.personnel.employees.index',
                    'icon' => 'fas fa-fw fa-user',
                ],
                [
                    'text' => 'Contratos',
                    'route'  => 'admin.personnel.contracts.index',
                    'icon' => 'fas fa-fw fa-file-contract',
                ],
                [
                    'text' => 'Asistencias',
                    'route'  => 'admin.personnel.attendances.index',
                    'icon' => 'fas fa-fw fa-clock',
                ],
                [
                    'text' => 'Dashboard Asistencias',
                    'route'  => 'admin.personnel.attendances.dashboard',
                    'icon' => 'fas fa-fw fa-chart-line',
                ],
                [
                    'text' => 'Vacaciones',
                    'route'  => 'admin.personnel.vacations.index',
                    'icon' => 'fas fa-fw fa-calendar-alt',
                ],
            ],
        ],
        [
            'text'    => 'Programación',
            'icon'    => 'fas fa-fw fa-calendar-alt',
            'submenu' => [
                [
                    'text' => 'Turnos',
                    'route'  => 'admin.shifts.index',
                    'icon' => 'fas fa-fw fa-clock',
                ],
                [
                    'text' => 'Zonas',
                    'route' => 'admin.zonesjenkz.index',
                    'icon' => 'fas fa-fw fa-map-marked-alt',
                ],
                // [
                //     'text' => 'Rutas de Recolección',
                //     'route' => 'admin.routes.index',
                //     'icon' => 'fas fa-fw fa-route',
                // ],
                [
                    'text' => 'Grupo de Personal',
                    'route' => 'admin.personnel.employeegroups.index',
                    'icon' => 'fas fa-fw fa-users',
                ],
                [
                'text'  => 'Programación',
                'route' => 'admin.schedulings.index',
                'icon'  => 'fas fa-fw fa-clock',
                ],
            ],
        ],
        [
            'text'    => 'Gestión de Cambios',
            'icon'    => 'fas fa-exchange-alt',
            'submenu' => [
                [
                    'text' => 'Motivos',
                    'route'  => 'admin.reasons.index',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                ],
                [
                     'text' => 'Cambios',
                     'route'  => 'admin.scheduling-changes.index',
                     'icon' => 'fas fa-fw fa-retweet',
                ],
            ],
        ],
        [
            'text'    => 'Otros',
            'icon'    => 'fa fa-bug',
            'submenu' => [
                [
                    'text' => 'Kiosco de Asistencias',
                    'route'  => 'attendance-kiosk.index',
                    'icon' => 'fas fa-fw fa-desktop',
                    'target' => '_blank',
                ],
            ],
        ],
        ['header' => 'CONFIGURACIÓN'],
        // [
        //     'text' => 'Configuración',
        //     'url' => '#',
        //     'icon' => 'fas fa-fw fa-cog',
        // ],
        [
            'text' => 'Usuarios',
            'url' => '#',
            'icon' => 'fas fa-fw fa-user-cog',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'CustomTheme' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/custom-theme.css',
                ],
            ],
        ],
        'AdminLTEOverride' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/adminlte-override.css',
                ],
            ],
        ],
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
