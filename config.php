<?php
	if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			StaticMiniForum
		*
		* Page 				Configuration
		*
		* @CMS required			PluXml 
		*
		* @version			3.1.0
		* @date				13/12/2024
		* @author 			G.Cyrille
		░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
		░       ░░  ░░░░░░░  ░░░░  ░  ░░░░  ░░      ░░       ░░░      ░░  ░░░░░░░        ░░      ░░░░░   ░░░  ░        ░        ░
		▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒  ▒▒  ▒▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒    ▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒
		▓       ▓▓  ▓▓▓▓▓▓▓  ▓▓▓▓  ▓▓▓    ▓▓▓  ▓▓▓▓  ▓       ▓▓  ▓▓▓▓  ▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓      ▓▓▓▓▓  ▓  ▓  ▓      ▓▓▓▓▓▓  ▓▓▓▓
		█  ███████  ███████  ████  ██  ██  ██  ████  █  ███████  ████  █  ██████████  ██████████  ████  ██    █  ██████████  ████
		█  ███████        ██      ██  ████  ██      ██  ████████      ██        █        ██      ██  █  ███   █        ████  ████
		█████████████████████████████████████████████████████████████████████████████████████████████████████████████████████████
	**/
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf['default_lang']);	
	
	if(!empty($_POST)) {
		$forumStatics = json_encode($_POST['statics'], JSON_PRETTY_PRINT);
		if(isset($plxAdmin->aCong['homeStatic']) && isset($forumStatics[$plxAdmin->aCong['homeStatic']])) 
		unset($forumStatics[$plxAdmin->aCong['homeStatic']]);
		$plxPlugin->setParam('showLast' 			,$_POST['showLast']									, 'numeric');
		$plxPlugin->setParam('otherForums' 			,$_POST['otherForums']								, 'numeric');
		$plxPlugin->setParam('notification' 		,$_POST['notification']								, 'numeric');
		$plxPlugin->setParam('bottom'		 		,$_POST['bottom']									, 'numeric');
		$plxPlugin->setParam('intermediaire' 		,$_POST['intermediaire']							, 'numeric');
		$plxPlugin->setParam('bypage' 				,$_POST['bypage']									, 'numeric');
		$plxPlugin->setParam('notificationNb'		,$_POST['notificationNb']							, 'numeric');
		$plxPlugin->setParam('captcha' 				,$_POST['captcha']									, 'numeric');
		$plxPlugin->setParam('moderateSubscription' ,$_POST['moderateSubscription']						, 'numeric');
		$plxPlugin->setParam('stopSubscription' 	,$_POST['stopSubscription']							, 'numeric');
		$plxPlugin->setParam('statics' 				,$forumStatics										, 'cdata');
		$plxPlugin->setParam('colorspattern' 		,$_POST['colorspattern']							, 'string');
		if(isset($_POST['staticsForm'])) $plxPlugin->setParam('staticsForm' 		,json_encode($_POST['staticsForm'], JSON_PRETTY_PRINT)	, 'cdata');
		
		$plxPlugin->saveParams();	
		header("Location: parametres_plugin.php?p=".basename(__DIR__));
		exit;
	}
	
	$var['showLast'] = $plxPlugin->getParam('showLast')=='' ? 0: $plxPlugin->getParam('showLast');	
	$var['otherForums'] = $plxPlugin->getParam('otherForums')=='' ? 0: $plxPlugin->getParam('otherForums');	
	$var['bottom'] = $plxPlugin->getParam('bottom')=='' ? 0: $plxPlugin->getParam('bottom');	
	$var['intermediaire'] = $plxPlugin->getParam('intermediaire')=='' ? 0: $plxPlugin->getParam('intermediaire');	
	$var['bypage'] = $plxPlugin->getParam('bypage')=='' ? 5: $plxPlugin->getParam('bypage');		
	$var['notificationNb'] = $plxPlugin->getParam('notificationNb')=='' ? 5: $plxPlugin->getParam('notificationNb');	
	$var['captcha'] = $plxPlugin->getParam('captcha')=='' ? 1: $plxPlugin->getParam('captcha');	
	$var['moderateSubscription'] = $plxPlugin->getParam('moderateSubscription')=='' ? 1: $plxPlugin->getParam('moderateSubscription');
	$var['stopSubscription'] = $plxPlugin->getParam('stopSubscription')=='' ? 1: $plxPlugin->getParam('stopSubscription');
	$var['statics'] = $plxPlugin->getParam('statics')== false||null ? '[]': $plxPlugin->getParam('statics');
	$var['staticsForm'] = $plxPlugin->getParam('staticsForm')== false||null ? '[]': $plxPlugin->getParam('staticsForm');
	$var['notification'] = $plxPlugin->getParam('notification')== '' ? '0': $plxPlugin->getParam('notification');
	$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');	
	$var['colorspattern'] = $plxPlugin->getParam('colorspattern')=='' ? 'colors1' : $plxPlugin->getParam('colorspattern');	
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
		# chargement de chaque fichier de langue
		$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.'StaticComments/lang/'.$lang.'.php');
		$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName_'.$lang);
	}
	$staticsList='';
	
	$staticSelected = json_decode($var['statics']);	
	$staticFormSelected = json_decode($var['staticsForm']);
	
	global $plxAdmin;
	$staticsArray= $plxAdmin->aStats;
	#recuperation des pages plugins en mode static :
	foreach($plxAdmin->plxPlugins->aPlugins as $key => $value) {
		$plugInstance=$plxAdmin->plxPlugins->getInstance($key);
		if($plugInstance->getParam('url') != false) {
			$staticsArray[$plugInstance->getParam('url')]=array('name'=> $plugInstance->getParam('url') );
		}			
	}
	
	# on recupére les options pattern de couleurs
	
	$optionColors ='
	<div style="background:#D3F5E2;width:max-content;display:grid;grid-template-columns:auto auto;gap:0 1em">';
	foreach($plxPlugin->colors as $color => $pattern) {	
		$checked='';
		if($var['colorspattern'] == $color) $checked="checked";
		$optionColors .= '<label for="'.$color.'">
		'.$plxPlugin->patternToRGB($plxPlugin->colors[$color]).'
		</label>
		<input '.$checked.' type="radio" name="colorspattern" id="'.$color.'" value="'.$color.'" style="margin:auto 0">'.PHP_EOL;
		
	}
	$optionColors .='</div>'.PHP_EOL;
	
	if(count($staticsArray) > 0 ) {
		foreach($staticsArray as $k => $static){
			if($k == $plxAdmin->aConf['homestatic']) {
				
				continue;
			} 
			$ok='';
			$formOk ='';
			if(is_array($staticSelected) && in_array($k,$staticSelected)) {
				$ok ='checked="checked"';
			}
			if(is_array($staticFormSelected) &&  in_array($k,$staticFormSelected)) {
				$formOk ='checked="checked"';
			}			
			$staticsList .='			<tr>
			<th><label for="statics-'.$k.'">'.$static['name'].'</label></th>
			<td><input type="checkbox" value="'.$k.'" '.$ok.'  name="statics[]" id="statics-'.$k.'" ></td>
			<td><input type="checkbox" value="'.$k.'" '.$formOk.'  name="staticsForm[]" id="staticsForm-'.$k.'" ></td>
			</tr>'.PHP_EOL;
		}
	}
	
	# affichage du wizard à la demande
	if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
	# fermeture session wizard
	if (isset($_SESSION['justactivated'.basename(__DIR__)])) {
		unset($_SESSION['justactivated'.basename(__DIR__)]);
		$plxPlugin->wizard();
	}
	# On récupère les templates des pages statiques
	$files = plxGlob::getInstance(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$plxAdmin->aConf['style']);
	if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
		foreach($array as $k=>$v)
		$aTemplates[$v] = $v;
	}	
