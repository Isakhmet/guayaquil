<?php
/**
 * Created by Laximo.
 * User: elnikov.a
 * Date: 17.08.17
 * Time: 16:03
 */

namespace guayaquil;

use guayaquil\guayaquillib\data\GuayaquilRequestOEM;
use guayaquil\guayaquillib\data\Language;
use guayaquil\modules\Input;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Filter_Function;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

/**
 * @property bool                user
 * @property GuayaquilRequestOEM request
 * @property bool                dev
 */
class View
{
    /**
     * @var bool
     */
    protected $error;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $errorTrace;

    /**
     * @var array
     */
    private $responseData;

    /**
     * @var string
     */
    public $theme;

    public function __construct()
    {
        $this->input = new Input();
        $this->data  = $this->getData();
        $this->theme = Config::$theme;

        if (Config::$useWebserviceAuthorize) {
            session_start();
        }
    }

    /**
     * @param array  $requests
     *
     * @param array  $params
     *
     * @param string $login
     * @param string $pass
     *
     * @return array
     */
    public function getData($requests = [], $params = [], $login = '', $pass = '')
    {
        $c   = isset($params['c']) ? $params['c'] : '';
        $ssd = isset($params['ssd']) ? $params['ssd'] : '';

        $request = new GuayaquilRequestOEM($c, $ssd, Config::$catalog_data);
        if (Config::$useLoginAuthorizationMethod) {
            $request->setUserAuthorizationMethod($login, $pass);
        }

        foreach ($requests as $requestItem => $paramsArr) {
            call_user_func_array([$request, $requestItem], $paramsArr);
        }

        $this->user = false;

        if ($data = $request->query()) {
            $this->user = true;
        }


        if ($request->error && (strpos($request->error, 'E_ACCESSDENIED') !== false)) {

            unset($request);
            $request = new GuayaquilRequestOEM($c, $ssd, Config::$catalog_data);
            if (Config::$useLoginAuthorizationMethod) {
                $request->setUserAuthorizationMethod(Config::$defaultUserLogin, Config::$defaultUserKey);
            }

            foreach ($requests as $requestItem => $paramsArr) {
                call_user_func_array([$request, $requestItem], $paramsArr);
            }

            if ($data = $request->query()) {
                $this->user = false;
            }
        }

        if ($request->error) {
            $this->error      = true;
            $this->message    = $request->error;
            $this->errorTrace = $request->errorTrace;
        }
        $this->responseData = $request->responseData;

        $this->request = $request;

        return $data;
    }

    public function Display($tpl = 'catalogs/tmpl', $view = 'view.twig')
    {
        $this->dev          = Config::$dev;
        $productionRevision = false;

        if (!$this->dev) {
            $productionRevision = json_decode(file_get_contents(GUAYAQUIL_DIR . '/revision.json'));
        }

        if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
            $this->user = true;
        } else {
            if (!Config::$showToGuest) {
                http_response_code(401);
                $this->renderHead([
                    'user'               => $this->user,
                    'dev'                => $this->dev,
                    'username'           => isset($_SESSION['username']) ? $_SESSION['username'] : '',
                    'productionRevision' => $productionRevision ?: '',
                    'useEnvParams'       => Config::$useEnvParams
                ]);
                $this->loadTwig('error/tmpl', 'unauthorized.twig', ['type' => 'unauthorized']);
                $this->renderFooter();
                die();
            }
        }

        $this->renderHead([
            'user'               => $this->user,
            'dev'                => $this->dev,
            'showToGuest'        => Config::$showToGuest,
            'useEnvParams'       => Config::$useEnvParams,
            'showGroupsToGuest'  => Config::$showGroupsToGuest,
            'showOemsToGuest'    => Config::$showOemsToGuest,
            'username'           => isset($_SESSION['username']) ? $_SESSION['username'] : '',
            'productionRevision' => $productionRevision ?: ''
        ]);

        $auth = $this->input->getString('auth', '');

        $language = new Language();

        if ($auth === 'true') {
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
            $message  = str_replace('%name%', $username, $language->t('AUTHORIZED'));
            $this->showMessage($message, 'success');
        } elseif ($auth === 'false') {
            $message = $language->t('UNAUTHORIZED');
            $this->showMessage($message, 'warning');
        }


        if (!isset($this->pathway)) {
            $this->pathway = null;
        }

        if (!isset($this->error)) {
            $this->error = null;
        }

        if ($this->error) {

            if (Config::$useEnvParams && strpos($this->message, 'E_ACCESSDENIED') !== false) {
                $this->message = 'E_ACCESSDENIED';
            }

            $this->loadTwig('error/tmpl', 'default.twig', ['message' => $this->message, 'more' => $this->errorTrace]);
        }

        if ($this->pathway) {
            $this->renderPathway($this->pathway);
        }

        $format = $this->input->getString('format');

        if ($format !== 'raw') {
            $task          = $this->input->getString('task');
            $this->toolbar = in_array($task, Config::$toolbarPages);
            $this->showRequest();
        }

