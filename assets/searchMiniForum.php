<?php  if(!defined('PLX_ROOT')) exit;
	# Plugin StaticMiniForum V 3.0.0  12/12/2024
	# @Author Gcyrillus
	
	$forums = glob(PLX_ROOT.$this->plxMotor->aConf['racine_statiques'].'thread*[!reply].json');
	$forumsReply = glob(PLX_ROOT.$this->plxMotor->aConf['racine_statiques'].'/thread*[reply].json');
	$subjectresults =array();
	$replysresults =array();
	$toutLesSujets=array();
	
	
	# recherche dans les sujets	 
	$subjectResultsNb=0;
	foreach($forums as $file) {
		preg_match_all('!\d!', $file, $matches);
		$statid=implode('',$matches[0]);
		if(!isset($this->plxMotor->aStats[str_pad($statid, 3, '0', STR_PAD_LEFT)])) {
			# nettoyage des fichier obsoletes  (voir si à configurer)
			//unlink($file);
			continue;
		}
		$DansLesSujets = $plug->getFileDatas($file);
		$toutLesSujets= array_merge($toutLesSujets, $DansLesSujets);
		foreach($DansLesSujets as $index => $sujet) {
			if(strpos($sujet['content'],$search) || strpos($sujet['subject'],$search) ) {
				$name= $this->plxMotor->aStats[str_pad($statid, 3, '0', STR_PAD_LEFT)]['name'];
				$subjectresults[]='<li><a href="'. $this->plxMotor->urlRewrite('?static'.$statid.'/'.$this->plxMotor->aStats[str_pad($statid, 3, '0', STR_PAD_LEFT)]['url']).'&replies='.$sujet['num'].'#subject">'.$sujet['subject'].'</a></li>';
				++$subjectResultsNb;
			}
		}
	}

	# recherche dans les réponses	 
	$replyResultsNb=0;	
	foreach($forumsReply as $file) {
	
		preg_match_all('!\d!', $file, $matches);
		$statid=implode('',$matches[0]);
		if(!isset($this->plxMotor->aStats[str_pad($statid, 3, '0', STR_PAD_LEFT)])) {

			# nettoyage des fichier obsoletes  (a voir, à configurer ?)
			//unlink($file);
			continue;
		} 
		
		$DansLesReponses = $plug->getFileDatas($file);
		foreach($DansLesReponses as $index => $sujet) {
			if(!in_array($sujet['subject'],array_column($toutLesSujets,'num'))) {
			# le sujet n'existe pas ou plus
				continue;
			}
			if(strpos($sujet['content'],trim($search)) !== false  /*&& isset($this->plxMotor->aStats[str_pad($sujet['subject'], 3, '0', STR_PAD_LEFT)]) */) {		
				$name= $this->plxMotor->aStats[str_pad($statid, 3, '0', STR_PAD_LEFT)]['name'];
				$replyresults[]='<li><a href="'. $this->plxMotor->urlRewrite('?static'.$statid.'/'.$this->plxMotor->aStats[str_pad($statid, 3, '0', STR_PAD_LEFT)]['url']).'&replies='.$sujet['subject'].'#id-'.$sujet['num'].'">'.plxUtils::truncate($sujet['content'], 25, '...', true, false).'</a></li>';
				++$replyResultsNb;
			}
		}
	}
	$totalResults =  $subjectResultsNb + $replyResultsNb;
    echo' <h2>'.$plug->getLang('L_SEARCH_RESULTS').'<br><br>" <b>'.$search.'  <sup>('.$totalResults.')</sup></b> "</h2>';	
	if($subjectResultsNb >0)
	{
		$resultat='';
		foreach($subjectresults as $results){
			$resultat .= $results.PHP_EOL;
		}
		echo '<h3>'. $plug->getLang('L_SUBJECT_RESULTS').' '.$subjectResultsNb.'</h3>
		<ul class="other-forum-list unstyled-list">
		'.$resultat.'
		</ul>';		
	}
	
	
	if($replyResultsNb > 0)
	{
		$resultat='';
		foreach($replyresults as $results){
			$resultat .= $results.PHP_EOL;
		}
		echo '<h3>'. $plug->getLang('L_REPLY_RESULTS').' '.$replyResultsNb.'</h3>
		<ul class="other-forum-list unstyled-list">
		'.$resultat.'
		</ul>';
		
	}
	# extraction de l'url
    //$foundUrl = $this->plxMotor->urlRewrite('?static' . $staticId . '/' . $this->plxMotor->aStats[str_pad($staticId, 3, '0', STR_PAD_LEFT)]['url'] );
//$args='';