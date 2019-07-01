<?php
/**
 * ajax_kanri.php
 * 管理画面
 * @copyright 2013 mweb.jp, 2019 mweb.jp
 * @license GNU Public License V2.0
 */
$results = "";
$comment = "";
$sessid = "";
$token = "";
//////////////////////////////////////////////////
if ($current_action=="page_check") {
	//ページ登録開始
	$results = 'ok';
	$comment = "";
	if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
		$comment = '無効なアクセスです';
		$results = 'ng';
		sleep(3);
	} else {
		$token = fc_get_token();
		$_SESSION['current_token'] = $token;
	}
	if($results == 'ok') {
		//ページ名
		$page_name = fc_prepare_input_length($_POST['page_name'], 80);
		$page_name = fc_sanitiz_id($page_name);
	}
	//セッションクローズ
	fc_session_close();

	$order = "";
	if($results == 'ok') {
		//ファイルの有無を確認
		try {
			$dir =  explode('-', $page_name);
			$dir_name = $dir[0];
			$realdir = BASE_DIR . '/pages/' . $dir_name;
			$realpath = $realdir . '/' . $page_name . '.php';

			if (!is_dir($realdir)) {
				$comment .= "新しいフォルダ、ページファイルを登録します。";
				$order = "new_dir";
			} else if (!is_file($realpath)){
				$comment .= "ページファイルを追加登録します。";
				$order = "new_file";
			} else {
				$comment .= "ページのタイトルを更新します。";
				$order = "update";
			}
		} catch ( Exception $e ) {
			$comment .= 'フォルダを参照できません。';
			$results = 'ng';
		}
	}

	//応答
	$rtn = array();
	$rtn['results'] = $results;
	$rtn['comment'] = fc_html_output($comment);
	$rtn['token'] = $token;
	$rtn['order'] = $order;
	echo json_encode($rtn);

