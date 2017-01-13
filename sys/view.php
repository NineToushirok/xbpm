<?

/**
 * Class TemplateClass 
 */
class ViewClass
{
	private $view = 'default';
	
	public function setView($view) {
		$this->view = $view;
	}
	
	public function out($view, $data=[]) {		
		foreach($this->response->header as $head) {
			header($head, false);
		}
		if(empty($view)) {
			echo $data;
		} else {
			echo $this->render($view, $data);
		}
	}
	
	public function render($view, $data=[]) {	
		$file = __DIR__.'/../view/'.$this->view.'/'.$view.'.php';
		if (is_file($file)) {
			extract($data);
			ob_start();
			require($file);
			return ob_get_clean();
		}
		return '';
	}
}