        $this->loadTwig($tpl . '/tmpl', $view . '.twig', (array)$this);
        $this->renderFooter();
    }

    public function renderHead($vars = [])
    {
        $input  = new Input();
        $format = $input->getString('format');
        $raw    = $format && $format === 'raw' ? true : false;

        if (!$raw) {
            $rootDir = realpath(__DIR__);

            $layoutsLoader = new Twig_Loader_Filesystem($rootDir . '/layouts/');
            $layouts       = new Twig_Environment($layoutsLoader, array(
                'cache'       => false,
                'auto_reload' => true,
            ));

            $language = new Language();
            $layouts->addFilter('t', new Twig_Filter_Function([$language, 't']));
            $createUrlFunc = new Twig_SimpleFunction('createUrl', [$language, 'createUrl']);
            $layouts->addFunction($createUrlFunc);
            $currentLocale = $language->getLocalization();
            $input         = new Input();

            echo $layouts->render('head.twig', [
                'languages'        => $language->getLocalizationsList(),
                'current'          => $currentLocale ?: Config::$catalog_data,
                'availablePages'   => Config::$toolbarPages,
                'theme'            => Config::$theme ?: 'guayaquil',
                'task'             => $input->getString('task', ''),
                'additional'       => $vars,
                'useAuthorization' => Config::$useWebserviceAuthorize
            ]);
        }
    }

    public function showMessage($message, $type = 'default')
    {
        $language = new Language();

        $this->loadTwig('tmpl', 'message.twig', ['message' => $language->t($message), 'type' => $type]);
    }

    public function loadTwig($tpl = '', $view = '', $vars = [])
    {
        if ($tpl === '') {
            $tpl = 'tmpl';
        }

        $rootDir = realpath(__DIR__);
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem($rootDir . '/views/' . $tpl . '/');
        $twig   = new Twig_Environment($loader, array(
            'cache'       => false,
            'auto_reload' => true,
        ));

        $language = new language();

        $createUrlFunc = new Twig_SimpleFunction('createUrl', [$language, 'createUrl']);
        $twig->addFunction($createUrlFunc);

        $twig->addFilter('dump', new Twig_Filter_Function('var_dump'));
        $twig->addFilter('t', new Twig_Filter_Function([$language, 't']));
        $twig->addFilter('noSpaces', new Twig_Filter_Function([$language, 'noSpaces']));
        $twig->addFilter('printr', new Twig_Filter_Function('print_r'));

        echo $twig->render($view, $vars);

        return $twig;
    }

    public function renderPathway($pathway)
    {
        $input  = new Input();
        $format = $input->getString('format');
        $raw    = $format && $format === 'raw' ? true : false;

        if (!$raw) {
            $rootDir       = realpath(__DIR__);
            $language      = new language();
            $layoutsLoader = new Twig_Loader_Filesystem($rootDir . '/layouts/');
            $layouts       = new Twig_Environment($layoutsLoader, array(
                'cache'       => false,
                'auto_reload' => true,
            ));

            $function = new Twig_SimpleFunction('createUrl', [$language, 'createUrl']);
            $layouts->addFunction($function);

            $layouts->addFilter('dump', new Twig_Filter_Function('var_dump'));
            $layouts->addFilter('t', new Twig_Filter_Function([$language, 't']));
            $layouts->addFilter('noSpaces', new Twig_Filter_Function([$language, 'noSpaces']));
            $layouts->addFilter('printr', new Twig_Filter_Function('print_r'));
            $currentLink = getenv('REQUEST_URI');

            $vars = [
                'pathway' => $pathway,
                'current' => $currentLink
            ];

            echo $layouts->render('pathway.twig', $vars);
        }
    }

    public function showRequest()
    {
        if (Config::$showRequest) {
            $this->loadTwig('tmpl', 'request.twig', ['this' => $this, 'response' => $this->responseData]);
        }
    }

    public function renderFooter()
    {
        $input  = new Input();
        $format = $input->getString('format');
        $raw    = $format && $format === 'raw' ? true : false;
        $task   = $this->input->getString('task');

        if (!$raw && in_array($task, Config::$toolbarPages)) {
            $rootDir = realpath(__DIR__);

            $layoutsLoader = new Twig_Loader_Filesystem($rootDir . '/layouts/');
            $layouts       = new Twig_Environment($layoutsLoader, array(
                'cache'       => false,
                'auto_reload' => true,
            ));

            echo $layouts->render('footer.twig', []);
        }
    }

    public function redirect($link)
    {
        header("Location: " . $link);
        exit();
    }

    function returnRequest($request, $requestItem)
    {
        return $request->$requestItem();
    }

    public function getBackUrl()
    {
        $envBackUrl = getenv('UUE_BACK_URL');
        if ($envBackUrl && Config::$useEnvParams) {
            return base64_decode($envBackUrl);
        }

        if (!Config::$useEnvParams) {
            return Config::$SiteDomain;
        }

        return false;
    }

    public function getLinkTarget() {
        if (!Config::$useEnvParams) {
            return Config::$linkTarget;
        }

        $envTarget = getenv('BACKURL_NEW_WINDOW');

        return boolval($envTarget) ? '_blank': Config::$linkTarget;
    }
}