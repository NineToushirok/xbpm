<?

/**
 * Class RequestClass Данные запроса
 */
class RequestClass
{
    /**
     * @var array Данные GET
     */
    public $get=array();

    /**
     * @var array Данные POST
     */
    public $post=array();

    /**
     * @var array Данные FILE
     */
    public $file=array();

    /**
     * @var array Данные COOKIE
     */
    public $cookie=array();

    public $raw = null;
	
    public $ajax = false;

	   /**
     * @var string Имя сервера, который принял запрос
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => subdomain.domain.zone
     */
    public $host = '';

    /**
     * @var string Протокол запроса, HTTP - по умолчанию
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => https
     */
    public $scheme = '';

    /**
     * @var string Описание браузера пользователя
     */
    public $agent = '';

    /**
     * @var string Полный (физический) путь к корню сайта, от корневой директории сервера
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => c:/web/www
     * @example https://subdomain.domain.zone/subdir/?id=0&db => c:/web/www
     * @example https://subdomain.domain.zone/subdir?id=0&db => c:/web/www
     * @example https://subdomain.domain.zone/?id=0&db => c:/web/www
     * @example https://subdomain.domain.zone?id=0&db => c:/web/www
     */
    public $path = '';

    /**
     * @var string Путь к скрипту (к index.php), от директории текущего запроса
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => /module/class/action/param1/param2/param3
     * @example https://subdomain.domain.zone/subdir/?id=0&db => /
     * @example https://subdomain.domain.zone/subdir?id=0&db => /
     * @example https://subdomain.domain.zone/?id=0&db => /
     * @example https://subdomain.domain.zone?id=0&db => /
     */
    public $url = '';

    /**
     * @var string Директория, в которой находится скрипт (index.php), относительно корновой директории сайта
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => /subdir
     * @example https://subdomain.domain.zone/subdir/?id=0&db => /subdir
     * @example https://subdomain.domain.zone/subdir?id=0&db => /subdir
     * @example https://subdomain.domain.zone/?id=0&db => empty
     * @example https://subdomain.domain.zone?id=0&db => empty
     */
    public $urlbase = '';

    /**
     * @var string Модуль, вызванный в результате запроса
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => module
     * @example https://subdomain.domain.zone/subdir/?id=0&db => default module
     * @example https://subdomain.domain.zone/subdir?id=0&db => default module
     * @example https://subdomain.domain.zone/?id=0&db => default module
     * @example https://subdomain.domain.zone?id=0&db => default module
     */
    public $module = '';

    /**
     * @var string Класс, вызванный в результате запроса
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => class
     * @example https://subdomain.domain.zone/subdir/?id=0&db => default class
     * @example https://subdomain.domain.zone/subdir?id=0&db => default class
     * @example https://subdomain.domain.zone/?id=0&db => default class
     * @example https://subdomain.domain.zone?id=0&db => default class
     */
    public $class = '';

    /**
     * @var string Функция, вызванная в результате запроса
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => action
     * @example https://subdomain.domain.zone/subdir/?id=0&db => default action
     * @example https://subdomain.domain.zone/subdir?id=0&db => default action
     * @example https://subdomain.domain.zone/?id=0&db => default action
     * @example https://subdomain.domain.zone?id=0&db => default action
     */
    public $action = '';

    /**
     * @var string Строка параметов, переданная в функцию, вызванную в результате запроса
     * @example https://subdomain.domain.zone/subdir/module/class/action/param1/param2/param3?id=0&db => param1/param2/param3
     * @example https://subdomain.domain.zone/subdir/?id=0&db => empty
     * @example https://subdomain.domain.zone/subdir?id=0&db => empty
     * @example https://subdomain.domain.zone/?id=0&db => empty
     * @example https://subdomain.domain.zone?id=0&db => empty
     */
    public $params = '';
    /**
     * @var bool Использовалась ли csrf защита на страницах
     */
    public $csrf = false;

    /**
     * Инициализация данных запроса
     */
    function __construct() 
    {
        foreach($_GET as $key =>$value){
            $this->get[$key] = $value;
		}
        foreach($_POST as $key =>$value){
            $this->post[$key] = $value;
        }
        foreach($_COOKIE as $key =>$value){
            $this->cookie[$key] = $value;
        }
        foreach($_FILES as $key =>$value){
            $this->file[$key] = $value;
        }

        $this->raw=file_get_contents("php://input");

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
            if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
				$this->ajax = true;
			}
		}
        $this->host = $_SERVER['HTTP_HOST'];
        $this->scheme = isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http';
		if(!isset($_SERVER['HTTP_USER_AGENT'])):
			$this->agent = '';
		else:	
			$this->agent = $_SERVER['HTTP_USER_AGENT'];
		endif;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $_SERVER['DOCUMENT_ROOT'];
        $script = $_SERVER['SCRIPT_NAME'];
        $this->urlbase = substr(substr($_SERVER['SCRIPT_FILENAME'], strlen($this->path)), 0, -strlen($script));
        // Для апачи - полный путь к скрипту /index.php/system/main/register/fssfs
        if(!isset($_SERVER['DOCUMENT_URI'])):
            $_SERVER['DOCUMENT_URI'] = $script.$_SERVER['REDIRECT_URL'];
        endif;
        $uri = $_SERVER['DOCUMENT_URI'];
        if(substr($uri, 0, strlen($script)) == $script):
            $uri = substr($uri, strlen($script) - 1);
        endif;
        // Для апачи - корневая дирректория скрипта
        if(!isset($_SERVER['REDIRECT_DIR'])):
            $_SERVER['REDIRECT_DIR'] = '/';
        endif;
        $this->url = substr($uri, strlen($_SERVER['REDIRECT_DIR'])) OR $this->url='/';
   }


}