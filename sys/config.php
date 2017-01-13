<?

/**
 * Class ConfigClass Конфигурация приложения
 */
class ConfigClass
{
	/**
	 * Список переменных конфигурации
	 */
	private $items = array();

	/**
	 * Загрузка переменных конфигурации
	 */
	function __construct()
	{
		include_once __DIR__.'/../config.php';
		$this->items = get_defined_vars();		
		
		if(isset($this->timeZone)) {
			date_default_timezone_set($this->timeZone);
		}
		if(isset($this->timeExecution)) {
			set_time_limit($this->timeExecution);
		}
	}
	
	/**
	 * Получение значения переменной конфигурации
	 */
	public function __get ( $name )
	{
		return $this->items[$name];
	}

	/**
	 * Проверка на существование значения переменной конфигурации
	 */
	public function __isset ( $name )
	{
		return isset($this->items[$name]);
	}
}
