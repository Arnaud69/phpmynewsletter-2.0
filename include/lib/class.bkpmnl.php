<?php
class BackupMySQL extends mysqli {
	protected $dossier;
	protected $nom_fichier;
	protected $gz_fichier;
	public function __construct($options = array()) {
		$default = array(
			'host' => ini_get('mysqli.default_host'),
			'username' => ini_get('mysqli.default_user'),
			'passwd' => ini_get('mysqli.default_pw'),
			'dbname' => '',
			'port' => ini_get('mysqli.default_port'),
			'socket' => ini_get('mysqli.default_socket'),
			'dossier' => './',
			'nbr_fichiers' => 5,
			'nom_fichier' => 'backup_pmnl',
			'prefixe' => 'ps_',
			'racine' => 'pmnl',
			'token' => false
		);
		$options = array_merge($default, $options);
		extract($options);
		@parent::__construct($host, $username, $passwd, $dbname, $port, $socket);
		if ( $this->connect_error) {
			$this->message('error','Erreur de connexion (' . $this->connect_errno . ') '. $this->connect_error);
			return;
		}
		$this->token = $token;
		if ( !($this->token)||$this->token=='') {
			$this->message('error','Erreur sur accès au script de sauvegarde');
			return;
		}
		$this->dossier = $dossier;
		if ( !is_dir($this->dossier)) {
			$this->message('error','Erreur de dossier &quot;' . htmlspecialchars($this->dossier) . '&quot;');
			return;
		}
		$this->nom_fichier = $nom_fichier .'-'. date('Ymd-His') . '-' . $token . '.sql.gz';
		$this->gz_fichier = @gzopen($this->dossier . $this->nom_fichier, 'w');
		if ( !$this->gz_fichier) {
			$this->message('error','Erreur de fichier &quot;' . htmlspecialchars($this->nom_fichier) . '&quot;');
			return;
		}
		$this->prefixe = $prefixe;
		$this->racine = $racine;
		$this->purger_fichiers($nbr_fichiers);
		if ( $this->sauvegarder())
			$this->message('success',$this->nom_fichier);
	}
	protected function message($level,$message = '&nbsp;') {
		$arr=array(
			'status'=>$level,
			'successmsg'=>$message
		);
		echo json_encode($arr, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
	protected function insert_clean($string) {
		$s1 = array( "\\"	, "'"	, "\r", "\n", );
		$s2 = array( "\\\\"	, "''"	, '\r', '\n', );
		return str_replace($s1, $s2, $string);
	}
	protected function sauvegarder() {
		$sql  = '--' ."\n";
		$sql .= '-- '. $this->nom_fichier ."\n";
		gzwrite($this->gz_fichier, $sql);
		$result_tables = $this->query('SHOW TABLE STATUS WHERE name LIKE "' . $this->prefixe . $this->racine . '%"');
		if ( $result_tables && $result_tables->num_rows) {
			while($obj_table = $result_tables->fetch_object()) {
				$sql  = "\n\n";
				$sql .= 'DROP TABLE IF EXISTS `'. $obj_table->{'Name'} .'`' .";\n";
				$result_create = $this->query('SHOW CREATE TABLE `'. $obj_table->{'Name'} .'`');
				if ( $result_create && $result_create->num_rows) {
					$obj_create = $result_create->fetch_object();
					$sql .= $obj_create->{'Create Table'} .";\n";
					$result_create->free_result();
				}
				$result_insert = $this->query('SELECT * FROM `'. $obj_table->{'Name'} .'`');
				if ( $result_insert && $result_insert->num_rows) {
					$sql .= "\n";
					while($obj_insert = $result_insert->fetch_object()) {
						$virgule = false;
						$sql .= 'INSERT INTO `'. $obj_table->{'Name'} .'` VALUES (';
						foreach($obj_insert as $val) {
							$sql .= ($virgule ? ',' : '');
							if ( is_null($val)) {
								$sql .= 'NULL';
							} else {
								$sql .= '\''. $this->insert_clean($val) . '\'';
							}
							$virgule = true;
						}
						$sql .= ')' .";\n";
						
					}
					$result_insert->free_result();
				}
				gzwrite($this->gz_fichier, $sql);
			}
			$result_tables->free_result();
		}
		if ( gzclose($this->gz_fichier))
			return true;
	}
	protected function purger_fichiers($nbr_fichiers_max) {
		$fichiers = array();
		if ( $dossier = dir($this->dossier)) {
			while(false !== ($fichier = $dossier->read())) {
				if ( $fichier != '.' && $fichier != '..') {
					if ( is_dir($this->dossier . $fichier)) {
						continue;
					} else {
						if ( preg_match('/\.gz$/i', $fichier)) {
							$fichiers[] = $fichier;
						}
					}
				}
			}
			$dossier->close();
		}
		$nbr_fichiers_total = count($fichiers);
		if ( $nbr_fichiers_total >= $nbr_fichiers_max) {
			rsort($fichiers);
			for($i = $nbr_fichiers_max; $i < $nbr_fichiers_total; $i++) {
				unlink($this->dossier . $fichiers[$i]);
			}
		}
	}
	
}