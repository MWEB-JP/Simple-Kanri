<?php
/**
 * kanri.php
 * @copyright 2019 mweb.jp
 * @license GNU Public License V2.0
 */

//カスタム定義
require('../inc/custom.php');

// 機能の定義・初期化
require('../inc/kanri_start.php');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title><?php echo WEBSITE_TITLE; ?></title>

<meta name="keywords" content="" />
<meta name="description" content="" />

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="../">

<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">

<link rel="canonical" href="<?php echo HTTPS_SERVER; ?>/skill/" />

<link rel="icon" href="<?php echo HTTPS_SERVER; ?>/skill/img/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" href="<?php echo HTTPS_SERVER; ?>/skill/img/iphone-icon.png" sizes="180x180">

<link rel="stylesheet" href="./css/style.css" type="text/css">
<link rel="stylesheet" href="./css/kanri.css" type="text/css">

<script src="./js/kanri.js" charset="UTF-8"></script>
<script type="text/javascript">
//<![CDATA[

	var today = new Date();
	var mt = today.getMinutes().toString() + Math.floor(Math.random()*1000);
	var fragment = document.createDocumentFragment();
	//グループとメニュー
	var script0 = document.createElement("script");
	script0.src = "./js/xgroups.js?var="+mt;
	fragment.appendChild(script0);
	var script1 = document.createElement("script");
	script1.src = "./js/xmenus.js?var="+mt;
	fragment.appendChild(script1);
	//ヘッダーに追加
	var head = document.getElementsByTagName("head");
	head[0].appendChild(fragment);

	window.onload = function(){ kanri_start(); };
//]]>
</script>
</head>
<body ontouchstart="">

<header class="kanri">
	<div class="inner">
		<div id="logo"><?php echo WEBSITE_TITLE; ?></div>
	</div>
</header>

<div id="basis">
<div id="contents">