?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS."StaticMiniForum/css/tabs.css" ?>" media="all" />
<p>Ajoute aux pages statique choisies le formulaire de commentaire et le captcha natif de PluXml.</p>	
<h2><?php $plxPlugin->lang("L_CONFIG") ?></h2>
<a href="parametres_plugin.php?p=<?= basename(__DIR__) ?>&wizard" class="aWizard"><img src="<?= PLX_PLUGINS.basename(__DIR__)?>/img/wizard.png" style="height:2em;vertical-align:middle" alt="Wizard"> Wizard</a>
<div id="tabContainer">
	<form action="parametres_plugin.php?p=<?= basename(__DIR__) ?>" method="post" >
		<div class="tabs">
			<ul>
				<li id="tabHeader_Param"><?php $plxPlugin->lang('L_SELECT_STATIC') ?></li>
				<li id="tabHeader_Print"><?php $plxPlugin->lang('L_CONFIG_FORUM') ?></li>			
			</ul>
		</div>
		<div class="tabscontent">
			<div class="tabpage" id="tabpage_Param">
				<fieldset style="display:grid;grid-template-columns:auto 1fr;place-items:center;"><legend><?= $plxPlugin->getLang('L_PARAMS_STATIC') ?></legend>
					<table>
						<thead>
							<tr>
								<th><?= $plxPlugin->getLang('L_SELECT_STATIC') ?></th>
								<th><?= $plxPlugin->getLang('L_PARAMS_CAPTCHA') ?></th>
								<th><?= $plxPlugin->getLang('L_FORM_DISPLAY') ?></th>
							</tr>
						</thead>
						<tbody>
							<?= $staticsList ?>
						</tbody>
					</table>
					<p><?= $plxPlugin->getLang('L_PARAMS_HINT') ?></p>
				</fieldset>
			</div>
			<div class="tabpage" id="tabpage_Print">
				<fieldset class="alert blue">
					<legend><?= $plxPlugin->getLang('L_SUBSCRIPTION') ?></legend>		
						<p>
							<label for="moderateSubscription"><?= $plxPlugin->getLang('L_MODERATE_SUBSCRITION') ?></label> 
							<?php plxUtils::printSelect('moderateSubscription',array('0'=>L_YES,'1'=>L_NO), $var['moderateSubscription']);?>		
						</p>		
						<p>
							<label for="stopSubscription"><?= $plxPlugin->getLang('L_STOP_SUBSCRIPTION') ?></label> 
							<?php plxUtils::printSelect('stopSubscription',array('1'=>L_YES,'0'=>L_NO), $var['stopSubscription']);?>		
						</p>
				</fieldset>	
				<fieldset class="grid alert green">
					<legend><?= $plxPlugin->getLang('L_PARAMS_FORUMS') ?></legend>	
					<div style="display:grid">
						<p>
							<label for="bypage"><?= $plxPlugin->getLang('L_PARAMS_BY_PAGE') ?></label> 
							<?php plxUtils::printInput('bypage',$var['bypage'],'text','3-5') ?>		
						</p>		
						<p>
							<label for="intermediaire"><?= $plxPlugin->getLang('L_IN_BETWEEN') ?></label> 
							<?php plxUtils::printSelect('intermediaire',array('1'=>L_YES,'0'=>L_NO), $var['intermediaire']);?>		
						</p>		
						<p>
							<label for="showLast"><?= $plxPlugin->getLang('L_LAST_FIRST') ?></label> 
							<?php plxUtils::printSelect('showLast',array('1'=>L_YES,'0'=>L_NO), $var['showLast']);?>		
						</p>
					</div>
					<div>
						<label for="colorspattern"><?= $plxPlugin->getLang('L_PATTERN_COLORS') ?></label>
						<?= $optionColors ?>
					</div>
				</fieldset>
				<fieldset class="grid alert blue">
				<legend><?= $plxPlugin->getLang('L_PARAMS_NOTIFICATIONS') ?></legend>	
							
					<p>
						<label for="nofication"><?= $plxPlugin->getLang('L_SIDEBAR_NOTIFICATION') ?></label> 
						<?php plxUtils::printSelect('notification',array('1'=>L_YES,'0'=>L_NO), $var['notification']);?>		
					</p>		
					<p>
						<label for="bottom"><?= $plxPlugin->getLang('L_BOTTOM_NOTIFICATION') ?></label> 
						<?php plxUtils::printSelect('bottom',array('1'=>L_YES,'0'=>L_NO), $var['bottom']);?>		
					</p>
						
					<p>
						<label for="notificationNb"><?= $plxPlugin->getLang('L_NOTIFICATION_NUMBER') ?></label> 
						<?php plxUtils::printInput('notificationNb',$var['notificationNb'],'text','3-5') ?>		
					</p>		
					<p>
						<label for="otherForums"><?= $plxPlugin->getLang('L_SHOW_OTHER_FORUMS') ?></label> 
						<?php plxUtils::printSelect('otherForums',array('1'=>L_YES,'0'=>L_NO), $var['otherForums']);?>		
					</p>	
				
				</fieldset>
			</div>
			
			
			<p class="in-action-bar">
				<?php echo plxToken::getTokenPostMethod() ?><br>
				<input type="submit" name="submit" value="<?= $plxPlugin->getLang('L_SAVE') ?>"/>
			</p>
			
		</form>
	</div>
<script type="text/javascript" src="<?php echo PLX_PLUGINS."StaticMiniForum/js/tabs.js" ?>"></script>											