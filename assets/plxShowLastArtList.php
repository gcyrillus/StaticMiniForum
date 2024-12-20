<?php  if(!defined('PLX_ROOT')) exit; 
# clone $plxShow function lastArtList() pluxml 5.8.16

        # Génération de notre motif
        $all = (isset($all) ? $all : empty($cat_id)); # pour le hook : si $all = TRUE, n'y passe pas
        $cats = $this->plxMotor->activeCats . '|home'; # toutes les categories active
        if (!$all) {
            if (is_numeric($cat_id)) # inclusion à partir de l'id de la categorie
                $cats = str_pad($cat_id, 3, '0', STR_PAD_LEFT);
            else { # inclusion à partir de url de la categorie
                $cat_id .= '|';
                foreach ($this->plxMotor->aCats as $key => $value) {
                    if (strpos($cat_id, $value['url'] . '|') !== false) {
                        $cats = explode('|', $cat_id);
                        if (in_array($value['url'], $cats)) {
                            $cat_id = str_replace($value['url'] . '|', $key . '|', $cat_id);
                        }
                    }
                }
                $cat_id = substr($cat_id, 0, -1);
                if (empty($cat_id)) {
                    $all = true;
                } else {
                    $cats = $cat_id;
                }
            }
        }
        if (empty($motif)) {# pour le hook. motif par defaut s'il n'a point créé cette variable
            if ($all)
                $motif = '/^[0-9]{4}.(?:[0-9]|home|,)*(?:' . $cats . ')(?:[0-9]|home|,)*.[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/';
            else
                $motif = '/^[0-9]{4}.((?:[0-9]|home|,)*(?:' . $cats . ')(?:[0-9]|home|,)*).[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/';
        }

        # Nouvel objet plxGlob et récupération des fichiers
        $plxGlob_arts = clone $this->plxMotor->plxGlob_arts;
        if ($aFiles = $plxGlob_arts->query($motif, 'art', $sort, 0, $max, 'before')) {
            foreach ($aFiles as $v) { # On parcourt tous les fichiers
                $art = $this->plxMotor->parseArticle(PLX_ROOT . $this->plxMotor->aConf['racine_articles'] . $v);
                $num = intval($art['numero']);
                $date = $art['date'];
                if (($this->plxMotor->mode == 'article') and ($art['numero'] == $this->plxMotor->cible))
                    $status = 'active';
                else
                    $status = 'noactive';

                # Mise en forme de la liste des catégories
                $catList = array();
                $catIds = explode(',', $art['categorie']);
                foreach ($catIds as $idx => $catId) {
                    if (isset($this->plxMotor->aCats[$catId])) { # La catégorie existe
                        $catName = plxUtils::strCheck($this->plxMotor->aCats[$catId]['name']);
                        $catUrl = $this->plxMotor->aCats[$catId]['url'];
                        $catList[] = '<a title="' . $catName . '" href="' . $this->plxMotor->urlRewrite('?categorie' . intval($catId) . '/' . $catUrl) . '">' . $catName . '</a>';
                    } else {
                        $catList[] = L_UNCLASSIFIED;
                    }
                }

                # On modifie nos motifs
                $row = str_replace('#art_id', $num, $format);
                $row = str_replace('#cat_list', implode(', ', $catList), $row);
                $row = str_replace('#art_url', $this->plxMotor->urlRewrite('?article' . $num . '/' . $art['url']), $row);
                $row = str_replace('#art_status', $status, $row);
                $author = plxUtils::getValue($this->plxMotor->aUsers[$art['author']]['name']);
                $row = str_replace('#art_author', plxUtils::strCheck($author), $row);
                $row = str_replace('#art_title', plxUtils::strCheck($art['title']), $row);
                $strlength = preg_match('/#art_chapo\(([0-9]+)\)/', $row, $capture) ? $capture[1] : '100';
                if($art['chapo'] !=='') $chapo = plxUtils::truncate($art['chapo'].'', $strlength, $ending, true, true);
                else $chapo='';
                $row = str_replace('#art_chapo(' . $strlength . ')', '#art_chapo', $row);
                $row = str_replace('#art_chapo', $chapo, $row);
                $strlength = preg_match('/#art_content\(([0-9]+)\)/', $row, $capture) ? $capture[1] : '100';
                if($art['content'] !=='') $content = plxUtils::truncate($art['content'].'', $strlength, $ending, true, true);
                else $content='';
                $row = str_replace('#art_content(' . $strlength . ')', '#art_content', $row);
                $row = str_replace('#art_content', $content, $row);
                $row = str_replace('#art_date', plxDate::formatDate($date, '#num_day/#num_month/#num_year(4)'), $row);
                $row = str_replace('#art_hour', plxDate::formatDate($date, '#hour:#minute'), $row);
                $row = str_replace('#art_time', plxDate::formatDate($date, '#time'), $row);
                $row = plxDate::formatDate($date, $row);
                $row = str_replace('#art_nbcoms', $art['nb_com'], $row);
                $row = str_replace('#art_thumbnail', '<img class="art_thumbnail" src="#img_url" alt="#img_alt" title="#img_title" />', $row);
                $row = str_replace('#img_url', $this->plxMotor->urlRewrite($art['thumbnail']), $row);
                $row = str_replace('#img_title', $art['thumbnail_title'], $row);
                $row = str_replace('#img_alt', $art['thumbnail_alt'], $row);

                # Hook plugin
                eval($this->plxMotor->plxPlugins->callHook('plxShowLastArtListContent'));

                # On genère notre ligne
                echo $row;
            }
        }
 


?>