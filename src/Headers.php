<?php

namespace ghopunk\Helpers\Server;

use ghopunk\Helpers\Server\Is;

class Headers {
	
	const MINUTE_IN_SECONDS = 60;
	const HOUR_IN_SECONDS 	= 60 * 60;
	const DAY_IN_SECONDS 	= 24 * 60 * 60;
	const WEEK_IN_SECONDS 	= 7 * 24 * 60 * 60;
	const MONTH_IN_SECONDS 	= 30 * 24 * 60 * 60;
	const YEAR_IN_SECONDS 	= 365 * 24 * 60 * 60;

	public static function getCodeDescription( $code ) {
		$code = abs( intval( $code ) );
		$abs_header_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
			
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
		);
		if ( isset( $abs_header_to_desc[$code] ) )
			return $abs_header_to_desc[$code];
		else
			return '';
	}
	public static function setCode( $code ) {
		$description = self::getCodeDescription( $code );
		if ( empty( $description ) ){
			return;
		}
		if(isset($_SERVER['SERVER_PROTOCOL'])){
			$protocol = $_SERVER['SERVER_PROTOCOL'];
			if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol ){
				$protocol = 'HTTP/1.0';
			}
		} else {
			$protocol = 'HTTP/1.1';
		}
		$setCode = "$protocol $code $description";
		header( $setCode, true, $code );
	}
	
	public static function setCleanContentType( $extention='html' ){
		header('Connection: close');
		if ( function_exists( 'header_remove' ) ) {
			header_remove('Cache-Control');
			header_remove('Expires');
			header_remove('Pragma');
		} else {
			foreach ( headers_list() as $header ) {
				if ( 0 === stripos( $header, 'Cache-Control' ) ) {
					$headers['Cache-Control'] = '';
				} elseif ( 0 === stripos( $header, 'Expires' ) ) {
					$headers['Expires'] = '';
				} elseif ( 0 === stripos( $header, 'Pragma' ) ) {
					$headers['Pragma'] = '';
				}
			}
		}
		self::setContentType( $extention );
	}
	
	private static function getNocache() {
		$headers = array(
			'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
			'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
			'Pragma' => 'no-cache',
		);
		$headers['Last-Modified'] = false;
		return $headers;
	}
	public static function setNocache() {
		$headers = getNocache();
		unset( $headers['Last-Modified'] );
		if ( function_exists( 'header_remove' ) ) {
			header_remove( 'Last-Modified' );
		} else {
			foreach ( headers_list() as $header ) {
				if ( 0 === stripos( $header, 'Last-Modified' ) ) {
					$headers['Last-Modified'] = '';
					break;
				}
			}
		}
		foreach( $headers as $name => $field_value )
			header("{$name}: {$field_value}");
	}
	public static function setRetryAfter( $expires='1' ) {
		$expiresOffset = $expires * Headers::DAY_IN_SECONDS;
		header('Retry-After: '.gmdate( 'D, d M Y H:i:s', time() + $expiresOffset ) . ' GMT');
	}
	public static function setContentLanguage($lang) {
		header('Content-Language: '.$lang);
	}
	
	public static function getMimeList() {
		return [
			'atom'		=> 'application/atom+xml',
			'ecma'		=> 'application/ecmascript',
			'epub'		=> 'application/epub+zip',
			'gz'		=> 'application/gzip',
			'jar'		=> 'application/java-archive',
			'json'		=> 'application/json',
			'jsonld'	=> 'application/ld+json',
			'doc'		=> 'application/msword',
			'bin'		=> 'application/octet-stream',
			'so'		=> 'application/octet-stream',
			'pkg'		=> 'application/octet-stream',
			'ogx'		=> 'application/ogg',
			'pdf'		=> 'application/pdf',
			'rdf'		=> 'application/rdf+xml',
			'rsd'		=> 'application/rsd+xml',
			'rss'		=> 'application/rss+xml',
			'rtf'		=> 'application/rtf',
			'apk'		=> 'application/vnd.android.package-archive',
			'mpkg'		=> 'application/vnd.apple.installer+xml',
			'm3u8'		=> 'application/vnd.apple.mpegurl',
			'kml'		=> 'application/vnd.google-earth.kml+xml',
			'kmz'		=> 'application/vnd.google-earth.kmz',
			'tpl'		=> 'application/vnd.groove-tool-template',
			'xul'		=> 'application/vnd.mozilla.xul+xml',
			'xls'		=> 'application/vnd.ms-excel',
			'eot'		=> 'application/vnd.ms-fontobject',
			'ppt'		=> 'application/vnd.ms-powerpoint',
			'pot'		=> 'application/vnd.ms-powerpoint',
			'wps'		=> 'application/vnd.ms-works',
			'xps'		=> 'application/vnd.ms-xpsdocument',
			'odc'		=> 'application/vnd.oasis.opendocument.chart',
			'odb'		=> 'application/vnd.oasis.opendocument.database',
			'odf'		=> 'application/vnd.oasis.opendocument.formula',
			'odg'		=> 'application/vnd.oasis.opendocument.graphics',
			'odi'		=> 'application/vnd.oasis.opendocument.image',
			'odp'		=> 'application/vnd.oasis.opendocument.presentation',
			'ods'		=> 'application/vnd.oasis.opendocument.spreadsheet',
			'odt'		=> 'application/vnd.oasis.opendocument.text',
			'xlsx'		=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'docx'		=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'vsd'		=> 'application/vnd.visio',
			'wsdl'		=> 'application/wsdl+xml',
			'7z'		=> 'application/x-7z-compressed',
			'torrent'	=> 'application/x-bittorrent',
			'bz'		=> 'application/x-bzip',
			'arc'		=> 'application/x-freearc',
			'iso'		=> 'application/x-iso9660-image',
			'latex'		=> 'application/x-latex',
			'application'	=> 'application/x-ms-application',
			'exe'		=> 'application/x-msdownload',
			'dll'		=> 'application/x-msdownload',
			'com'		=> 'application/x-msdownload',
			'bat'		=> 'application/x-msdownload',
			'msi'		=> 'application/x-msdownload',
			'wmf'		=> 'application/x-msmetafile',
			'pub'		=> 'application/x-mspublisher',
			'rar'		=> 'application/x-rar-compressed',
			'swf'		=> 'application/x-shockwave-flash',
			'sql'		=> 'application/x-sql',
			'tar'		=> 'application/x-tar',
			'tcl'		=> 'application/x-tcl',
			'xpi'		=> 'application/x-xpinstall',
			'xhtml'		=> 'application/xhtml+xml',
			'xml'		=> 'application/xml',
			'xsl'		=> 'application/xml',
			'xslt'		=> 'application/xslt+xml',
			'midi'		=> 'audio/midi',
			'mp4a'		=> 'audio/mp4',
			'mpga'		=> 'audio/mpeg',
			'mp2'		=> 'audio/mpeg',
			'mp2a'		=> 'audio/mpeg',
			'mp3'		=> 'audio/mpeg',
			'oga'		=> 'audio/ogg',
			'ogg'		=> 'audio/ogg',
			'opus'		=> 'audio/ogg',
			'dts'		=> 'audio/vnd.dts',
			'dtshd'		=> 'audio/vnd.dts.hd',
			'aac'		=> 'audio/aac',
			'flac'		=> 'audio/x-flac',
			'mka'		=> 'audio/x-matroska',
			'wma'		=> 'audio/x-ms-wma',
			'wav'		=> 'audio/x-wav',
			'ttf'		=> 'font/ttf',
			'woff'		=> 'font/woff',
			'woff2'		=> 'font/woff2',
			'avif'		=> 'image/avif',
			'bmp'		=> 'image/bmp',
			'gif'		=> 'image/gif',
			'jpeg'		=> 'image/jpeg',
			'jpg'		=> 'image/jpeg',
			'jpe'		=> 'image/jpeg',
			'png'		=> 'image/png',
			'svg'		=> 'image/svg+xml',
			'tiff'		=> 'image/tiff',
			'tif'		=> 'image/tiff',
			'psd'		=> 'image/vnd.adobe.photoshop',
			'dwg'		=> 'image/vnd.dwg',
			'webp'		=> 'image/webp',
			'3ds'		=> 'image/x-3ds',
			'ico'		=> 'image/x-icon',
			'eml'		=> 'message/rfc822',
			'mime'		=> 'message/rfc822',
			'ics'		=> 'text/calendar',
			'css'		=> 'text/css',
			'csv'		=> 'text/csv',
			'html'		=> 'text/html',
			'htm'		=> 'text/html',
			'js'		=> 'text/javascript',
			'txt'		=> 'text/plain',
			'text'		=> 'text/plain',
			'log'		=> 'text/plain',
			'rtx'		=> 'text/richtext',
			'vcard'		=> 'text/vcard',
			'java'		=> 'text/x-java-source',
			'vcs'		=> 'text/x-vcalendar',
			'3gp'		=> 'video/3gpp',
			'3g2'		=> 'video/3gpp2',
			'h261'		=> 'video/h261',
			'h263'		=> 'video/h263',
			'h264'		=> 'video/h264',
			'jpgv'		=> 'video/jpeg',
			'jpm'		=> 'video/jpm',
			'jpgm'		=> 'video/jpm',
			'mp4'		=> 'video/mp4',
			'mpeg'		=> 'video/mpeg',
			'mpg'		=> 'video/mpeg',
			'mpe'		=> 'video/mpeg',
			'ogv'		=> 'video/ogg',
			'qt'		=> 'video/quicktime',
			'mov'		=> 'video/quicktime',
			'm4u'		=> 'video/vnd.mpegurl',
			'webm'		=> 'video/webm',
			'flv'		=> 'video/x-flv',
			'mkv'		=> 'video/x-matroska',
			'vob'		=> 'video/x-ms-vob',
			'wmv'		=> 'video/x-ms-wmv',
			'avi'		=> 'video/x-msvideo',
		];
	}
	
	public static function setContentType( $extention='html' ) {
		if( isset(self::getMimeList()[$extention]) ) {
			header('Content-Type: ' . self::getMimeList()[$extention] . '; charset=UTF-8');
		}
	}
	
	public static function setNoindex() {
		self::setRobot('noindex');
	}
	public static function setExpires( $expires=7 ) {
		$expiresOffset = $expires * Headers::DAY_IN_SECONDS;
		header( "Vary: Accept-Encoding" ); // Handle proxies
		header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + $expiresOffset ) . " GMT" );
	}
	public static function setXFrameOptions( $opt='SAMEORIGIN' ) {
		header('X-Frame-Options: '.$opt);
	}
	public static function setXContentTypeOptions( $opt='nosniff' ) {
		header('X-Content-Type-Options: '.$opt);
	}
	public static function setAccessControlAllowCredentials() {
		header('Access-Control-Allow-Credentials: true');
	}
	public static function setCanonical( $url ) {
		header('Link: <' . $url . '>; rel="canonical"');
	}
	
	public static function setCookie( $name, $value, $path='/', $expires=false ){
		$domain = $_SERVER['HTTP_HOST'];
		if( $expires === false ){
			$expires = time() + (3600 * 24 * 1);
		}
		if( PHP_VERSION_ID < 70300 ){
			$expiryTime = gmdate('D, d-M-Y H:i:s T', $expires);
			if( Is::httpsSite() ){
				$samesite = 'SameSite=None; Secure';
			} else {
				$samesite = 'SameSite=Lax;';
			}
			header( 'Set-Cookie: '.$name.'='.urlencode($value).'; path='.$path.'; domain='.$domain.'; expires='.$expiryTime.'; '.$samesite, false );
		} else {
			if( Is::httpsSite() ){
				$samesite = 'None';
			} else {
				$samesite = 'Lax';
			}
			$cookie_options = [
								'expires' 	=> $expires,
								'path' 		=> $path,
								'domain' 	=> $domain,
								'secure' 	=> Is::httpsSite(),
								'samesite' 	=> $samesite
							];
			return setcookie( $name, $value, $cookie_options );
		}
	}
	
	public static function removeCookie( $name, $value='', $path='/' ){
		$expires 	= time()-3600;
		$domain 	= $_SERVER['HTTP_HOST'];
		if(PHP_VERSION_ID < 70300){
			$expiryTime = gmdate('D, d-M-Y H:i:s T', $expires);
			if( Is::httpsSite() ){
				$samesite = 'SameSite=None; Secure';
			} else {
				$samesite = 'SameSite=Lax;';
			}
			header( 'Set-Cookie: '.$name.'=deleted; path='.$path.'; domain='.$domain.'; expires='.$expiryTime.'; '.$samesite );
		} else {
			if( Is::httpsSite() ){
				$samesite = 'None';
			} else {
				$samesite = 'Lax';
			}
			$cookie_options = [
								'expires' 	=> $expires,
								'path' 		=> $path,
								'domain' 	=> $domain,
								'secure' 	=> Is::httpsSite(),
								'samesite' 	=> $samesite
							];
			setcookie( $name, $value, $cookie_options );
		}
	}

	public static function setLocation( $url, $code=301 ) {
		header( "Location: $url", true, $code );
	}
	
	public static function setRobot( $arr = 'noindex' ) {
		header('X-Robots-Tag: ' . $arr);
	}
	
	public static function parseHeaders( $headers ){
		$head = array();
		if( !empty($headers) ) {
			if( !is_array($headers) ) {
				$headers = explode( "\n", $headers );
			}
			foreach( $headers as $k=>$v ){
				$t = explode( ':', $v, 2 );
				if( preg_match( "#Content-Type#i", $v, $out ) ) {
					$c_type	= explode( ';', $t[1] );
					$head['mime_type']	= trim($c_type[0]);
				} elseif( preg_match( '#filename="([^"]+)"#i', $v, $out ) ) {
					$head['filename']	= trim($out[1]);
				} elseif( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) ) {
					$head['response_code'] = intval($out[1]);
				} elseif( isset( $t[1] ) ) {
					$head[ trim($t[0]) ]	= trim( $t[1] );
				}
			}
		}
		return $head;
	}
}