<article>
	<h1>管理画面</h1>

	<section>
		<h2>分類登録</h2>
		<form enctype="multipart/form-data" accept-charset="UTF-8" name="group_edit" id="group_edit" method="post" autocomplete="off" spellcheck="false">
			<input type="hidden" name="mode" value="" />
			<input type="hidden" name="action" value="" />
			<input type="hidden" name="navid" value="<?php echo fc_get_navid(); ?>" />
			<input type="hidden" name="token" value="" />
			<input type="hidden" name="order" value="" />
			<div id="group_edit_area">
				<div class="input_form both">
					<h3>分類ID</h3>
					<input type="text" name="group_id" value="" maxlength="80">
				</div>
				<div class="input_form">
					<h3>分類名</h3>
					<input type="text" name="group_name" value="" maxlength="80">
				</div>
			</div>
			<div class="input_line both" id="group_edit_check">
				<input type="button" value="分類登録" onclick="group_action_check();">
				<input type="checkbox" name="delete_group" id="delete_group" value="1" />
				<label for="delete_group">一致する分類を削除します</label>
			</div>
			<div class="input_line both" id="group_edit_confirm">
				<input type="button" value="戻る" onclick="group_action_cancel();">
				<input type="button" value="実行" onclick="group_action_confirm();">
				<input type="checkbox" name="exec_group" id="exec_group" value="1" />
				<label for="exec_group" id="group_action_message">分類を新規作成します</label>
			</div>
			<div class="input_line both">
				<img src="./img/icon/guide.png" onclick="group_guide();">
				<span id="group_message"></span>
			</div>
		</form>
	</section>


	<section>
		<h2>メニュー登録</h2>
		<form enctype="multipart/form-data" accept-charset="UTF-8" name="list_edit" id="list_edit" method="post" autocomplete="off">
			<input type="hidden" name="mode" value="" />
			<input type="hidden" name="action" value="" />
			<input type="hidden" name="navid" value="<?php echo fc_get_navid(); ?>" />
			<input type="hidden" name="token" value="" />
			<div class="input_area" id="menu_list_area">
				<ul class="box_list">
					<li>
						<span>分類ID<input type="radio" name="pos_up" value="1" /></span>
						<select size="3" name="group_select" id="group_select" onchange="group_select_change();">
							<option value=""></option>
						</select>
					</li>
					<li>
						<span>分類に割付け</span>
						<select size="3" name="group_conte" id="group_conte">
							<option value=""></option>
						</select>
					</li>
					<li class="buttons">
						<input type="button" value="×" onclick="page_delete_from_group();">
						<input type="button" value="←" onclick="page_add_to_group();">
						<input type="button" value="↑" onclick="kanri_pos_move(0);">
						<input type="button" value="↓" onclick="kanri_pos_move(1);">
					</li>
					<li>
						<span>メニューID<input type="radio" name="pos_up" value="2" checked="checked" /></span>
						<select size="3" name="page_folder" id="page_folder" onchange="folder_select_change();">
							<option value=""></option>
						</select>
					</li>
					<li>
						<span>ページファイル<input type="radio" name="pos_up" value="3" /></span>
						<select size="3" name="page_select" id="page_select" onchange="page_select_change();">
							<option value=""></option>
						</select>
					</li>
				</ul>
			</div>
			<div class="input_line both" id="list_edit_check">
				<input type="button" value="メニュー登録" onclick="list_action_check();">
			</div>
			<div class="input_line both" id="list_edit_confirm">
				<input type="button" value="戻る" onclick="list_action_cancel();">
				<input type="button" value="実行" onclick="list_action_confirm();">
			</div>
			<div class="input_line both">
				<img src="./img/icon/guide.png" onclick="list_guide();">
				<span id="list_message"></span>
			</div>
		</form>
	</section>

	<section>
		<h2>ページ登録</h2>
		<form enctype="multipart/form-data" accept-charset="UTF-8" name="page_edit" id="page_edit" method="post" autocomplete="off" spellcheck="false">
			<input type="hidden" name="mode" value="" />
			<input type="hidden" name="action" value="" />
			<input type="hidden" name="navid" value="<?php echo fc_get_navid(); ?>" />
			<input type="hidden" name="token" value="" />
			<input type="hidden" name="order" value="" />
			<div id="page_edit_area">
				<div class="input_form">
					<h3>メニューID</h3>
					<input type="text" name="dir_name" value="" maxlength="80">
				</div>
				<div class="input_form">
					<h3>メニュー名</h3>
					<input type="text" name="menu_name" value="" maxlength="80">
				</div>
				<div class="input_form">
					<h3>ページファイル</h3>
					<input type="text" name="page_file" value="" maxlength="80">
				</div>
				<div class="input_form">
					<h3>ページタイトル</h3>
					<input type="text" name="page_title" value="" maxlength="80">
				</div>
				<div class="input_form">
					<h3>スクリプト</h3>
					<input type="text" name="page_script" value="" maxlength="80">
				</div>
				<div class="input_form">
					<h3>スタイルシート</h3>
					<input type="text" name="page_style" value="" maxlength="80">
				</div>
			</div>
			<div class="input_line both" id="page_edit_check">
				<input type="button" value="ページ登録" onclick="page_action_check();">
				<input type="checkbox" name="delete_page" id="delete_page" value="1" />
				<label for="delete_page">一致するページを削除します</label>
			</div>
			<div class="input_line both" id="page_edit_confirm">
				<input type="button" value="戻る" onclick="page_action_cancel();">
				<input type="button" value="実行" onclick="page_action_confirm();">
				<input type="checkbox" name="exec_page" id="exec_page" value="1" />
				<label for="exec_page" id="page_action_message">ページを新規作成します</label>
			</div>
			<div class="input_line both">
				<img src="./img/icon/guide.png" onclick="page_guide();">
				<span id="page_message"></span>
			</div>
		</form>
	</section>

</article>

</div>
</div>

<footer>
	<noscript>
	このサイトの表示にはJavaスクリプトが必要です。Javaスクリプトを有効にして下さい。<br>
	</noscript>
	<div id="copyright"><a href="http://www.mweb.jp/">&copy; 2016 mweb.jp</a></div>
</footer>

</body>
</html>
