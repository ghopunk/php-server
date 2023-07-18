<?php

namespace ghopunk\Helpers\Server;

class Is {
	
	public static function publicIp(){
		if(	isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']) && 
			isset($_SERVER['SERVER_ADDR']) && !empty($_SERVER['SERVER_ADDR']) && 
			$_SERVER['HTTP_HOST'] === $_SERVER['SERVER_ADDR']
		){
			return true;
		}
		return false;
	}
	
	public static function localhost(){
		$localip = array( '127.0.0.1', '::1' );
		if(	isset( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] === 'localhost' 
			|| isset( $_SERVER['REMOTE_ADDR'] ) && in_array( $_SERVER['REMOTE_ADDR'], $localip )
			|| isset( $_SERVER['SERVER_ADDR'] ) && in_array( $_SERVER['SERVER_ADDR'], $localip )
		) {
			return true;
		}
		return false;
	}
	
	public static function nginx(){
		if( isset( $_SERVER['SERVER_SOFTWARE'] ) 
			&& stristr( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false 
		) {
			return true;
		}
		return false;
	}
	
	public static function windows(){
		if(	strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			return true;
		}
		return false;
	}
	
	public static function httpsSite(){
		if(	isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
			|| isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443
			|| isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https'
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
			|| isset($_SERVER['HTTP_CF_VISITOR']) && json_decode($_SERVER['HTTP_CF_VISITOR'])->scheme === 'https' //cloudflare
		){
			return true;
		}
		return false;
	}
	
	public static function phpCli(){
		if ( defined('STDIN') ) {
			return true;
		}
		if ( php_sapi_name() === 'cli' ) {
			return true;
		}
		if ( array_key_exists('SHELL', $_ENV) ) {
			return true;
		}
		if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
			return true;
		}
		if ( !array_key_exists('REQUEST_METHOD', $_SERVER) ) {
			return true;
		}
		return false;
	}
	
	public static function clientIpAddress() {
		$request	= array( 
							'HTTP_CLIENT_IP', 
							'HTTP_X_FORWARDED_FOR', 
							'HTTP_X_FORWARDED', 
							'HTTP_X_CLUSTER_CLIENT_IP', 
							'HTTP_FORWARDED_FOR', 
							'HTTP_FORWARDED', 
							'REMOTE_ADDR'
						);
		foreach ( $request as $key ) {
			if ( array_key_exists( $key, $_SERVER) === true ) {
				$getip = explode( ', ', $_SERVER[$key] );
				foreach ( $getip as $ip ) {
					$ip = trim( $ip ); // just to be safe
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
						return $ip;
					}
				}
			}
		}
		return false;
	}
	
	public static function isBot() {
		return (
			isset( $_SERVER['HTTP_USER_AGENT'] )
			&& preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
		);
	}
}