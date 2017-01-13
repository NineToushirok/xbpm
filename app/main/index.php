<?
class Main_Index extends ActionClass
{	

    public function action_index()
    {
		$view = 'main/index';
		$data['title'] = 'test';
		$data['head'] = $this->load('main/index/head', $data);
		$data['foot'] = $this->load('main/index/foot');
		$this->view->out($view, $data);	
    }
	
    public function action_404()
    {
		$view = 'main/404';
		$data['title'] = 'test';
		$data['head'] = $this->load('main/index/head', $data);
		$data['foot'] = $this->load('main/index/foot');
		$this->response->header[] = "HTTP/1.1 404 Not Found";
		$this->view->out($view, $data);	
    }
	
	public function action_head($data) {
		$view = 'main/head';
		$this->view->out($view, $data);	
	}
	
	public function action_foot() {
		$view = 'main/foot';
		$this->view->out($view);	
	}
}



