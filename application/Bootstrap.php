<?php
class Bootstrap extends Yaf_Bootstrap_Abstract {
	protected $config;

	public function _initConfig(Yaf_Dispatcher $dispatcher) {
		$this->config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $this->config);
		//判断请求方式，命令行请求应跳过一些HTTP请求使用的初始化操作，如模板引擎初始化
		define('REQUEST_METHOD', strtoupper($dispatcher->getRequest()->getMethod()));
	}

	public function _initError(Yaf_Dispatcher $dispatcher) {
		if ($this->config->application->debug)
		{
			define('DEBUG_MODE', true);
			ini_set('display_errors', 'On');
		}
		else
		{
			define('DEBUG_MODE', false);
			ini_set('display_errors', 'Off');
		}
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		$routes = $this->config->routes;
		if (!empty($routes))
		{
			$router = $dispatcher->getRouter();
			$router->addConfig($routes);
		}
	}

	public function _initMemcache() {
		
	}

	public function _initDatabase() {
		$servers = array();
		$database = $this->config->database;
		$servers[] = $database->master->toArray();
		$slaves = $database->slaves;
		if (!empty($slaves))
		{
			$slave_servers = explode('|', $slaves->servers);
			$slave_users = explode('|', $slaves->users);
			$slave_passwords = explode('|', $slaves->passwords);
			$slave_databases = explode('|', $slaves->databases);
			$slaves = array();
			foreach ($slave_servers as $key => $slave_server)
			{
				if (isset($slave_users[$key]) && isset($slave_passwords[$key]) && isset($slave_databases[$key]))
				{
					$slaves[] = array('server' => $slave_server, 'user' => $slave_users[$key], 'password' => $slave_passwords[$key], 'database' => $slave_databases[$key]);
				}
			}
			$servers[] = $slaves[array_rand($slaves)];
		}
		Yaf_Registry::set('database', $servers);
		if (isset($database->mysql_cache_enable) && $database->mysql_cache_enable && !defined('MYSQL_CACHE_ENABLE'))
		{
			define('MYSQL_CACHE_ENABLE', true);
		}
		if (isset($database->mysql_log_error) && $database->mysql_log_error && !defined('MYSQL_LOG_ERROR'))
		{
			define('MYSQL_LOG_ERROR', true);
		}
	}

	public function _initMailer(Yaf_Dispatcher $dispatcher) {
		
	}

	public function _initView(Yaf_Dispatcher $dispatcher) {
		//命令行下基本不需要使用smarty
		if (REQUEST_METHOD != 'CLI'){
			
		}
	}

	public function _initSite(Yaf_Dispatcher $dispatcher) {
		define('SITE_NAME', 'dafan');
		//某些根据HTTP请求赋值的操作在命令行下应跳过
		if (REQUEST_METHOD != 'CLI'){
			
		}
	}

}
