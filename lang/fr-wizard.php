<?php
	if(!defined('PLX_ROOT')) exit; 
	/**
		* Plugin 			StaticComments
		*
		* @CMS required		PluXml 
		* @page				-wizard.php
		* @version			2.0
		* @date				21/07/2024
		* @author 			G.Cyrille
	**/		
	
	# pas d'affichage dans un autre plugin !	
	if(isset($_GET['p'])&& $_GET['p'] !== 'StaticMiniForum' ) {goto end;}
	
	# on charge la class du plugin pour y accéder
	$plxMotor = plxMotor::getInstance();
	$plxPlugin = $plxMotor->plxPlugins->getInstance( 'StaticMiniForum'); 
	
	# On vide la valeur de session qui affiche le Wizard maintenant qu'il est visible.
	if (isset($_SESSION['justactivatedStaticMiniForum'])) {unset($_SESSION['justactivatedStaticMiniForum']);}
	
	# initialisation des variables propres à chaque lanque 
	$langs = array();
	
	# initialisation des variables communes à chaque langue	
	$var = array();
	

	$var['showLast'] = $plxPlugin->getParam('showLast')=='' ? 1: $plxPlugin->getParam('showLast');	
	$var['colorspattern'] = $plxPlugin->getParam('colorspattern')=='' ? 'color1': $plxPlugin->getParam('colorspattern');	
	$var['intermediaire'] = $plxPlugin->getParam('intermediaire')=='' ? 0: $plxPlugin->getParam('intermediaire');	
	$var['bypage'] = $plxPlugin->getParam('bypage')=='' ? 5: $plxPlugin->getParam('bypage');	
	$var['captcha'] = $plxPlugin->getParam('captcha')=='' ? 1: $plxPlugin->getParam('captcha');
	$var['statics'] = $plxPlugin->getParam('statics')== false||null ? '[]': $plxPlugin->getParam('statics');
	$var['staticsForm'] = $plxPlugin->getParam('staticsForm')== false||null ? '[]': $plxPlugin->getParam('staticsForm');
	$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');	
	
	# recuperation liste et config page statiques
	$staticsList='';
	
	
	$optionColors ='
	<div style="background:white;width:max-content;display:grid;grid-template-columns:auto auto;gap:0 1em">';
	foreach($plxPlugin->colors as $color => $pattern) {	
	$checked='';
	if($var['colorspattern'] == $color) $checked="checked";
	 $optionColors .= '<label for="'.$color.'">
	 '.$plxPlugin->patternToRGB($plxPlugin->colors[$color]).'
	 </label>
	 <input '.$checked.' type="radio" name="colorspattern" id="'.$color.'" value="'.$color.'" style="margin:auto 0">'.PHP_EOL;
	}
	 $optionColors .='</div>'.PHP_EOL;
	
	$staticSelected = json_decode($var['statics']);	
	global $plxAdmin;
	$plxAdmin = plxAdmin::getInstance();
	$staticsArray= $plxAdmin->aStats;
	if(count($staticsArray) > 0 ) {
		foreach($plxAdmin->aStats as $k => $static){
			$ok='';
			if(in_array($k,$staticSelected)) {$ok ='selected="selected"';}
			
			$staticsList .='			<option value="'.$k.'" '.$ok.'>'.$static['name'].'</option>'.PHP_EOL;
		}
	}	
	#affichage
