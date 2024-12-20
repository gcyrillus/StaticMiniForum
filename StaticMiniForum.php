<?php if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			StaticMiniForum
		*
		* @CMS required			PluXml 
		*
		* @version			3.1.0
		* @date				09/12/2024
		* @author 			G.Cyrille
		░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
		░       ░░  ░░░░░░░  ░░░░  ░  ░░░░  ░░      ░░       ░░░      ░░  ░░░░░░░        ░░      ░░░░░   ░░░  ░        ░        ░
		▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒  ▒▒  ▒▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒    ▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒
		▓       ▓▓  ▓▓▓▓▓▓▓  ▓▓▓▓  ▓▓▓    ▓▓▓  ▓▓▓▓  ▓       ▓▓  ▓▓▓▓  ▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓      ▓▓▓▓▓  ▓  ▓  ▓      ▓▓▓▓▓▓  ▓▓▓▓
		█  ███████  ███████  ████  ██  ██  ██  ████  █  ███████  ████  █  ██████████  ██████████  ████  ██    █  ██████████  ████
		█  ███████        ██      ██  ████  ██      ██  ████████      ██        █        ██      ██  █  ███   █        ████  ████
		█████████████████████████████████████████████████████████████████████████████████████████████████████████████████████████
	**/
	class StaticMiniForum extends plxPlugin {
		
		
		
		const BEGIN_CODE = '<?php' . PHP_EOL;
		const END_CODE = PHP_EOL . '?>';
		public $lang = '';
		public $colors = array(
		"colors1" => array("#230571","#8e006d","#0040a4","#00b6b6","#00cbec","#00a777","#ff9300","#d7002c","#eb8096","#f5c0cb"),
		"colors2" => array("#ff3130","#ff8800","#ffca00","#4cd95d","#34b2dc","#0088ff","#a54eca","#e0008e","#757575","#0dff00"),
		"colors3" => array("#802d2f","#ff595e","#ff924c","#ffca3a","#c5ca30","#8ac926","#52a675","#1982c4","#4267ac","#6a4c93"),
		"colors4" => array("#ff595e","#ff924c","#ffca3a","#c5ca30","#8ac926","#52a675","#1982c4","#4267ac","#6a4c93","#d677b8"),
		"colors5" => array("#ff595e","#ff924c","#ffca3a","#c5ca30","#8ac926","#52a675","#1982c4","#4267ac","#6a4c93","#b5a6c9"),
		"colors6" => array("#3646c4","#dd2a18","#23ac07","#241517","#de55aa","#922e1e","#e8cd23","#291465","#1a6a06","#660f0f"),
		"colors7" => array("#ff5959","#ff8922","#e6a800","#2ad607","#00b5d9","#5c59ff","#c900c6","#ff00f2","#7d4545","#080707"),
		"colors8" => array("#16171c","#f7d826","#eb870d","#8804e0","#d4210d","#9c0000","#82e80d","#00ad2b","#36b4fc","#1628cf"),
		"colors9" => array("#ff3c3c","#2e69ff","#0fff1f","#fffb00","#06f7ff","#ad16ff","#ff8cd7","#17dbbb","#ff08a0","#ff9021"),
		);
		public $HTMLletterBadge='<b title="#member" class="badge_miniForum" style="--bgColor:#bgColor;--ftSize:#sizech">#ucWL</b>';
		# Tableau des profils
		public $memberProfils = array();
		public $version = 'V 3.1.0';
		public $lastSubjects = PLX_ROOT.'data/statiques/last-threads.json';
		public $lastReplies = PLX_ROOT.'data/statiques/last-threads-replies.json';	
		public $moderateSubscription;
		
		
		public function __construct($default_lang) {
			# appel du constructeur de la classe plxPlugin (obligatoire)
			parent::__construct($default_lang);			
			
			# droits pour accèder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);	
			
			$captcha = $this->getParam('captcha') ==''   ?   '0' : $this->getParam('captcha') ;
			$statics = $this->getParam('statics') ==''   ?   '0' : $this->getParam('statics') ;	
			$this->moderateSubscription = $this->getParam('moderateSubscription') ==''   ?   '1' : $this->getParam('moderateSubscription') ;			
			
			# Declaration des hooks		
			$this->addHook('AdminPrepend', 'AdminPrepend');	
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
			$this->addHook('AdminAuthPrepend', 'AdminAuthPrepend');
			$this->addHook('AdminAuthTop', 'AdminAuthTop');			
			$this->addHook('AdminAuthEndHead', 'AdminAuthEndHead');
			$this->addHook('AdminAuthEndBody', 'AdminAuthEndBody');
			$this->addHook('AdminUsersTop', 'AdminUsersTop');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('ThemeEndBody','ThemeEndBody');
			$this->addHook('wizard', 'wizard');
			$this->addHook('plxShowLastArtList','plxShowLastArtList');
			$this->addHook('plxShowStaticContent', 'plxShowStaticContent');	
			$this->addHook('lastThreads','lastThreads');
			// 'plxShowLastComList'
			$this->memberProfils = array(			
			0 => $this->getLang('L_PROFIL_ADMIN'),
			1 => $this->getLang('L_PROFIL_MANAGER'),
			2 => $this->getLang('L_PROFIL_MODERATOR'),
			3 => $this->getLang('L_PROFIL_EDITOR'),
			4 => $this->getLang('L_PROFIL_WRITER'),
			5 => $this->getLang('L_PROFIL_FORUM')
			);
			if(!file_exists($this->lastSubjects) || strlen(trim(file_get_contents($this->lastSubjects)))<1)  { 
                touch($this->lastSubjects);
                file_put_contents($this->lastSubjects,'[]');
			}
            if(!file_exists($this->lastReplies) || strlen(trim(file_get_contents($this->lastReplies)))<1)  { 
                touch($this->lastReplies);
                file_put_contents($this->lastReplies,'[]');
			}
			
			
			
			
			
		}
		
		public function plxShowLastArtList() {
			if($this->getParam('notification') == 1) {
				echo '<?php include(PLX_ROOT."plugins/StaticMiniForum/assets/plxShowLastArtList.php"); ?>' ;
				echo '</ul>';
				echo $this->lastThreads();
				echo '<?php $format=""; ?>';
			}
		}
		#code à exécuter à la désactivation du plugin
        public function OnDeactivate() {		
			#desactive les comptes visiteurs	
			$deactivateVisitors=simplexml_load_file(PLX_ROOT.PLX_CONFIG_PATH."users.xml") or die("Error: erreur fichier users.xml");
			foreach($deactivateVisitors->children() as $users) {
				if ( $users['profil'] =='5') {
					$users->attributes()->active = '0';
				}
			}
			$deactivateVisitors->asXml(PLX_ROOT.PLX_CONFIG_PATH."users.xml");
			
		}
		
        #code à exécuter à l’activation du plugin 
        public function OnActivate() { 
			
			$_SESSION['justactivated'.basename(__DIR__)] = true;
			
			#réactive compte visiteur s'il y en a
			$reactivateVisitors=simplexml_load_file(PLX_ROOT.PLX_CONFIG_PATH."users.xml") or die("Error: erreur fichier users.xml");
			foreach($reactivateVisitors->children() as $users) {
				if (( $users['profil'] =='5') and ( $users['active'] =='0') ) {
					$users->attributes()->active = '1';
				}
			} 
			$reactivateVisitors->asXml(PLX_ROOT.PLX_CONFIG_PATH."users.xml");			
		}	
		
		public function AdminAuthEndHead(){
		?>
        <link rel="stylesheet" type="text/css" href="<?php echo PLX_PLUGINS . __CLASS__ ?>/css/auth.css" media="screen" />
        <?php
			if(isset($_SESSION['pageRequest'])) {
			?>
			<style>			
				h1.h5.text-center strong {display: none}
				h1.h5.text-center::before {content:'CONNEXION AU FORUM';display:block;}
			</style>
			<?php
			}
		}
        public function AdminAuthPrepend(){#save new on post
		echo '<?php #' . __CLASS__ . '::' . __FUNCTION__ . PHP_EOL; ?>
        #New user
        if(!empty($_POST) AND !empty($_POST['update']) AND !empty($_POST['name']) AND !empty($_POST['wall-e']) AND !empty($_POST['password']) AND !empty($_POST['login']) AND empty($_POST['email'])) {
        $posts = array();
        $posts['userNum'] = array();
        $posts['newuser'] = 'true';
        $posts['update'] = $_POST['update'];
        # On récupère le dernier identifiant
        $a = array_keys($plxAdmin->aUsers);
        rsort($a);
        $new_userid = str_pad($a['0']+1, 3, "0", STR_PAD_LEFT);
        # On integre le nouvel utilisateur
        $posts['userNum'][] = $new_userid;
        $posts[$new_userid.'_newuser'] = 'true';
        $posts[$new_userid.'_name'] = $_POST['name'];
        $posts[$new_userid.'_infos'] = '';
        $posts[$new_userid.'_login'] = $_POST['login'];
        $posts[$new_userid.'_password'] = $_POST['password'];
        $posts[$new_userid.'_email'] = $_POST['wall-e'];#Real mail field ;)
        $posts[$new_userid.'_profil'] = PROFIL_FORUM;
        $posts[$new_userid.'_active'] = <?= $this->moderateSubscription ?>;
        $_SESSION['user'] = '1000';#need in editUsers()
        #On enregistre le nouvel utilisateur
        if(!$plxAdmin->editUsers($posts)) {
        $msgSignUp = $_SESSION['error'];
        unset($_SESSION['error'], $_POST['login'], $_POST['password']);
        $errorSignUp = 'alert red';
        }
        unset($_SESSION['user']);#remove fake
			if($posts[$new_userid.'_active'] =='0') {
				echo '<?php $this->lang('L_SUBSCRIPTION_PENDING') ?>';
				unset($_SESSION['profil']);
				exit;
			}
        }
        ?><?PHP
		}
        public function AdminAuthBegin(){#get != 'newMember=1' (<5.8 == AdminAuthTop)
		echo '<?php '; ?>
        if($plxAdmin->get == 'newMember=1'){
        ob_start();
        }
        ?><?php
		}
        public function AdminAuthEndBody(){#get == 'new=1' or all
			echo self::BEGIN_CODE;
            echo '$label='.intval($this->getParam('label')).';';
		?>
        $formSignUpForum = '';
        if($plxAdmin->get == 'newMember=1') {# get == newMember=1
        ob_end_clean();
        define('L_BACK_SIGNUP_FORUM', '<?php $this->lang('L_BACK') ?>');
        define('L_SHIFT_SIGNUP_FORUM', '<?php $this->lang('L_SHIFT') ?>');
		define('L_PASSWORD_SIGNUP_FORUM', '<?php $this->lang('L_PASSWORD') ?>');
		include PLX_PLUGINS . '<?=__CLASS__?>' . DIRECTORY_SEPARATOR . 'form.signup.forum.php';
		unset($plxAdmin->get); 
		exit;
		}
		<?php
            echo self::END_CODE;
		}		
		
		#ajout constante profil VIP
		public function AdminPrepend() {
			if(!isset($plxShow->plxMotor->plxPlugins->aPlugins['vip_zone'])) {		
				echo self::BEGIN_CODE;
			?>
			const PROFIL_FORUM = 5;	
			if(isset($_SESSION['profil']) && $_SESSION['profil'] > 4 && empty($_GET['d']) )  {
			if(isset($_SESSION['pageRequest'])) {
			$loc= $_SESSION['pageRequest'];
			unset($_SESSION['pageRequest']);
			echo header("location: ".$loc);
			die;
			}
			else {
			echo header("location: /");
			die;
			}
			}
			<?php
				echo self::END_CODE;
			}
		}		
		#On ajoute un profil utilisateur
        public function AdminUsersTop() {
			echo self::BEGIN_CODE;
		?>
		$plxMotor = plxMotor::getInstance();
		$plugin = $plxMotor->plxPlugins->aPlugins['<?= __CLASS__ ?>'];
		$VIP_Profil = $plugin->getLang('L_PROFIL_FORUM');				
		# Tableau des profils
		$aProfils[PROFIL_FORUM] = $VIP_Profil;
		<?php
            echo self::END_CODE;
		} 
		
	    #On renvoi le membre vers le forum demandé aprés authentification
        public function AdminTopEndHead() {
			if(!isset($plxShow->plxMotor->plxPlugins->aPlugins['vip_zone'])) {
				echo self::BEGIN_CODE;
			?>				 
			if(isset($_SESSION['profil']) && $_SESSION['profil'] == 5 && empty($_GET['d']) )  {
			if(isset($_SESSION['pageRequest'])) {
			$loc= $_SESSION['pageRequest'];
			unset($_SESSION['pageRequest']);
			echo header("location: ".$loc);
			die;
			}
			else {
			echo header("location: /");
			die;
			}
			}
			<?php
				echo self::END_CODE;
			}
		}
		
		public function ThemeEndBody() {
			echo '<?php  if($plxMotor->mode=="static") echo\'<script src="'.PLX_PLUGINS.__CLASS__.'/js/loadForm.js"></script>\'.PHP_EOL; ?>	';		
		}
		
		public function ThemeEndHead() {
			echo '<?php 
			if(file_exists(PLX_ROOT.$plxShow->plxMotor->aConf[\'racine_themes\'].$plxShow->plxMotor->aConf[\'style\'].\'/css/custom-'.__CLASS__.'\'))
			echo \'	<link href="\'.PLX_ROOT.$plxShow->plxMotor->aConf[\'racine_themes\'].$plxShow->plxMotor->aConf[\'style\'].\'/css/custom-'.__CLASS__.'.css" rel="stylesheet" type="text/css" />\'."\n"  ; 
			
			echo \'	<link href="'.PLX_PLUGINS.__CLASS__.'/css/site.css" rel="stylesheet" type="text/css" />\';
			?>';
			echo'<?php	
			if (!isset($_SESSION[\'profil\']) && ($plxMotor->mode === \'static\' )) $_SESSION[\'pageRequest\']= $_SERVER[\'REQUEST_URI\']; 
			
			?>';
			#gestion multilingue
			if(defined('PLX_MYMULTILINGUE')) {		
				$plxMML = is_array(PLX_MYMULTILINGUE) ? PLX_MYMULTILINGUE : unserialize(PLX_MYMULTILINGUE);
				$langues = empty($plxMML['langs']) ? array() : explode(',', $plxMML['langs']);
				$string = '';
				foreach($langues as $k=>$v)	{
					$url_lang="";
					if($_SESSION['default_lang'] != $v) $url_lang = $v.'/';
					$string .= 'echo "\\t<link rel=\\"alternate\\" hreflang=\\"'.$v.'\\" href=\\"".$plxMotor->urlRewrite("?'.$url_lang.$this->getParam('url').'")."\" />\\n";';
				}
				echo '<?php if($plxMotor->mode=="'.$this->getParam('url').'") { '.$string.'} ?>';
			}
			// ajouter ici aux besoins vos propre codes (insertion balises link, script , ou autre)
		}
		
		/**
			* Méthode qui affiche un message si le plugin n'a pas la langue du site dans sa traduction
			* Ajout gestion du wizard si inclus au plugin
			* @return	stdio
			* @author	Stephane F / G.Cyrille
		**/
		public function AdminTopBottom() {			
			echo '<?php
			
			error_reporting(E_ALL ^ E_NOTICE );
			$file = PLX_PLUGINS."'.$this->plug['name'].'/lang/".$plxAdmin->aConf["default_lang"].".php";
			if(!file_exists($file)) {
			echo "<p class=\\"warning\\">'.basename(__DIR__).'<br />".sprintf("'.$this->getLang('L_LANG_UNAVAILABLE').'", $file)."</p>";
			plxMsg::Display();
			}
			?>';
			
			# affichage du wizard à la demande
			if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
			# fermeture session wizard
			if (isset($_SESSION['justactivated'.basename(__DIR__)])) {
				unset($_SESSION['justactivated'.basename(__DIR__)]);
				$this->wizard();
			}			
		}
		
		/** 
			* Méthode wizard
			* 
			* Descrition	: Affiche le wizard dans l'administration
			* @author		: G.Cyrille
			* 
		**/
		# insertion du wizard
		public function wizard() {
			# uniquement dans les page d'administration du plugin.
			if(basename(
			$_SERVER['SCRIPT_FILENAME']) 			=='parametres_plugins.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='parametres_plugin.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='plugin.php'
			) 	{	
				include_once(PLX_PLUGINS.__CLASS__.'/lang/'.$this->default_lang.'-wizard.php');
			}
		}
		
		/** 
			* Méthode plxShowStaticInclude
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		public function plxShowStaticContent() {
			# insertion des commentaires et formulaire			
			echo self::BEGIN_CODE;
		?>
		ob_start();
		if(isset($_GET['backHome'])) {
		$message=$_SESSION['msgcom'];
		$_SESSION = array();
		$_SESSION['msgcom']=$message;
		header('location: index.php?static'.$this->staticId().'/'.$this->staticTitle());
		exit;
		}
		include( PLX_PLUGINS.'StaticMiniForum/assets/commentaires.php');
		$output .= ob_get_clean();
		<?php
			echo self::END_CODE;
		}
		####################################
		######## Fonctions internes ########
		####################################
		
		/**
			* Methode getFileDatas
			*
			* creer le fichier si absent
			*
			* charge et decode json
			*
			* @author		: Gcyrillus
			*
		**/
		public function getFileDatas($file) {
			$file = PLX_ROOT.$file;
			if(!file_exists($file) || strlen(trim(file_get_contents($file)))<1)  { 
				touch($file);
				file_put_contents($file,'[]');
			}
			return json_decode(file_get_contents($file), true);
			
		}
		
		
		/**
			* Methode SaveJsonDatas
			*
			* Enregistre les données au format json
			*
			* @author		: Gcyrillus
			*
		**/
		public function saveJsonDatas($file, $datas) {
			file_put_contents($file, json_encode($datas,true|JSON_PRETTY_PRINT) );			
		}
		/** 
			* Méthode HTMLbadge
			* 
			* Descrition	: Extrait capitale(s) et/ ou premiers lettres du nom du membre
			*				  Extrait une couleur $colors
			*				  creer un badge avec $HTMLletterBadge 
			* @author		: Gcyrillus
			* 
		**/		
		public 	function HTMLbadge($string,$patternColors, $HTMLletterBadge){
			$av='';
			$num4color=1;
			$alphabet = range('A', 'Z');
			$letters=preg_split("/(?=[A-Z])/", ucwords($string),-1);
			foreach($letters as $k) {
				$av .= mb_substr(trim($k), 0,1);
				$num4color = ($num4color + array_search(strtoupper($k), $alphabet));
			}
			$dataReplace=array('#bgColor','#size','#ucWL','#member');
			$replaceBy =array($patternColors[substr($num4color, -1)],3.5 / strlen(substr($av,0,3)),substr($av,0,3),$string);
			return str_replace($dataReplace, $replaceBy, $HTMLletterBadge).PHP_EOL;
		}
		
		/** 
			* Méthode patternToRGB
			* 
			* Descrition	: renvoi une image pour chaque pattern de couleur
			*
			* @author		: Gcyrillus
			* 
		**/		
		public function patternToRGB($pattern) {
			$bg=$pattern[0];
			$pattern =array_reverse($pattern);
			$pattern[]=$bg;
			$image = imagecreatetruecolor(200,20);
			$step=200;
			
			# ajout block couleurs
			foreach($pattern as $block ) {
				list($r,$g,$b) = array_map('hexdec', str_split(ltrim($block,'#'), 2));
				imagefilledrectangle($image, $step , 0 , 20, 20 ,	imagecolorallocate($image,$r,$g,$b ));
				$step = $step-20;
			}
			# renvoi l'image dans une balise <img src="data:image/image/png;base64,...">
			ob_start();
			imagePng($image);
			$base_64 = base64_encode(ob_get_clean());
			imagedestroy($image);			
			return '<img src="data:image/image/png;base64,'.$base_64.'">';
		}
		
		
		/* hook 
			*
			* capte les dernieres action du forum
			*
		*/
		public function lastThreads() {
			# récupération d'une instance de plxShow
			$plxShow = plxShow::getInstance();
			# récupération d'une instance de plxMotor
			$plxMotor = plxMotor::getInstance();
			echo '<div class="lastThreads">';
			# recupere les sujets
			$threadsS = glob(PLX_ROOT.$plxMotor->aConf['racine_statiques'].'thread*[!reply].json');
			$forums=array();
			foreach($threadsS as $subject) {
			
				# lien vers le forum 
				preg_match_all('!\d!', $subject, $matches);
				$statid=implode('',$matches[0]);
				$statnum =str_pad($statid,3,'0',STR_PAD_LEFT);
				
				#si la page statique n'existe pas, on zappe
				if(!isset($plxMotor->aStats[$statnum])) {
					//unlink($subject);
					continue;
					} 
				
				# nettoyage ratrapage markdown dans les liens
				$patterns= array(
				0=>'/[#]/',
				1=>'/`/',
				2=>'/~/',
				3=>'/\*/',
				4=>'/___/',
				5=>'/|/',
				6=>'/|/',
				7=>'/:-/',
				8=>'/-:/',
				9=>'/&039;/',
				);
				$patternsRemoved= array(
				0=>'',
				1=>'',
				2=>'',
				3=>'',
				4=>'',
				5=>'',
				6=>'',
				7=>'',
				8=>'',
				9=>'&#039;',
				);
				
				$statiqueUrl= $plxMotor->urlRewrite('?static'.$statid.'/'.$plxMotor->aStats[$statnum]['url']);
				$statiqueName = $plxMotor->aStats[$statnum]['name'];

				# récupération des sujets
				$content = json_decode( file_get_contents($subject),true );
				$active='';
				if($plxMotor->mode == 'static' && $statid == $plxShow->staticId()) $active='class="active"';
				if(count($content)>0)
				$forumLink[]='			<li '.$active.'><a href="'.$statiqueUrl.'#forum_posts">'.$statiqueName.'</a></li>'.PHP_EOL;
				
				# on zappe les forums vides
				if(!is_array($content)) continue;
				$content = array_reverse($content);
				foreach($content as $k => $sujet) {
				if($sujet['content'] =='')continue;
					# création des liens vers sujet
					$id='#thread-'.$sujet['num'];
					$url='<li><a href="'.$statiqueUrl.$id.'" title="'.$sujet['subject'].': '.
					plxUtils::truncate(preg_replace($patterns, $patternsRemoved, $sujet['subject']), 45, '...', true, false)
					.'"><b>'.$sujet['name'].'</b>: '.
					plxUtils::truncate(preg_replace($patterns, $patternsRemoved, $sujet['subject']), 25, '...', true, false)
					.'</a></li>';
					#stockage par  la date
					$date=explode('-',$sujet['date'] );
					$forumThreads[$date[2].$date[1].$date[0]][$id] = $url; 
				}		
			}
			
			
			# Affichage vers les autres forum si plus d'un.
			$otherForums='';
			if($this->getParam('otherForums') && $this->getParam('otherForums') == 1 && count($forumLink) >1){
			
			$otherForums='
	<h4>'.$this->getLang('L_OTHER_FORUMS').'</h4>
		<ul  class="other-forum-list unstyled-list">';
				foreach($forumLink as $link) {
					$otherForums .=$link;
					}
				$otherForums .='
		</ul>';
				}

			# Affichage des derniers sujets
			$derniersujet='';

			krsort($forumThreads);
			$i=50;
			if($this->getParam('notificationNb')) {
				$limit= $this->getParam('notificationNb');
				$i = 0;
				} 
			foreach($forumThreads as $date => $id){
			krsort($id);
				foreach($id as $li){
					if($i >= $limit) break;
					$derniersujet .= $li;
					++$i;
					}
				}
				if($derniersujet=='') $derniersujet= '<li>'.$this->getLang('L_NONE').'</li>';
			echo '<h3>'.$this->getLang('L_FROM_FORUM').' <span class="nbMembers">'.count($plxMotor->aUsers).' '.$this->getLang('L_USERS').'</span></h3>
			<h4>'.$this->getLang('L_LAST_SUBJECT').'</h4>
			<ul class="forum-subject-list unstyled-list">'.$derniersujet.'</ul>';
			
			# récupére les réponses
			$threadsR = glob(PLX_ROOT.$plxMotor->aConf['racine_statiques'].'/thread*[reply].json');
			//var_dump($threadsR);
			
			foreach($threadsR as $response) {
			
				# lien vers le forum 
				preg_match_all('!\d!', $response, $matches);
				$statid=implode('',$matches[0]);
				$statnum =str_pad($statid,3,'0',STR_PAD_LEFT);
				
				#si la page statique n'existe pas, on zappe
				if(!isset($plxMotor->aStats[$statnum])) {
				// unlink($response);
				echo '<p>NOP'.$response.'</p>';
					continue;
				}
				
				$statiqueUrl= $plxMotor->urlRewrite('?static'.$statid.'/'.$plxMotor->aStats[$statnum]['url']);
				$statiqueName = $plxMotor->aStats[$statnum]['name'];

				# récupération des reponses
				$reponses = json_decode( file_get_contents($response),true );
				
				# on zappe les forums vides
				if(!is_array($reponses)) continue;
				$content = array_reverse($reponses);
				foreach($reponses as $k => $reponse) {
				if($reponse['content'] =='')continue;
					# création des liens vers sujet
					$id='&replies='.$reponse['subject'].'#id-'.$reponse['num'];
					
					$url='<li><a href="'.$statiqueUrl.$id.'" title="'.$statiqueName.': '.
					plxUtils::truncate(preg_replace($patterns, $patternsRemoved, $reponse['content']), 75, '...', true, false)
					.'"><b>'.
					$reponse['name']
					.'</b>: '.plxUtils::truncate(preg_replace($patterns, $patternsRemoved, $reponse['content']), 25, '...', true, false).'</a></li>';
					#stockage par  la date
					$date=explode('-',$reponse['date'] );
					$forumThreadsR[$date[2].$date[1].$date[0]][$id] = $url; 
				}		
			}

			# Affichage des dernieres réponses
			$derniersujet='';

			krsort($forumThreadsR);
			$i=50;
			if($this->getParam('notificationNb')) {
				$limit= $this->getParam('notificationNb');
				$i = 0;
				} 
			foreach($forumThreadsR as $date => $id){
			krsort($id);
				foreach($id as $li){
					if($i >= $limit) break;
					$derniersujet .= $li;
					++$i;
					}
				}
				if($derniersujet=='')$derniersujet= '<li>'.$this->getLang('L_NONE_IN_LIST').'</li>';
			echo '
			<h4>'.$this->getLang('L_LAST_REPLY').'</h4>
			<ul class="forum-response-list unstyled-list">'.$derniersujet.'</ul>
			'.$otherForums.'
			</ul>'.$this->getModos().'
			</div>';			
			
			
		}
		
		public function getModos() {
		
			# récupération d'une instance de plxMotor
			$plxMotor = plxMotor::getInstance();
			
			$profilModo= array("0","1","2");
			$modos ='<p class="modos">'.$this->getLang('L_FORUM_MODERATOR').': ';
			foreach($plxMotor->aUsers as $user => $data) {
			if(in_array($data['profil'],$profilModo))
					$modos .= '<span><b>'.$data['name'].'</b>: '.$this->memberProfils[$data['profil']].' </span> ';
				}
				return $modos;
			
			#
			
			
		}
	}													