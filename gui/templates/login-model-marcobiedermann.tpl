<!-- começa aqui o login-model-marcobidermann.tpl-->
<!DOCTYPE html>
{config_load file="input_dimensions.conf" section="login"}
{lang_get var='labels' 
          s='login_name,password,btn_login,new_user_q,login,demo_usage,e_mail,mail,password_again,
             lost_password_q,demo_mode_suggested_user,demo_mode_suggested_password,old_style_login'}
<html >
	<head>
		<meta charset="UTF-8">
		<title>{$labels.login}</title>
		<!--<link rel="stylesheet" href="gui/icons/font-awesome-4.5.0/css/font-awesome.min.css">

		<link rel="stylesheet" href="gui/themes/default/login/codepen.io/marcobiedermann/css/style.css">-->
	
		<link rel="stylesheet" href="vendor/font-awesome-4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="vendor/bootstrap-3.3.7/css/bootstrap.css">
		<link rel="stylesheet" href="vendor/Argo/ArgoCustomizations.css">			
	</head>
	<body>
		<div class="container">
		{if $gui->draw} 
			<div class="row">
				<div class="col-lg-4"></div>
				      
				<form class="col-lg-4" name="login" id="login" action="login.php?viewer={$gui->viewer}" method="post">
					<img style = "padding:10%;"class="img-responsive center-block" src="{$tlCfg->theme_dir}images/{$tlCfg->logo_login}">
					<!--input type="hidden" name="reqURI" value="{$gui->reqURI|escape:'url'}"/>
					<input type="hidden" name="destination" value="{$gui->destination|escape:'url'}"/-->
					<div class="form-group">
						<div class = "input-group">
							<div class= "input-group-addon">
								<label for="tl_login"><i class="fa fa-user"></i></label>
							</div>
							<input class="form-control" maxlength="{#LOGIN_MAXLEN#}" name="tl_login" id="tl_login" type="text" placeholder="{$labels.login_name}" required>
						</div>
					</div>

					<div class="form-group">
						<div class = "input-group">
							<div class= "input-group-addon">
								<label for="tl_password"><i class="fa fa-lock"></i></label>
							</div>
							<input class="form-control" name="tl_password" id="tl_password" type="password" placeholder="{$labels.password}" required>
						</div>
						{if $gui->note != ''}
							<div class="grid__container">
								<div class="user__feedback">
									{$gui->note}
								</div>
							</div>
						{/if}
					</div>
					
					<input class="btn btn-default center-block" type="submit" value="{$labels.btn_login}">
				</form>
			</div>
		</div>
	{/if}
	</body>
</html>
<!-- termina aqui o login-model-marcobidermann.tpl-->