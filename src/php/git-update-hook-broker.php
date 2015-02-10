<?php

$instance = Git_Update_Broker::get_instance();
$instance->process();

class Git_Update_Broker {
	protected static $_instance;
	protected $_branch = array();
	protected $_has_payload = false;
	protected $_debug = true;
	protected $_log = true;

	// Jenkins server url. 
	protected $_build_url = 'http://jenkins-server/buildByToken/buildWithParameters';

	public static function get_instance() {
		if ( empty (self::$_instance) ) {
			self::$_instance = new Git_Update_Broker();
		}
		return self::$_instance;
	}
	public function __construct() {
		if ( isset( $_POST['payload'] ) ) {
			$payload = json_decode( $_POST['payload'], false );
			if ( isset( $payload ) ) {
				$this->_has_payload = true;
				if ( !empty( $payload->commits ) ) {
					foreach ( $payload->commits as $commit ) {
						if ( empty($commit->branch) ) {
							continue;
						}
						$branch = $commit->branch;
						if ( !empty( $_GET['branch'] ) ) {
							if ( $_GET['branch'] === $branch ) {
								$this->_branch[$commit->branch] = array( 'branch' => $branch, 'email' => $this->extract_email($commit->raw_author,'') );
							}
							continue;
						}
						elseif ( !empty($commit->branch) ) {
							if ( preg_match('@^feature/@',$commit->branch) || $commit->branch == 'qa') {
								$branch = str_replace('feature/','',$commit->branch);
								if ( strpos( $branch, '/' ) === false ) {
									$this->_branch[$commit->branch] = array('branch' => $branch, 'email' => $this->extract_email($commit->raw_author,'') );
								}
							} elseif ( isset( $_GET['master'] ) && $commit->branch == 'master' ) {
								$this->_branch[$commit->branch] = array('branch' => $_GET['master'], 'email' => $this->extract_email($commit->raw_author,'') );
							}
						}
					}
				} else {
					// no commit, trigger must be coming from merge
					if ( isset( $_GET['master'] ) ) {
						$this->_branch['master'] = array('branch' => $_GET['master'], 'email' => '' );
					}
				}
			}
			if ( isset( $_GET['buildurl'] ) ) {
				$this->_build_url = $_GET['buildurl'];
				unset( $_GET['buildurl'] );
			}
			if ( isset( $_GET['debug'] ) ) {
				$this->_debug = $_GET['debug'] == 'true';
				unset( $_GET['debug'] );
			}
			if ( isset( $_GET['log'] ) ) {
				$this->_log = $_GET['log'] == 'true';
				unset( $_GET['log'] );
			}
			$this->_build_url .= "?". http_build_query( $_GET );
			if ($this->_debug) {
				$this->write_log( $_POST['payload'] );
			}
		}
	}

	public function process() {
		if ( $this->_has_payload ) {
			if ( !empty($this->_branch) ) {
				foreach ( $this->_branch as $branch => $value ) {
					$this->notify_build( $value['branch'], $value['email'] );
				}
			}
			else {
				// branch delete, no other information provide.
				// Disable for now, need jenkins script ready before notify build
				//$this->notify_build();
			}
		}
	}

	protected function extract_email ( $data, $default = false ) {
		if ( preg_match('/\<([^@]*@[^@]*\.[^@]*)\>/', $data, $matches ) ) {
			return $matches[1];
		}
		return $default;
	}

	protected function get_build_url( $branch, $email = '' ) {
		$url = str_replace( '%24%7Bbranch%7D', $branch, $this->_build_url );
		$url = str_replace( '${branch}', $branch, $url );
		$url = str_replace( '%24%7Bemail%7D', $email, $url );
		$url = str_replace( '${email}', $email, $url );
		return $url;
	}

	protected function notify_build( $branch = '', $email = '' ) {
		$url = $this->get_build_url( $branch, $email );
		$result = $this->send_request( $url );
		if ( empty($result) || $result['status'] != 200 ) {
			$this->write_log( sprintf("Error querying: %s", $url ) );
		}
		else {
			$this->write_log( sprintf("Notified: %s\n%s\n", $url, $result['data']) );
		}
	}

	protected function send_request( $url )
	{
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_NOBODY, 0);
			curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

			$data = curl_exec($ch);
			$status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close($ch);
			return array('data'=>$data,'status'=>$status);
		}
		catch(Exception $e)
		{

		}
		return false;
	}

	protected function write_log( $data ) {
		if ( !$this->_log ) return;
		$file = sys_get_temp_dir() ."/git-update-hook-broker.log";
		$bufs = sprintf("%s\n%s\n\n", gmdate('Y-m-d\TH:i:s\Z'), $data);
		file_put_contents($file, $bufs , FILE_APPEND);
		if ( $this->_debug ) {
			echo "<pre>{$file}\n{$bufs}\n</pre>";
		}
	}
}

// EOF