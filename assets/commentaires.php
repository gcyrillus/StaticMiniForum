<?php  if(!defined('PLX_ROOT')) exit;
	# Plugin StaticMiniForum V 3.1.0  13/12/2024
	# @Author Gcyrillus
	$plug = $this->plxMotor->plxPlugins->getInstance('StaticMiniForum');
    if(isset($_POST['searchForum']) ) {
        $search =htmlentities(trim($_POST['searchForum']));
        if(strlen(trim($_POST['searchForum'])) < 3) {
            echo '<p class="alert blue">'.$plug->getLang('L_THREE_LETTERS').'</p>';
        }
        else {
            echo '<div id="results">';
            include 'searchMiniForum.php';  
            echo'</div>';
        }
        
    }   
    
	
	#est ce une page statique configurée pour les commentaires ?
	$var['statics']      = $plug->getParam('statics')      == false||null ? '[]': $plug->getParam('statics');
	$var['colorspattern']= $plug->getParam('colorspattern')== false||null ? 'colors1' : $plug->getParam('colorspattern');
	$var['staticsForm']  = $plug->getParam('staticsForm')  == false||null ? '[]': $plug->getParam('staticsForm');
	$staticSelected      = json_decode($var['statics']);
	$staticFormSelected  = json_decode($var['staticsForm']);
	$color="green";
	if(is_array($staticSelected) && in_array($this->staticId(), $staticSelected) && str_pad($this->staticId(), 3, '0', STR_PAD_LEFT) != $this->plxMotor->aConf['homestatic'] ) {
    ?>
    <div id="staticMiniForum">
        <?php
            
            # affichage du formulaire et commentaires enregistrés.	
            #########################################
            #configuration
            #########################################
            
            
            # nombre de Commentaire à afficher par page
            $bypage  = $plug->getParam('bypage')=='' ? 5: $plug->getParam('bypage');
            
            #  1 au lieu de 0 pour afficher les liens de chaque page
            $intermediaire = $plug->getParam('intermediaire')=='' ? 0: $plug->getParam('intermediaire');
            
            #  1 pour afficher la derniere page des commentaires par défaut
            $showLast = $plug->getParam('showLast')=='' ? 1: $plug->getParam('showLast'); 
            
            #########################################
            # FIN configuration
            #########################################
            
            # fichier de stockages
            $sujets ='/data/statiques/threads-'.$this->staticId().'.json';
            $reponses = '/data/statiques/threads-'.$this->staticId().'-reply.json';
            
            $numRepliesBySubject = array();
            $threadsFile = $plug->getFileDatas($sujets);
            $threadsFileReply =$plug->getFileDatas($reponses);
            $replies = $threadsFileReply;
            $dataFile= PLX_ROOT.$sujets;
            
            foreach($replies as $k => $v) {
                if(isset($numRepliesBySubject[$v['subject']])) $numRepliesBySubject[$v['subject']] = ++$numRepliesBySubject[$v['subject']];
                else $numRepliesBySubject[$v['subject']] =1;
            }
            if(isset($_GET['replies'])) {
                $subjectRelated =  $threadsFile;
                foreach($subjectRelated as $index => $array) {
                    if($array['num'] === trim($_GET['replies'])){
                        $subjectRelated = $array;
                        break;
                    }
                }
                $threadsFile = $threadsFileReply;
                $dataFile= PLX_ROOT.$reponses;
            } 
            # extraction des commentaires dans un tableau
            $threads =  $threadsFile;
            # format du message aprés soumission du formulaire 
            $row ='<p id="forum_message" class="#forum_class"><strong>#forum_message</strong></p>';   
            
            ############
			# moderation
            ############
            
			if (isset($_SESSION['profil']) && $_SESSION['profil']=='0' && isset($_POST['delCom']))  { 
				$color = 'green';
                $message='';
				$_SESSION['msgcom'] = $this->getLang('CLEAR').' : '.$plug->getLang('L_NO_THREAD') ;
				foreach ($threads as $subKey => $subArray) { 
                    
					if (in_array($subArray['num'], $_POST["delCom"])) {
                        
                        if(isset($_GET['replies']) &&  trim($subArray['subject']) != trim($_GET['replies'])) continue;
                        
						$message .= $plug->getLang('L_THREAD').' #'.
						$subArray['num'].'-'.
						$this->getLang('WRITTEN_BY').' 
						<cite>'.$threads[$subKey]['name'].'</cite> <b>: ' .
						$this->getLang('CLEAR').'</b><br>';
						unset($threads[$subKey]);
                        
                    }
                }
                $plug->saveJsonDatas($dataFile,$threads);
                header("location:".$_SERVER['REQUEST_URI']);
				$threads =  $plug->getFileDatas($sujets);
                
                if($message !='') $_SESSION['msgcom']=$message;
				$num = count($threads);
            }
            
            
            if(isset($_GET['replies'])) {
                foreach($threads as $k => $post ){
                    if(trim($post['subject']) != trim($_GET['replies']))
                    unset($threads[$k]);
                }  
            }
            $num = count($threads);
			######################################
            # As t-on de nouvelles interventions ?
			######################################
            if(isset($_SESSION["profil"]) && !isset($_GET['backHome'])) {
                if(is_array($staticFormSelected) &&  in_array($this->staticId(), $staticFormSelected)){
                    $type='';
                    $level='level0';
                    if(isset($_POST['name']) AND isset($_POST['content']) && $_POST['content'] !='') {
                        # color boite message par défaut
                        $color = 'orange';
                        if( $_POST['name'] =='' OR $_POST['content'] =='') {
                            $_SESSION['msgcom'] =  L_NEWCOMMENT_FIELDS_REQUIRED;
                        } 
                        else {
                            
                            if(isset($_POST['level'])) 
                            {
                                $level = trim($_POST['level']) ;
                                if($level == 'level5' || $level =='level-max' ) {$level ='level-max';}
                                else {
                                    if(phpversion()>= '8.3.0') {$level= str_increment($level);}
                                    else {$level++;}
                                }
                            }            
                            if(isset($_GET['replies'])) { 
                                $q = trim($_GET['replies']); 
                                $type = '&replies='.$q;
                                
                                
                                // $threadsFile[$index][$array['date']]=date('d-m-Y');
                                //var_dump($threadsFile[$q]);
                                
                                
                            }
                            else $q=trim(strip_tags($_POST['subject']));
                            
                            $newpost[] = array(
                            'num'       => trim(strip_tags($_POST['num'])),
                            'subject'   => $q,
                            'level'     => trim(strip_tags($level)),                                
                            'date'      => date('d-m-Y') ,
                            'name'      => trim(strip_tags($_POST['name'])) , 
                            'profil'    => trim($_POST['profilMember']),
                            'mail'      => trim(strip_tags($_POST['mail']))  , 
                            'content'   => htmlspecialchars($_POST['content'])
                            );  
                            
                            $color = 'green';
                            $_SESSION['msgcom'] = $plug->getLang('L_THREAD_PUBLISHED');
                            if(isset($_GET['replies'])) $_SESSION['msgcom'] = $plug->getLang('L_REPLY_PUBLISHED');
                            # est ce une reponse ?
                            if(isset($_GET['replies'])){
                                $threads= $threadsFileReply;  
                            }
                            
                            array_push($threads,$newpost[0]);
                            
                            # on enregistre la derniere action
                            $link = $this->plxMotor->urlRewrite('index.php?static'.$this->staticId().'/'.$this->plxMotor->aStats[str_pad($this->staticId(), 3, '0', STR_PAD_LEFT)]['url'].$type.'#id-'.trim($_POST['num']));
                            $name=trim(strip_tags($_POST['name']));
                            $summary =trim(strip_tags($_POST['subject']));
                            if(isset($_GET['replies'])) $summary =strip_tags($_POST['content']);
                            $contentslice =plxUtils::strCut($summary);
                            $notification = '<li><a href="'.$link.'">'.$name  .' : '.$contentslice.'</a></li>';
                            
                            if($type =='' ) $records = $plug->lastSubjects;
                            else $records = $plug->lastReplies;
                            $updateRecords = $plug->getFileDatas($records);
                            if(count($updateRecords) >= 5 ) 
                            $delete = array_shift($updateRecords);
                            
                            $updateRecords[]=$notification;
                            $plug->saveJsonDatas($records,$updateRecords); 
                            
                            # on met à jour la liste des sujets en remontant celui commenter
                            $upthread= json_decode(file_get_contents(PLX_ROOT.$sujets),true);
                            foreach($upthread as $index=>$subnum) {
                                if($subnum['num'] == $q) {
                                    $upthread[$index]['date'] = date('d-m-Y');
                                    $update= $upthread[$index];
                                    # remove
                                    unset($upthread[$index]);
                                    # replace 
                                    $upthread[]= $update;
                                    $plug->saveJsonDatas(PLX_ROOT.$sujets,$upthread);
                                    break;
                                }
                            }
                            
                            
                            $plug->saveJsonDatas($dataFile,$threads);
                            
                            header('location:'.$_SERVER['REQUEST_URI']);
                            exit;
                        }
                    }
                }
            }
            
			############################################
			# Quel numero pour le prochain commentaire ?	
			############################################
            
			foreach ($threads as $subKey => $subArray) {
				if(isset($threads[$subKey]['num']) && $num < $threads[$subKey]['num']) $num= $threads[$subKey]['num'];
            }
            
            
            
			###########
            # affichage
			###########
            # Style barre pagination commentaires
            
            echo '<p id="version">
            <img class="vicon" src="plugins/staticMiniForum/icon.jpg"><small>
            <a href ="https://ressources.pluxopolis.net/banque-plugins/index.php?all_versions#tr-StaticMiniForum" title="Plugin staticMiniForum">Miniforum pour PluXml</a> '.$plug->version.'</small>
            </p>.';
            echo '<nav id="forum_nav">'.PHP_EOL;
            if(!isset($_SESSION['profil'])) echo ' <a href="core/admin/auth.php?newMember=1">S\'inscrire</a> - <a href="core/admin/auth.php">Se connecter</a> '.PHP_EOL;
            
            else echo '<a href="'. $this->plxMotor->urlRewrite(ltrim($_SERVER['REQUEST_URI'],'/')) .'?&backHome'.'">Se déconnecter</a>'.PHP_EOL. '<small>'.$plug->memberProfils[$_SESSION['profil']].'</small>'.PHP_EOL;
            
            if(isset($_GET['replies'])) echo ' - <a class="reddish" href="'. $this->plxMotor->urlRewrite('?static' . $this->staticId() . '/' . $this->plxMotor->aStats[str_pad($this->staticId(), 3, '0', STR_PAD_LEFT)]['url'] ).'#forum_posts">Retour aux Sujets</a>'.PHP_EOL;
            
            echo'</nav>'.PHP_EOL;
            
            if(isset($_GET['replies']) && isset($subjectRelated['subject'])) { 
                if(!isset($subjectRelated['profil'])) $subjectRelated['profil'] ='5';
                echo '
                <div id="subject" class="main_subject">
                <header>
                <h3>'.$plug->HTMLbadge(trim($subjectRelated['name']),$plug->colors[$var['colorspattern']],$plug->HTMLletterBadge)
                .'<big>'.$subjectRelated['subject'].'</big>
                <time date="'.$subjectRelated['date'].'">'.$subjectRelated['date'].'</time> 
                <small>'.$plug->memberProfils[$subjectRelated['profil']].'</small></h3> 
                </header>
                <div>'.$subjectRelated['content'].'</div>             
                </div>
                ';
            }
            
            
            if(count($threads)>0) {
                if(isset($_GET['replies'])) {
                    $nbitem = $plug->getLang('L_SUBJECT_REPLY');
                    $nbitems = $plug->getLang('L_SUBJECTS_REPLY');
                }
                else {
                    $nbitem = $plug->getLang('L_SUBJECT');
                    $nbitems = $plug->getLang('L_SUBJECTS');
                }
                $tittleThread ="<h3 id='forum_posts'>".count($threads)." ". $nbitem ."</h3>".PHP_EOL;        
                if(count($threads)>1) {
                    $tittleThread ="<h3 id='forum_posts'>".count($threads)." ".  $nbitems ."</h3>".PHP_EOL;          
                }
                echo '<header id="headForum">'.$tittleThread.'<form action="'.$_SERVER['REQUEST_URI'].'#results" method="post"><input name="searchForum" placeholder="'.$plug->getLang('L_PLACEHOLDER_SEARCH').'"><input type="submit" value="'.$plug->getLang('L_SUBMIT_SEARCH').'"></form></header>' ;
                
                #############################
                # extraction et maj variables
                #############################
                
                # extraction de l'url
                $url = $this->plxMotor->urlRewrite('?static' . $this->staticId() . '/' . $this->plxMotor->aStats[str_pad($this->staticId(), 3, '0', STR_PAD_LEFT)]['url'] );
                $args='';
                if(isset(parse_url($_SERVER['REQUEST_URI'])['query'])) {
                    $uriRequest = parse_url($_SERVER['REQUEST_URI'])['query'];
                    $args= strstr($uriRequest, '&');
                }
                if(isset($_GET['replies'])) $args='&replies='.trim($_GET['replies']);
                
                # generation du lien
                $link = $this->plxMotor->urlRewrite($url."/page");
                
                // On calcule le nombre de pages total
                $nbr = count($threads);
                $pages = ceil( $nbr / $bypage);
                $position = 1;
                if($showLast==1) $threads = array_reverse($threads,true);
                
                # extraction du numéro de page dans l'URL 
                $currentPage = preg_match('#\b/page(\d*)#',$_SERVER["QUERY_STRING"], $capture) ? intval($capture[1]) : $position;
                
                # indice de début, premier article à afficher
                $start = ($currentPage - 1) * $bypage;  
                
                // Calcul du 1er commentaire de la page
                $premier = ($currentPage * $bypage) - $bypage;
                
                $pageThreads = array_slice($threads, $premier, $bypage); 
                
                ###################################################
                # affichage commentaire et formulaire de moderation
                ###################################################
                $pseudo='';
                if (isset($_SESSION['profil'])  && in_array($_SESSION['profil'],array("0","1","2,")))  {  echo ' <form method="post" id="modoComs"> '; }            
                foreach($pageThreads as $thread =>$val) { # On boucle sur les sujets
                    if(!isset($val['profil']) || $val['profil']== '') $val['profil'] = 5;
                    if(!isset($val['num']) || $val['num'] =='' || $val['content'] =='') continue;
                    if(isset($_GET['replies']) && $val['subject'] != trim($_GET['replies'])) continue;
                    $pseudo = $plug->HTMLbadge(trim($val['name']),$plug->colors[$var['colorspattern']],$plug->HTMLletterBadge);
                    if(!isset($val['profil'])) $val['profil'] ='5';
                    $index=array_search( $val['num'], array_column( $threads, 'num' ) );   
                    if(isset($val['subject']) && !ctype_digit($val['subject']) ) {
                        $numReplies ='0';
                        if(isset($numRepliesBySubject[$val['num']])) $numReplies = $numRepliesBySubject[$val['num']];
                        echo '<div id="id-'.$val['num'].'" class="thread '.preg_replace('/[^a-zA-Z0-9]/s', '', $val['level']).'">'.PHP_EOL;
                        if (isset($_SESSION['profil']) && in_array($_SESSION['profil'],array("0","1","2,")) && $numReplies =='0')  { echo '<label  class="modo"><input type="checkbox" name="delCom[]" id="#'.$val['num'].'" value="'.$val['num'].'">'.$plug->getLang('L_DELETE').'</label>'.PHP_EOL;}
                        $valsubject='<h3>
                        <a href="'. $this->plxMotor->urlRewrite('?static' . $this->staticId() . '/' . $this->plxMotor->aStats[str_pad($this->staticId(), 3, '0', STR_PAD_LEFT)]['url'] ).'&replies='.$val['num'].'#forum_posts" title="Voir le sujet :'.$val['subject'].' ">'.$val['subject'].'</a>
                        <span>'.$numReplies.' '.$plug->getLang('L_REPLYS').'</span>
                        </h3>'.PHP_EOL;
                        $class='class="main_subject thread"';
                        echo '<div id="thread-'.$val['num'].'" data-index="'.$index.'" data-level="'.preg_replace('/[^a-zA-Z0-9]/s', '', $val['level']). '" '.$class.'>
                        '.$valsubject.'
                        <p class="named">'.$pseudo.' <time datetime="'.$val['date'].'">'.$val['date'].'</time> <small>'.$plug->memberProfils[$val['profil']].'</small></p>
                        </div>'.PHP_EOL;
                        
                        echo '</div>'.PHP_EOL;
                    } 
                    
                    else {
                        echo '<div id="id-'.$val['num'].'" class="thread '.preg_replace('/[^a-zA-Z0-9]/s', '', $val['level']).'">';
                        if (isset($_SESSION['profil']) && in_array($_SESSION['profil'],array("0","1","2,")))  { echo '<label  class="modo"><input type="checkbox" name="delCom[]" id="#'.$val['num'].'" value="'.$val['num'].'">'.$plug->getLang('L_DELETE').'</label>';}
                        echo '
                        <div id="thread-'.$val['num'].'" data-index="'.$index.'" data-level="'.preg_replace('/[^a-zA-Z0-9]/s', '', $val['level']). '">
                        <p class="named">'.$pseudo.' 
                        <time datetime="'.$val['date'].'">'.$val['date'].'</time>  <small>'.$plug->memberProfils[$val['profil']].'</small></p>
                        <blockquote><div class="content_thread">'.$val['content'].'</div>
                        </blockquote>'.PHP_EOL;
                        echo '</div>
                        </div>'.PHP_EOL ;
                    } 
                } 
                echo '<div style="padding:0.2em;text-align:center"><img src="'.PLX_ROOT.'/plugins/StaticMiniForum/icon.jpg" width=40></div>'.PHP_EOL;
                
                if (isset($_SESSION['profil']) && $_SESSION['profil']=='0')  { echo '<input type="submit" value="'.$plug->getLang('L_VALID_DELETE').'"></form>'.PHP_EOL;}
                
                
                
                ############################
                # Affichage de la pagination
                ############################
                if($pages>1){
                ?>
                <nav>
                    <ul class="pagination text-center center bordered">
                        <!-- Lien vers la page précédente (si on ne se trouve pas sur la 1ère page) -->
                        <?= ($currentPage > 1)  ? "<li class=\"page-item\" ><a href=\"".$link . ($currentPage - 1).$args ."#forum_posts\" class=\"page-link\">".L_PAGINATION_PREVIOUS."</a></li>" : "" ?>
                        
                        <?php if($intermediaire == 1)  {
                            for($page = 1; $page <= $pages; $page++) {
                                # Lien vers chacune des pages (activé si on se trouve sur la page correspondante
                                echo '<li class="page-item ';
                                if($currentPage == $page)  echo 'active';
                                echo "\"><a href=\"".$link.$page.$args ."#forum_posts\" class=\"page-link\">".$page."</a></li>";
                            }
                        }
                        else {
                            echo "<li class=\"page-item page-link  active \">
                            ".$currentPage." / ". $pages."
                            </li>"; 
                            
                        }   ?>
                        <!-- Lien vers la page suivante (si on ne se trouve pas sur la dernière page) -->
                        <?= ($currentPage < $pages) ? " <li class=\"page-item\"><a href=\"".$link.($currentPage + 1 ).$args."#forum_posts\" class=\"page-link\">".L_PAGINATION_NEXT."</a></li>" : "" ?>
                        
                    </ul>
                </nav>
                <?php   }
                if($plug->getParam('bottom') && $plug->getParam('bottom') ==1) $plug->lastThreads();
            }
            else {
                $tittleThread ="<h3 id='forum_posts'>".$plug->getLang('L_NO_POST')."</h3>".PHP_EOL; 
                echo '<header id="headForum">'.$tittleThread.'<form action="'.$_SERVER['REQUEST_URI'].'#results" method="post"><input name="searchForum" placeholder="'.$plug->getLang('L_PLACEHOLDER_SEARCH').'"><input type="submit" value="'.$plug->getLang('L_SUBMIT_SEARCH').'"></form></header>' ;
                
            }
            
            # formulaire de recherche
            
            
            if (!empty($_SESSION['msgcom'])) {
                $message=$_SESSION['msgcom'];
                $color="green";
                $row = str_replace('#forum_class', 'alert ' . $color, $row);
                unset($_SESSION['msgcom']);
            }
            else {
                $message='';
                
            }
            $row = str_replace('#forum_message',$message , $row);
            
            if(is_array($staticFormSelected) && in_array($this->staticId(), $staticFormSelected)) {
                
                if(isset($_SESSION['profil']) && isset($_GET['replies']) && !isset($subjectRelated['subject'])){
                ?>
                <script>
                    const myformtpl =``;
                </script>
                <?php
                }
                elseif(isset($_SESSION['profil'])) {
                ?>
                <script>
                    const myformtpl =`
                    <template id="myform">
                    <h3 class="miniForum">
                    <?php if(!isset($_GET['replies'])) {
                        $plug->lang('WRITE_A_THREAD');
                        $txt_content = $plug->getLang('SUBJECT_CONTENT');
                    }
                    else {
                        $plug->lang('SUBJECT_REPLY'); 
                        $txt_content = $plug->getLang('SUBJECT_CONTENT_REPLY');
                        
                    }?>
                </h3>
                <form id="form" action="<?php echo $_SERVER['REQUEST_URI']; ?>#form" method="post">
                    <?= $row;  ?>
                    <fieldset>
                        <?php  if(!isset($_GET['replies'])) { ?>
                            <div class="grid">
                                <div class="col sml-12">
                                    <label for="id_subject"><?php $plug->lang('SUBJECT') ?>* :</label>
                                    <input id="id_subject" name="subject" type="text" size="20" value="" maxlength="30" required="required" />
                                </div>
                            </div>  
                        <?php } ?>
                        <div class="grid">
                            <div class="col sml-12">
                                <input type="hidden" name="profilMember" id="profilMember" value="<?= $_SESSION['profil'] ?>">
                                <label for="id_content" class="lab_com"><?= $txt_content ?>* :</label>
                                <textarea id="id_content" name="content" cols="35" rows="6" placeholder="<?php $plug->lang('L_WRITE') ?>" required="required"></textarea>
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col sml-12 med-6">
                                <label for="id_name"><?php $this->lang('NAME') ?>* :</label>
                                <input id="id_name" name="name" type="text" size="20" maxlength="30" required="required" value="<?= $this->plxMotor->aUsers[$_SESSION["user"]]["name"] ?>" />
                            </div>
                            <div class="col sml-12 med-6">
                                <label for="id_mail"><?php $this->lang('EMAIL') ?> :</label>
                                <input id="id_mail" name="mail" type="text" size="20" value="<?= $this->plxMotor->aUsers[$_SESSION["user"]]["email"] ?>" />
                            </div>
                        </div>
                        
                        <div class="grid">
                            <div class="col sml-12">
                                <input type="hidden" id="num" name="num" value="<?php 
                                    if ($num == 0 ) $num = 1;
                                    else ++$num ;
                                echo $num; ?>"/>
                                <input type="hidden" id="id_parent" name="parent" value="" />
                                <input class="blue" type="submit"  />
                            </div>
                        </div>
                        
                    </fieldset>
                    
                </form>
            </template>`;
        </script>
        <?php }
        else { 
        ?>
        <script>
            const myformtpl =``;
        </script>
        <?php   }
        
    } ?>
    
    
</div>
<?php } ?>        