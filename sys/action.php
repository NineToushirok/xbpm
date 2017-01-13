<?
/**
 * Класс для наследования классами приложения 
 */
class ActionClass
{    
	/**
	 * Модель
	 */
    public $model;
	
	/**
	 * Подключения модели
	 * и
	 * Построение меню
	 */
    public function __construct()
    {
        global $routeClass;
        $class_name = get_class($this);

        $file = $routeClass->getClassFileModel($class_name);
        if(file_exists($file)):
            require_once ($file);
            $class_name .='_Model';
            $this->model = new $class_name;
        endif;
    }
}