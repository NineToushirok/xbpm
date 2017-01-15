<?
/**
 * Класс для наследования классами приложения 
 */
class ActionClass
{    
    private static $_config = null;
    private static $_db = null;
    private static $_session = null;
    private static $_cache = null;
    private static $_request = null;
    private static $_response = null;
    private static $_view = null;

    public $config;
    public $db;
    public $session;
    public $cache;
    public $request;
    public $response;
    public $view;
    public $model;
	
	/**
	 * Подключения модели
	 * и
	 * Построение меню
	 */
    public function __construct()
    {
		if(is_null(self::$_config)) {
			require_once __DIR__.'/config.php';
			self::$_config = new ConfigClass();
		}
		$this->config = self::$_config;
		if(is_null(self::$_db)) {
			require_once __DIR__.'/db.php';
			self::$_db = new DBClass();
		}
		$this->db = self::$_db;
		if(is_null(self::$_cache)) {
			require_once __DIR__.'/cache.php';
			self::$_cache = new CacheClass();
		}
		$this->cache = self::$_cache;
		if(is_null(self::$_request)) {
			require_once __DIR__.'/request.php';
			self::$_request = new RequestClass();
		}
		$this->request = self::$_request;
		if(is_null(self::$_session)) {
			require_once __DIR__.'/session.php';
			self::$_session = new SessionClass($this->config, $this->request);
		}

		$this->session = self::$_session;
		if(is_null(self::$_response)) {
			require_once __DIR__.'/response.php';
			self::$_response = new ResponseClass();
		}
		$this->response = self::$_response;
		if(is_null(self::$_view)) {
			require_once __DIR__.'/view.php';
			self::$_view = new ViewClass();
			self::$_view->response = self::$_response;
		}
		$this->view = self::$_view;
		if(isset($this->config->view)) {
			$this->view->setView($this->config->view);
		}
		
        $class_name = get_class($this);		
		$file = __DIR__.'/../app/'.$this->request->module.'/'.$this->request->class.'_model.php';
		
        if(file_exists($file)){
            require_once ($file);
            $class_name .='_Model';
			if(class_exists($class_name)){
				$this->model = new $class_name;
				$this->model->db = $this->db;
				$this->model->cache = $this->cache;
				$this->model->session = $this->session;
			} else {
				$this->model = new stdClass();
			}		
		} else {
			$this->model = new stdClass();
		}		
    }
	
	public function load($app, $data=[]) {
		list($module, $class, $action, $params) = self::extract_route($app);
		ob_start();
		self::start_route($module, $class, $action, $params, $data);
		return ob_get_clean();
	}
	
	public static function extract_route($route) {
		$load = explode('/', $route, 4);
		if(isset($load[0]) && strlen($load[0]) > 0){
			$module = $load[0];
		} else {
			$module = 'main';
		}
		if(isset($load[1]) && strlen($load[1]) > 0){
			$class = $load[1];
		} else {
			$class = 'index';
		}
		if(isset($load[2]) && strlen($load[2]) > 0){
			$action = $load[2];
		} else {
			$action = 'index';
		}
		
		if(isset($load[3]) && strlen($load[3]) > 0){
			$params = $load[3];
		} else {
			$params = '';
		}
		return [$module, $class, $action, $params];
	}

	public static function start_route($module, $class, $action, $params, $data=[]) {
		$is_404 = false;	
		$file = __DIR__.'/../app/'.$module.'/'.$class.'.php';
		if(file_exists($file)) {	
			require_once $file;
			$className = ucfirst($module).'_'.ucfirst($class);	
			if(class_exists($className)){
				$className = new $className();
				$className->request->module = $module;
				$className->request->class = $class;
				$className->request->action = $action;
				$className->request->params = $params;
				$actionName = 'action_'.$action;
				if(method_exists($className, $actionName)){
					$className->$actionName($data);
				} else {
					$is_404 = true;
				}
			} else {
				$is_404 = true;
			}
		} else {
			$is_404 = true;
		}
		if($is_404) {
			require_once __DIR__.'/../app/main/index.php';
			$className = new Main_Index();
			$className->action_404();
		}	
	}
}  


	$uri = substr(explode('?', $_SERVER['REQUEST_URI'])[0], 1);	
	list($module, $class, $action, $params) = ActionClass::extract_route($uri);
	ActionClass::start_route($module, $class, $action, $params);
