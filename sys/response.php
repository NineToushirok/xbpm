<?

/**
 * Class ResponseClass Ответ пользователю
 */
class ResponseClass
{
    /**
     * @var array Список заголовков, которые необходимо подключить к странице шаблона
     */
    public $header = array();

    /**
     * @var array Список css, которые необходимо подключить к странице шаблона
     */
    private $css = array();

    /**
     * @var array Список скриптов, которые необходимо подключить к странице шаблона
     */
    private $script = array();

	public function redirect($url, $status = 302) {
		header('Location: ' . $url, true, $status);
		exit();
	}
}
