<?
/**
 * Класс для получения данны всего приложения
 */
class AppClass
{
	/**
	 * Массив пользователей, по которым были запрошена информация
	 */
    private $user = array();
	
	/**
	 * Массив используемы классов
	 */
    private $classes = array();

	/**
	 * Инициализация текущего класса приложения
	 */
    public function init()
	{
		global $routeClass;
		global $responseClass;
		$file = $routeClass->getAppFile();
        require_once __DIR__.'/action.php';
        require_once($file);
        $className = $routeClass->getAppClass();
        $class = new $className;
        $action = $routeClass->getAppAction();
        if(method_exists($class, $action)):
            $class->$action($routeClass->params);
        else:
            $responseClass->redirect = '/404';
        endif;
		
	}

	/**
	 * Инициализация заданного класса приложения
	 */
    public function initClass($module, $class)
    {
        global $routeClass;
        if(isset($this->classes[$module])):
            if(isset($this->classes[$module][$class])):
                return $this->classes[$module][$class];
            endif;
        endif;
        $file = $routeClass->getClassFile($module, $class);
        require_once($file);
        $className=ucfirst($module).'_'.ucfirst($class);
        $initclass = new $className;
        $this->classes[$module][$class] = $initclass;
        return $initclass;
    }
}