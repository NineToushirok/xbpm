<?
class SessionClass
{
	private $request = null;
	private $config = null;
	private $session = '';
	private $path = '';
	
	private $data = [];
	private $changed = [];
	
    function __construct($config, $request)
    {
		$session = ''; 
		if(isset($request->cookie['xbpm'])) {
			$session = $request->cookie['xbpm'];
		} 
		if(strlen($session) !== 128) {
			$session = hash('sha512', $config->salt . $request->ip . $request->agent . microtime());
			if(setcookie('xbpm', $session, time()+31622400, '/') === false) {  // 1 year
				throw new RuntimeException('Unable to send cookie');
			}
		}
		
		$this->request = $request;
		$this->config = $config;
		$this->path = $request->path.'/'.$config->sessionPath.'/'.$session[0].'/'.$session[1].'/'.$session[2].'/';
		$this->session = $session;
		$this->data = $this->load();
    }
	
	function __destruct() {
		$this->save();	
	}
	
	public function save() {
		if(count($this->changed) > 0) {
			$file = $this->path;
			if(!file_exists($file)) {
				if(mkdir($file, 0777, true) === false) {
					throw new RuntimeException('Unable to create session path');
				}
			}
			$file .= $this->session;

			$fp = @fopen($file, 'wb');
			if($fp === false) {
				throw new RuntimeException('Unable to open session\'s file');
			}
			if(flock($fp, LOCK_EX) === false) {
				fclose($fp);
				throw new RuntimeException('Unable to lock session\'s file');
			}
			$data = $this->load();
			foreach($this->changed as $name) {
				if(!isset($this->data[$name])) {
					unset($data[$name]);
				} else {
					$data[$name] = $this->data[$name];
				}
			}
			$json = json_encode($data);
			if($json === false) {
				flock($fp, LOCK_UN);
				fclose($fp);
				throw new RuntimeException('Unable to encode session\'s data');
			}
			$json = fwrite($fp, $json);
			flock($fp, LOCK_UN);
			fclose($fp);
			if($json === false) {
				throw new RuntimeException('Unable to write data to session\'s file');
			}
		}		
	}
	
	public function load() {
		$file = $this->path.$this->session;

		$data = [];
		if(file_exists($file)) {
			$json = @file_get_contents($file);
			if($json === false) {
				throw new RuntimeException('Unable to read session\'s file');
			}
			if(strlen($json) > 0) {
				$json = json_decode($json, true);
				if($json === false) {
					throw new RuntimeException('Unable to decode session\'s data');
				}
				if(!is_array($json)) {
					throw new RuntimeException('Incorrect format session\'s data');
				}
				$data = $json;
			}
		}
		return $data;
	}
	
	public function __set($name, $value) {
		if(!in_array($name, $this->changed)) {
			$this->changed[] = $name;
		}
		$this->data[$name] = $value;
	}
	
	public function __get($name) {
		if(isset($this->data[$name])) {
			return $this->data[$name];			
		}
		return null;
	}	
	
	public function __isset($name) {
		return $this->data[$name];
	}
	
	public function __unset($name) {
		if(!in_array($name, $this->changed)) {
			$this->changed[] = $name;
		}
		unset($this->data[$name]);
	}
}