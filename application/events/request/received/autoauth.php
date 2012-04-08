<?php
class events_request_received_autoauth {
	
    static public function handler($data = null) { 
		if (isset($_SESSION['done_autoauth'])) return;
		if (empty($_SERVER['SSL_CLIENT_RAW_CERT'])) return self::done();
		$certs = new certs(ConnectionFactory::get('redis'));
		$userId = $certs->check($_SERVER['SSL_CLIENT_RAW_CERT']);
		
		if ($userId == NULL) return self::done();
		
		$users = new users(ConnectionFactory::get('mongo'));
		$user = $users->get($userId, false);
		
		if (!in_array('autoauth', $user['auths'])) return self::done();
		if (Session::isLoggedIn()) return self::done();
		
		Session::setBatchVars($user);
		return self::done(); 
	}
	
	static private function done() {
		$_SESSION['done_autoauth'] = true;
		return false;
	}
	
}