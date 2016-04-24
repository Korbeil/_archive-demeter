<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 15:57
 */

    namespace Demeter\Core;

    class Application {

        protected $app;

        public function __construct() {
            $this->app = new \Silex\Application();
        }

        public function init() {
            // debug
            $this->app['debug'] = true;

            // twig
            $this->app->register(new \Silex\Provider\TwigServiceProvider(), Array(
                'twig.path'     => __DIR__.'/../../../views/',
                'twig.options'  => Array(
                    'debug'     => true,
                    'cache'     => false
                )
            ));
            $this->app['twig']->addGlobal('session', $_SESSION);

            // notifications
            $notifObj = NULL;
            if(isset($_SESSION['notification_manager']) && !empty($_SESSION['notification_manager'])) {
                $notifObj = unserialize($_SESSION['notification_manager']);
            } else {
                $notifObj = new \Demeter\Notification\NotificationManager();
            }
            \Demeter\Utils\GlobalVars::getInstance()->set('notifications', $notifObj);
            $this->app['twig']->addGlobal('notifications', $notifObj->render());

            //////////////////////////////////////////////////////////
            // check for connection
            $this->app->before(function (\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app) {
                if( isset($_SESSION['isLogged']) && $_SESSION['isLogged']) {
                    return;
                }

                $requestUri = $request->getRequestUri();
                $requestUri = explode('?', $requestUri);
                $requestUri = $requestUri[0];

                if( $requestUri != '/login' &&
                    $requestUri != '/register' &&
                    $requestUri != '/eve-sso') {

                    return $app->redirect('/login');
                }
            }, \Silex\Application::EARLY_EVENT);

            //////////////////////////////////////////////////////////
            // init Pheal Config
            \Pheal\Core\Config::getInstance()->cache = new \Pheal\Cache\RedisStorage();
            \Pheal\Core\Config::getInstance()->access = new \Pheal\Access\StaticCheck();

        }

        public function config() {
            if(!file_exists(__DIR__. '/../../../config.php')) {
                throw new \Demeter\Exception\ConfigFileException('Configuration file doesn\'t exists.');
            }

            require_once __DIR__. '/../../../config.php';
        }

        public function route() {
            $controllers = Array(
                'Account',
                'ApiKeys',
                'Character',
                'Dashboard',
                'Error',
                'LoginRegister'
            );

            foreach($controllers as $controller) {
                $class = '\\Demeter\\Controller\\' .$controller. 'Controller';
                $class::route($this->app);
            }
        }

        public function routine() {
            $this->init();
            $this->config();
            $this->route();

            $this->app->run();
        }

    }