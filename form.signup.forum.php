<?php if(!defined('PLX_ROOT')) exit;
$plug =	$plxAdmin->plxPlugins->getInstance('StaticMiniForum')
	?><!DOCTYPE html>
<html lang="<?php echo $plxAdmin->aConf['default_lang'] ?>" id="new">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower(PLX_CHARSET); ?>"/>
		<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
		<title><?= L_CONFIG_USERS_NEW ?></title>
		<link rel="stylesheet" type="text/css" href="<?= PLX_PLUGINS . basename(__DIR__) ?>/css/auth.css" media="screen" />
	</head>
	<body>
		
		<main class="container">
			<section class="grid">
				<div class="auth col sml-12 sml-centered med-5 lrg-3">
				<?php if($plug->getParam('stopSubscription') ==0) {?>
					<form action="" method="post" id="form_new">
						<fieldset class="new">
							<h2 class="h6 text-center"><strong><?php echo L_CONFIG_USERS_NEW; ?></strong></h2>
							<?php (!empty($msgSignUp))?plxUtils::showMsg($msgSignUp, $errorSignUp):''; ?>
							
							<div class="signup">
								<div class="grid">
									<div class="col sml-12">
										<?php echo plxToken::getTokenPostMethod(); ?>
										<?php if($label){ ?><label for="id_name"><?=L_PROFIL_USER?>&nbsp;*</label><?php } ?>
										<i class="ico icon-name-si"></i>
										<?php plxUtils::printInput('name', '', 'text', '10-255', false, 'full-width" required placeholder="'.L_PROFIL_USER.' *'); ?>
										
									</div>
								</div>
								<div class="grid">
									<div class="col sml-12">
										<?php if($label){ ?><label for="sign_login"><?=L_PROFIL_LOGIN?>&nbsp;*</label><?php } ?>
										<i class="ico icon-user-si"></i>
										<input id="sign_login" name="login" type="text" value="" class="full-width" required placeholder="<?= L_PROFIL_LOGIN?> *" size="10" maxlength="255"/>
									</div>
								</div>
								<div class="grid">
									<div class="col sml-12">
										<?php if($label){ ?><label for="sign_password"><?=L_PASSWORD_SIGNUP_FORUM?>&nbsp;*</label><?php } ?>
										<i class="ico icon-lock-si"></i>
										<input id="sign_password" name="password" type="password" value="" class="full-width" min="6" required placeholder="<?=L_PASSWORD_SIGNUP_FORUM ?> *" onkeyup="pwdStrength(this.id)" size="10" maxlength="255"/>
									</div>
								</div>
								<div class="grid">
									<div class="col sml-12">
										<?php if($label){ ?><label for="id_wall-e"><?=L_USER_MAIL?>&nbsp;*</label><?php } ?>
										<i class="ico icon-mail-si"></i>
										<?php plxUtils::printInput('wall-e', '', 'email', '10-255', false, 'full-width" required placeholder="'.L_USER_MAIL.' *'); ?>
										
										<?php plxUtils::printInput('email', '', 'email', '10-255', false, 'full-width" placeholder="'.L_USER_MAIL); ?>
										
									</div>
								</div>
								<div class="grid">
									<div class="col sml-12 text-center">
										<input type="submit" name="update" value="<?php echo L_SUBMIT_BUTTON ?>" class="green" />
									</div>
								</div>
							</div>
						</fieldset>
					</form>
				<?php  }
				else { ?>
				<p class="new"><?php $plug->lang('L_STOPPED_SUBSCRIPTION') ?></p>
				<?php } ?>
					<p class="text-center">
						<small>
							<a class="back" href="<?php echo PLX_ROOT; ?>"><?php echo L_BACK_SIGNUP_FORUM ?></a> - 
							<a rel="nofollow" href="auth.php"><?php echo L_SHIFT_SIGNUP_FORUM ?></a> - 
							<?php echo L_POWERED_BY ?>
						</small>
					</p>
				</div>
			</section>
		</main>
	</body>
</html>