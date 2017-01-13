<?
class SessionClass
{
	private $request = null;
	
    function __construct($request)
    {
		$this->request = $request;
		//var_dump($this->request);
    }
}