?>
<link rel="stylesheet" href="<?= PLX_PLUGINS ?>StaticMiniForum/css/wizard.css" media="all" />
<input id="closeWizard" type="checkbox">
<div class="wizard">	
	<div class="container">	
		<div class='title-wizard'>
			<h2><?= $plxPlugin->aInfos['title']?><br><?= $plxPlugin->aInfos['version']?></h2>
			<img src="<?php echo PLX_PLUGINS. 'StaticMiniForum'?>/icon.png">
			<div><q> Made at <a href="https://pluxopolis.net/">PluXopolis</a> By <?= $plxPlugin->aInfos['author']?> </q></div>
		</div>
		<p></p>
		
		<div id="tab-status">
			<span class="tab active">1</span>
		</div>		
		<form action="parametres_plugin.php?p=<?php echo 'StaticMiniForum' ?>"  method="post">
			<div role="tab-list">		
				<div role="tabpanel" id="tab1" class="tabpanel">
					<h2>Bienvenue dans l’extension <b style="font-family:cursive;color:crimson;font-variant:small-caps;font-size:2em;vertical-align:-.5rem;display:inline-block;"><?= $plxPlugin->aInfos['title']?></b></h2>
					<p>Cette extension permet à vos visiteur de commenter vos pages statiques.</p>
					<p>&Agrave; l'activation, il est necessaire de configurer l'extension en choisissant vos pages et le mode d'affichage du forum.</p>
					<p></p>
					<p>Ce wiz'aide est là pour vous aider au fil de ces quelques pages.</p>
				</div>	
				<div role="tabpanel" id="tab2" class="tabpanel hidden title">
					<h2>Choix des pages</h2>
					<p>Une liste à cliquer ...</p>
					<!-- Ci-dessous , valide le passage à une autre page si d'autre champs required existe dans le formulaire -->
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				<div role="tabpanel" id="tab3" class="tabpanel hidden">
					<h2>Choisir</h2>
					<p>Pour séléctionner plusieurs pages statiques , il faut appuyer sur la touche  <kbd> CTRL </kbd> 
						et cliquer sur le nom de la page dans la liste ci-dessous.
					</p>
					<select name="statics[]" id="statics" multiple style="width:100%;">
						<option value><?= $plxPlugin->lang('L_NONE_IN_LIST') ?></option>
						<?= $staticsList ?>
					</select>	
				</div>

				
				<div role="tabpanel" id="tab6" class="tabpanel hidden title">
					<h2>Affichage</h2>
					<p>pagination du forum et sens d'affichage.</p>
					<!-- Ci-dessous , valide le passage à une autre page si d'autre champs required existe dans le formulaire -->
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				<div role="tabpanel" id="tab7" class="tabpanel hidden">
					<h2>Options d'affichages</h2>
					<p>L'affichages des sujets et réponses (et de son formulaire)  peuvent être distribué sur plusieurs pages lorsqu'ils sont nombreux.
					</p>
					<dl>
						<dt>vous pouvez:</dt>
						<dd>les regroupé par <?php plxUtils::printInput('bypage',$var['bypage'],'text','1-5') ?>	 par pages.</dd>
						<dd> affiché les liens de toutes les pages intermediares 
						<?php plxUtils::printSelect('intermediaire',array('1'=>L_YES,'0'=>L_NO), $var['intermediaire']);?>
						<small>(non = pour seulement suivant/précédent)</small></dd>
						<dd> les trié par odre du plus récent <small>oui</small> 
						<?php plxUtils::printSelect('showLast',array('1'=>L_YES,'0'=>L_NO), $var['showLast']);?></dd>
					</dl>
				</div>
				<div role="tabpanel" id="tab6bis" class="tabpanel hidden title">
					<h2>Couleurs</h2>
					<p>Plusieurs palettes de couleurs<br> sont disponibles pour les badge "membres"</p>
					<!-- Ci-dessous , valide le passage à une autre page si d'autre champs required existe dans le formulaire -->
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>	
				<div role="tabpanel" id="tab7bis" class="tabpanel hidden">
						<h2> Choisir votre palette de couleurs.</h2>
						<p><label for="colorspattern"><?= $plxPlugin->getLang('L_PATTERN_COLORS') ?></label></p>
						<?= $optionColors ?>
				</div>				
				<div role="tabpanel" id="tab8" class="tabpanel hidden title">
					<h2>Modération</h2>
					<p>Effacer les sujets et réponses indésirables.</p>
					<!-- Ci-dessous , valide le passage à une autre page si d'autre champs required existe dans le formulaire -->
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>
				<div role="tabpanel" id="tab9" class="tabpanel hidden">
					<h2>Modération</h2>
					<p>La seule action modératrice actuellement disponible est l'effacement irreversible d'un Sujey vide ou de réponses déjà en ligne</p>
					<p>Pour effacer une ou plusieurs réponses):<br>
					1. Se connecter en Administrateur, Gestionnaire ou Modérateur à l'administration<br>
					2. Se rendre sur la page statique coté visiteurs et cliquer sur le bouton <span style="background:coral;border: 1px solid black;color:white;border-radius:5px">supprimer</span> du commentaire à effacer.<br>
					3. Valider la section en cliquant sur le bouton <span style="color:white;background:#777;border-radius:3px">Supprimer le(s) commentaire(s) selectioné(s)</span> qui apparait en bas de page.<br>
					<br><b>P.S.</b> Pour effacer un sujet, il faut au préalable effacer toutes les réponses qu'il contient.</p>
				</div>
				<div role="tabpanel" id="tabEnd" class="tabpanel hidden title">
					<h2>End Wiz'aide</h2>
					<p>Enregistrer ou fermer</p>
					<!-- Ci-dessous , valide le passage à une autre page si d'autre champs required existe dans le formulaire -->
					<!-- <input type="hidden"  class="form-input" value="keepGoing"> -->
				</div>		
				<div class="pagination">
					<a class="btn hidden" id="prev"><?php $plxPlugin->lang('L_PREVIOUS') ?></a>
					<a class="btn" id="next"><?php $plxPlugin->lang('L_NEXT') ?></a>
					<?php echo plxToken::getTokenPostMethod().PHP_EOL ?>
					<button class="btn btn-submit hidden" id="submit"><?php $plxPlugin->lang('L_SAVE') ?></button>
				</div>
			</div>		
		</form>			
		<p class="idConfig">
			<?php
				if(file_exists(PLX_PLUGINS. 'StaticMiniForum/admin.php')) {echo ' 
				<a href="/core/admin/plugin.php?p= StaticMiniForum">Page d\'administration '. basename(__DIR__ ).'</a>';}
				if(file_exists(PLX_PLUGINS. 'StaticMiniForum/config.php')) {echo ' 	<a href="/core/admin/parametres_plugin.php?p=StaticMiniForum">Page de configuration  StaticMiniForum</a>';}
			?>
			<label for="closeWizard"> Fermer </label>
		</p>	
</div>	
<script src="<?= PLX_PLUGINS ?>StaticMiniForum/js/wizard.js"></script>
</div>
<?php end: // FIN! ?>				