//////////////////////////////////////////////////
} else if ($current_action=="page_edit") {
	//ページ登録
	$results = 'ok';
	$comment = "";
	if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
		$comment = '無効なアクセスです';
		$results = 'ng';
		sleep(3);
	} else if(empty($current_token) || $current_token != $_SESSION['current_token']) {
		$comment = 'アクセスが無効になりました。';
		$results = 'ng';
		sleep(3);
	} else {
		$token = fc_get_token();
		$_SESSION['current_token'] = $token;
	}
	if($results == 'ok') {
		try {
			$order = fc_prepare_input_length($_POST['order'], 20);
			$dir_name = fc_prepare_input_length($_POST['dir_name'], 80);
			$menu_name = fc_prepare_input_length($_POST['menu_name'], 80);
			$page_file = fc_prepare_input_length($_POST['page_file'], 80);
			$page_title = fc_prepare_input_length($_POST['page_title'], 80);
			$page_script = fc_prepare_input_length($_POST['page_script'], 80);
			$page_style = fc_prepare_input_length($_POST['page_style'], 80);

			if (empty($order) || empty($dir_name) || empty($menu_name) || empty($page_file) || empty($page_title)) {
				$comment = '未設定の項目があります。';
				throw new Exception('error');
			}
			$menu = htmlspecialchars($menu_name);
			if ($menu != $menu_name) {
				$comment = 'メニュー名として使用できない文字があります。';
				throw new Exception('error');
			}
			$title = htmlspecialchars($page_title);
			if ($title != $page_title) {
				$comment = 'タイトル名として使用できない文字があります。';
				throw new Exception('error');
			}
			$file = fc_sanitiz_id($page_file);
			if ($file != $page_file) {
				$comment = 'ページファイルとして使用できない文字があります。';
				throw new Exception('error');
			}
			$dir =  explode('-', $page_file);
			if ($dir[0] != $dir_name) {
				$comment = 'ページファイル中のメニューIDが一致しません。';
				throw new Exception('error');
			}
			if ($dir_name == 'archive' || $dir_name == 'common' || $dir_name == 'top') {
				$comment = 'このメニューIDは使用できません。';
				throw new Exception('error');
			}
			$check = preg_replace('/[^0-9a-zA-Z_,\-]/', '', $page_script);
			if ($check != $page_script) {
				$comment = '登録スクリプトとして使用できない文字があります。'. $check;
				throw new Exception('error');
			}
			$check = preg_replace('/[^0-9a-zA-Z_,\-]/', '', $page_style);
			if ($check != $page_style) {
				$comment = '登録スタイルとして使用できない文字があります。';
				throw new Exception('error');
			}
		} catch ( Exception $e ) {
			$results = 'rt';
		}
	}
	if($results == 'ok') {
		$realdir = BASE_DIR . '/pages/' . $dir_name;
		$file_path = $realdir . '/' . $page_file . '.php';
		$back_path = $realdir . '/' . $page_file . '.bak';
		$json_path = $realdir . '/xpage.json';
		$text_path = $realdir . '/xpage.txt';
		$js_path = $realdir . '/xpage.js';
		try {
			if($order == 'update') {
				$comment = 'メニューの更新を実行しました。';
			} else if($order == 'delete') {
				$comment = 'ページの削除を実行しました。';
			} else if($order == 'new_dir') {
				$comment = '新規メニュー作成を実行しました。';
			} else if($order == 'new_file') {
				$comment = 'ページ追加を実行しました。';
			} else {
				$comment = '不明なコマンドです。';
				throw new Exception('error');
			}
			if($order == 'delete') {
				@rename($file_path, $back_path);
			} else {
				if (is_file($back_path)) {
					@rename($back_path, $file_path);
				}
				fc_page_file($dir_name, $page_file, $page_title);
			}
			//jsonファイルがある場合は、ファイルからサブメニューを読み取る
			if (is_file($json_path)) {
				$input = @file_get_contents($json_path);
				$input = mb_convert_encoding($input, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			} else {
				$page_menu = fc_prepare_input_length($_POST['page_menu'], 5000);
				$input = mb_convert_encoding($page_menu, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			}
			$array = json_decode($input, true); //連想配列として読み込む
			//変更・追加・削除
			$add = true;
			if ($order == 'delete') $add = false;
			for ($i = 0; $i < count($array); $i++) {
				$name = $array[$i]['name'];
				$dir =  explode('-', $name);
				if ($dir[0] != $dir_name) {
					//不一致の定義を削除
					unset($array[$i]);
				} else if ($name == $page_file) {
					if($order == 'delete'){
						unset($array[$i]);
					} else {
						$array[$i]['title'] = $page_title;
						if (!empty($page_script)) {
							$array[$i]['script'] = $page_script;
						} else {
							if(array_key_exists('script',$array[$i])){
								unset($array[$i]['script']);
							}
						}
						if (!empty($page_style)) {
							$array[$i]['style'] = $page_style;
						} else {
							if(array_key_exists('style', $array[$i])){
								unset($array[$i]['style']);
							}
						}
					}
					$add = false;
				}
			}
			if($add){
				$add_array = array('title'=>$page_title,'name'=>$page_file);
				if (!empty($page_script)) {
					$add_array['script'] = $page_script;
				}
				if (!empty($page_style)) {
					$add_array['style'] = $page_style;
				}
				//オプションの追加
				$array[] = $add_array;
			}
			//ページメニュー
			$page_menu = array_values($array);

			//ファイルに出力
			$json = json_encode($page_menu, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		    @file_put_contents($json_path, $json);
			@chmod($json_path, 0660);
			//テキスト出力
			$output = 'page = new Array();'."\n";
			for ($i = 0; $i < count($page_menu); $i++) {
				$src = json_encode($page_menu[$i], JSON_UNESCAPED_UNICODE);
				$output .= 'page['.$i.']=' . $src . ';' . "\n";
			}
			$fp = @fopen($text_path,"w");
			if ($fp !== false) {
				@fwrite($fp, $output);
				@fclose($fp);
			}
			@chmod($text_path, 0660);
			@rename($text_path, $js_path);

			//メニュー登録・削除
			$menu_json_path = BASE_DIR . '/js/xmenus.json';
			$menu_text_path = BASE_DIR . '/js/xmenus.txt';
			$menu_js_path = BASE_DIR . '/js/xmenus.js';

			$act = "add";
			if(count($page_menu) < 1) {
				//全て削除でメニューから削除
				$act = "delete";
			}
			if (is_file($menu_json_path)) {
				$input = @file_get_contents($menu_json_path);
				$input = mb_convert_encoding($input, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
				$mmenu = json_decode($input, true); //連想配列として読み込む
				for ($i = 0; $i < count($mmenu); $i++) {
					if ($mmenu[$i]['name'] == $dir_name) {
						if($act == "delete"){
							unset($mmenu[$i]);
						} else {
							$mmenu[$i]['title'] = $menu_name;
						}
						$act = "";
					}
				}
				if($act == "add"){
					$add_array = array('title'=>$menu_name, 'name'=>$dir_name, 'gname'=>'');
					$mmenu[] = $add_array;
				}
			}
			//メニュー
			$mmenu = array_values($mmenu);

			//ファイルに出力
			$json = json_encode($mmenu, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		    @file_put_contents($menu_json_path, $json);
			@chmod($menu_json_path, 0660);
			//テキスト出力
			$output = 'mmenu = new Array();'."\n";
			for ($i = 0; $i < count($mmenu); $i++) {
				$src = json_encode($mmenu[$i], JSON_UNESCAPED_UNICODE);
				$output .= 'mmenu['.$i.']=' . $src . ';' . "\n";
			}
			$fp = @fopen($menu_text_path,"w");
			if ($fp !== false) {
				@fwrite($fp, $output);
				@fclose($fp);
			}
			@chmod($menu_text_path, 0660);
			@rename($menu_text_path, $menu_js_path);

		} catch ( Exception $e ) {
			$comment = 'ファイルを登録・編集できません。';
			$results = 'ng';
		}
	}
	//セッションクローズ
	fc_session_close();

	//応答
	$rtn = array();
	$rtn['results'] = $results;
	$rtn['comment'] = fc_html_output($comment);
	$rtn['token'] = $token;
	$rtn['page_menu'] = $page_menu;
	$rtn['mmenu'] = $mmenu;
	echo json_encode($rtn);

//////////////////////////////////////////////////
} else if ($current_action=="group_check") {
	//分類登録
	$results = 'ok';
	$comment = "";
	if (!isset($_SESSION['login_user'])  || empty($_SESSION['login_user'])) {
		$comment = '無効なアクセスです';
		$results = 'ng';
		sleep(3);
	} else {
		$token = fc_get_token();
		$_SESSION['current_token'] = $token;
	}

	//セッションクローズ
	fc_session_close();

	//応答
	$rtn = array();
	$rtn['results'] = $results;
	$rtn['comment'] = fc_html_output($comment);
	$rtn['token'] = $token;
	echo json_encode($rtn);

//////////////////////////////////////////////////
} else if ($current_action=="group_edit") {
	//分類登録
	$results = 'ok';
	$comment = "";
	if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
		$comment = '無効なアクセスです';
		$results = 'ng';
		sleep(3);
	} else if(empty($current_token) || $current_token != $_SESSION['current_token']) {
		$comment = 'アクセスが無効になりました。';
		$results = 'ng';
		sleep(3);
	} else {
		$token = fc_get_token();
		$_SESSION['current_token'] = $token;
	}
	if($results == 'ok') {
		try {
			$order = fc_prepare_input_length($_POST['order'], 20);
			$group_id = fc_prepare_input_length($_POST['group_id'], 80);
			$group_name = fc_prepare_input_length($_POST['group_name'], 80);

			if (empty($order) || empty($group_id) || empty($group_name)) {
				$comment = '未設定の項目があります。';
				throw new Exception('error');
			}
			$gid = fc_sanitiz_id($group_id);
			if ($gid != $group_id) {
				$comment = '分類IDとして使用できない文字があります。';
				throw new Exception('error');
			}
			$gnam = htmlspecialchars($group_name);
			if ($gnam != $group_name) {
				$comment = '分類名として使用できない文字があります。';
				throw new Exception('error');
			}
		} catch ( Exception $e ) {
			$results = 'rt';
		}
	}
	if($results == 'ok') {
		$json_path = BASE_DIR . '/js/xgroups.json';
		$text_path = BASE_DIR . '/js/xgroups.txt';
		$js_path = BASE_DIR . '/js/xgroups.js';
		try {
			if($order == 'update') {
				$comment = '分類の更新を実行しました。' ;
			} else if($order == 'delete') {
				$comment = '分類の削除を実行しました。';
			} else {
				$comment = '不明なコマンドです。';
				throw new Exception('error');
			}
			//jsonファイルがある場合は、ファイルから分類を読み取る
			if (is_file($json_path)) {
				$input = @file_get_contents($json_path);
				$input = mb_convert_encoding($input, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			} else {
				$group_str = fc_prepare_input_length($_POST['groups'], 5000);
				$input = mb_convert_encoding($group_str, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			}
			$array = json_decode($input, true); //連想配列として読み込む
			//変更・追加・削除
			$add = true;
			if ($order == 'delete') $add = false;
			for($i=0; $i < count($array); $i++) {
				$name = $array[$i]['name'];
				if ($name == $group_id) {
					if($order == 'delete'){
						unset($array[$i]);
					} else {
						$array[$i]['title'] = $group_name;
					}
					$add = false;
				}
			}
			if($add){
				$add_array = array('title'=>$group_name,'name'=>$group_id);
				$array[] = $add_array;
			}
			$groups = array_values($array);

			//ファイルに出力
			$json = json_encode($groups, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		    @file_put_contents($json_path, $json);
			@chmod($json_path, 0660);
			//テキスト出力
			$output = 'groups = new Array();'."\n";
			for ($i = 0; $i < count($groups); $i++) {
				$src = json_encode($groups[$i], JSON_UNESCAPED_UNICODE);
				$output .= 'groups['.$i.']=' . $src . ';' . "\n";
			}
			$fp = @fopen($text_path,"w");
			if ($fp !== false) {
				@fwrite($fp, $output);
				@fclose($fp);
			}
			@chmod($text_path, 0660);
			@rename($text_path, $js_path);
		} catch ( Exception $e ) {
			$comment = 'ファイルを登録・編集できません。';
			$results = 'ng';
		}
	}
	//セッションクローズ
	fc_session_close();

	//応答
	$rtn = array();
	$rtn['results'] = $results;
	$rtn['comment'] = fc_html_output($comment);
	$rtn['token'] = $token;
	$rtn['groups'] = $groups;
	echo json_encode($rtn);

//////////////////////////////////////////////////
} else if ($current_action=="list_check") {
	//メニュー登録開始
	$results = 'ok';
	$comment = "";
	if (!isset($_SESSION['login_user'])  || empty($_SESSION['login_user'])) {
		$comment = '無効なアクセスです';
		$results = 'ng';
		sleep(3);
	} else {
		$token = fc_get_token();
		$_SESSION['current_token'] = $token;
	}

	//セッションクローズ
	fc_session_close();

	if($results == 'ok') {
		$comment = 'メニューの変更内容を登録します。';
	}

	//応答
	$rtn = array();
	$rtn['results'] = $results;
	$rtn['comment'] = fc_html_output($comment);
	$rtn['token'] = $token;
	echo json_encode($rtn);

//////////////////////////////////////////////////
} else if ($current_action=="list_edit") {
	//メニュー登録
	$results = 'ok';
	if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
		$comment = '無効なアクセスです';
		$results = 'ng';
		sleep(3);
	} else if(empty($current_token) || $current_token != $_SESSION['current_token']) {
		$comment = 'アクセスが無効になりました。';
		$results = 'ng';
		sleep(3);
	} else {
		$token = fc_get_token();
		$_SESSION['current_token'] = $token;
	}
	if($results == 'ok') {
		try {
			$comment = 'メニューの更新を実行しました。';

			//分類メニュー
			$groups_path = BASE_DIR . '/js/xgroups.json';
			$groups_text_path = BASE_DIR . '/js/xgroups.txt';
			$groups_js_path = BASE_DIR . '/js/xgroups.js';

			//管理画面で変更されたメニュー
			$groups_str = fc_prepare_input_length($_POST['groups'], 5000);
			$input = mb_convert_encoding($groups_str, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			$array = json_decode($input, true); //連想配列として読み込む
			$groups = array_values($array);

			//ファイルに出力
			$json = json_encode($groups, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		    @file_put_contents($groups_path, $json);
			@chmod($groups_path, 0660);
			//テキスト出力
			$output = 'groups = new Array();'."\n";
			for($i=0; $i < count($groups); $i++) {
				$src = json_encode($groups[$i], JSON_UNESCAPED_UNICODE);
				$output .= 'groups['.$i.']=' . $src . ';' . "\n";
			}
			$fp = @fopen($groups_text_path,"w");
			if ($fp !== false) {
				@fwrite($fp, $output);
				@fclose($fp);
			}
			@chmod($groups_text_path, 0660);
			@rename($groups_text_path, $groups_js_path);

			//ページメニュー
			$page_top = "";
			if (isset($_POST['pagenm'])) {
				$page_name = fc_prepare_input_length($_POST['pagenm'], 80);
				$page_name = fc_sanitiz_id($page_name);
				$dir =  explode('-', $page_name);
				$dir_name = $dir[0];
				$realdir = BASE_DIR . '/pages/' . $dir_name;
				$page_path = $realdir . '/xpage.json';
				$page_text_path = $realdir . '/xpage.txt';
				$page_js_path = $realdir . '/xpage.js';

				//管理画面で変更されたメニュー
				$pages_str = fc_prepare_input_length($_POST['pmenu'], 5000);
				if($pages_str != '') {
					$input = mb_convert_encoding($pages_str, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
					$array = json_decode($input, true); //連想配列として読み込む
					$page_menu = array_values($array);

					//ページトップの抽出
					$page_top = $page_menu[0]['name'];

					//ファイルに出力
					$json = json_encode($page_menu, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			    	@file_put_contents($page_path, $json);
					@chmod($page_path, 0660);

					//テキスト出力
					$output = 'page = new Array();'."\n";
					for($i=0; $i < count($page_menu); $i++) {
						$src = json_encode($page_menu[$i], JSON_UNESCAPED_UNICODE);
						$output .= 'page['.$i.']=' . $src . ';' . "\n";
					}
					$fp = @fopen($page_text_path,"w");
					if ($fp !== false) {
						@fwrite($fp, $output);
						@fclose($fp);
					}
					@chmod($page_text_path, 0660);
					@rename($page_text_path, $page_js_path);
				}
			}

			//メニュー
			$mmenu_path = BASE_DIR . '/js/xmenus.json';
			$mmenu_text_path = BASE_DIR . '/js/xmenus.txt';
			$mmenu_js_path = BASE_DIR . '/js/xmenus.js';

			//管理画面で変更されたメニュー
			$mmenu_str = fc_prepare_input_length($_POST['mmenu'], 5000);
			$input = mb_convert_encoding($mmenu_str, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			$array = json_decode($input, true); //連想配列として読み込む
			$mmenu = array_values($array);

			if($page_top != "") {
				//ページトップの登録
				$dir =  explode('-', $page_top);
				$dir_name = $dir[0];
				for ($i = 0; $i < count($mmenu); $i++) {
					if ($mmenu[$i]['name'] == $dir_name) {
						$mmenu[$i]['top'] = $page_top;
						break;
					}
				}
			}

			//ファイルに出力
			$json = json_encode($mmenu, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		    @file_put_contents($mmenu_path, $json);
			@chmod($mmenu_path, 0660);
			//テキスト出力
			$output = 'mmenu = new Array();'."\n";
			for($i=0; $i < count($mmenu); $i++) {
				$src = json_encode($mmenu[$i], JSON_UNESCAPED_UNICODE);
				$output .= 'mmenu['.$i.']=' . $src . ';' . "\n";
			}
			$fp = @fopen($mmenu_text_path,"w");
			if ($fp !== false) {
				@fwrite($fp, $output);
				@fclose($fp);
			}
			@chmod($mmenu_text_path, 0660);
			@rename($mmenu_text_path, $mmenu_js_path);

		} catch ( Exception $e ) {
			$comment = 'ファイルを登録・編集できません。';
			$results = 'ng';
		}
	}
	//セッションクローズ
	fc_session_close();

	//応答
	$rtn = array();
	$rtn['results'] = $results;
	$rtn['comment'] = fc_html_output($comment);
	$rtn['token'] = $token;
	$rtn['mmenu'] = $mmenu;
	echo json_encode($rtn);

//////////////////////////////////////////////////
} else {
	fc_session_destroy();
	sleep(3);

	//応答
	$rtn = array();
	$rtn['results'] = 'ng';
	$rtn['comment'] = 'Invalid Access!';
	echo json_encode($rtn);

}
//////////////////////////////////////////////////
// ページ情報の更新
function fc_page_file($dir_name, $page_file, $page_title) {
	$realdir = BASE_DIR . '/pages/' . $dir_name;
	$file_path = $realdir . '/' . $page_file . '.php';
	if (!is_dir($realdir)) {
		mkdir($realdir);
		chmod($realdir, 0775);
	}
	if (!is_file($file_path)) {
		$base_file = BASE_DIR . '/pages/common/base.php';
		copy($base_file, $file_path);
		chmod($file_path, 0660);
	}
	$fp = @fopen($file_path,"rb");
	if ($fp !== false) {
		$output = @fread($fp, filesize($file_path));
		@fclose($fp);
	} else throw new Exception('error');
	//タイトルの更新
	$key = '<h1>';
	if (stripos($output, $key) !== false) {
		$next = stristr($output, $key);
		$prev = stristr($output, $key, true);
		$next = stristr($next, '</h1>');
		$output = $prev;
		$output .= '<h1>' . $page_title;
		$output .= $next;
	}
	//編集日付の更新
	$key = '<span class="date">';
	if (stripos($output, $key) !== false) {
		$next = stristr($output, $key);
		$prev = stristr($output, $key, true);
		$next = stristr($next, '</span>');
		$output = $prev;
		$output .= '<span class="date">';
		$output .= strftime("%Y年%m月%d日 %H:%M ",strtotime(fc_date()));
		$output .= 'by ' . fc_html_output($_SESSION['login_user']);
		$output .= $next;
	}
	//ファイルに書き出し
	$fp = @fopen($file_path,"w");
	if ($fp !== false) {
		@fwrite($fp, $output);
		@fclose($fp);
	} else throw new Exception('error');
	return $output;
}
?>
