<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


class Database {
	private static $_mysqlUser = "php-user"; 
	private static $_mysqlPass = "php-user"; 
	private static $_mysqlDb = "php-user"; 
	private static $_hostName = "localhost";

	protected static $_connection = NULL;

	private function __construct(){}

	public static function getConnection() {
		if (!self::$_connection) {
			self::$_connection = new mysqli(self::$_hostName, self::$_mysqlUser, self::$_mysqlPass, self::$_mysqlDb);
			if (self::$_connection->connect_error) {
				die('Connect Error: ' . self::$_connection->connect_error);
			}
		}
	return self::$_connection; 
	}	
}

?>
