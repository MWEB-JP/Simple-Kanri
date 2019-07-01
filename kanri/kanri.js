/**
 * kanri.js
 * @copyright 2019 mweb.jp
 * @license GNU Public License V2.0
 */
//グローバル変数
var group_name = "";
var page_name = "";
var item_name = "";
var page_title = "";
var page = new Array();
//管理画面表示////////////////////////////////////
//スタートアップ
function kanri_start(){
	//セレクトのオプション項目の設定
	document.getElementById("page_message").textContent = "";
	document.getElementById("group_message").textContent = "";
	//分類IDオプション
	group_select_options("");
	//メニューIDオプション
	page_folder_options("")
	return false;
}
//////////////////////////////////////////////////
//分類IDオプション
function group_select_options(grp){
	var str = "";
	var chk = "";
	for (var i = 0; i < groups.length; i++) {
		var key = groups[i].name;
		str = str + "<option value='"+key+"' >"+key+"</option>";
		if(grp != '' && key == grp) chk = key;
	}
	document.getElementById("group_select").innerHTML = str;
	document.getElementById("group_select").value = chk;
}
//メニューIDオプション
function page_folder_options(dir){
	var mnu = "";
	var chk = "";
	for (var i = 0; i < mmenu.length; i++) {
		var key = mmenu[i].name;
		mnu = mnu + "<option value='"+key+"' >"+key+"</option>";
		if(dir != '' && key == dir) chk = key;
	}
	document.getElementById("page_folder").innerHTML = mnu;
	document.getElementById("page_folder").value = chk;
}
//ページファイルオプション
function page_select_options(pg){
	var conte = "";
	var chk = "";
	for (var i = 0; i < page.length; i++) {
		var key = page[i].name;
		conte = conte + "<option value='"+key+"' >"+key+"</option>";
		if(pg != '' && key == pg) chk = key;
	}
	document.getElementById("page_select").innerHTML = conte;
	document.getElementById("page_select").value = chk;
}
//メニュータイトルの取得
function page_mmenu_title(dir){
	var title = "";
	for (var i = 0; i < mmenu.length; i++) {
		var check = mmenu[i].name;
		if(check == dir) {
			title = mmenu[i].title;
			break;
		}
	}
	return title;
}
//分類名の取得
function group_title(grp){
	var title = "";
	for (var i = 0; i < groups.length; i++) {
		var check = groups[i].name;
		if(check == grp) {
			title = groups[i].title;
			break;
		}
	}
	return title;
}
//分類に割付けオプション
function group_conte_options(grp){
	var conte = "";
	for (var i = 0; i < mmenu.length; i++) {
		var check = mmenu[i].gname;
		var key = mmenu[i].name;
		if(check == grp) {
			conte = conte + "<option value='"+key+"' >"+key+"</option>";
		}
	}
	document.getElementById("group_conte").innerHTML = conte;
}
//////////////////////////////////////////////////
//分類オプション選択//////////////////////////////
function group_select_change(){
	var obj = document.getElementById("group_select");
	if(obj != null){
		//分類ID、分類名
		var gname = obj.value;
		var gtitle = group_title(gname);
		//分類登録フォーム
		var frm = document.getElementById("group_edit");
		frm.group_id.value = gname;
		frm.group_name.value = gtitle;
		//分類に割りつけられた項目リスト
		group_conte_options(gname);
		document.getElementById("list_edit").pos_up.value = 1;
	}
	return false;
}
//分類から割付け削除//////////////////////////////
function page_delete_from_group(){
	var dir_name = document.getElementById("group_conte").value;
	var gname = "";
	if(dir_name != ""){
		for (var i = 0; i < mmenu.length; i++) {
			var check = mmenu[i].name;
			if(check == dir_name) {
				gname = mmenu[i].gname;
				//メニューのグループを消去
				mmenu[i].gname = "";
				break;
			}
		}
		var msg = "メニューの登録が必要です。";
		document.getElementById("list_message").textContent = msg;
	}
	var check = document.getElementById("group_select").value;
	if(gname != "" && gname == check){
		//分類に割付けオプション
		group_conte_options(gname);
	}
	return false;
}
//分類に割付け////////////////////////////////////
function page_add_to_group(){
	var gname = document.getElementById("group_select").value;
	var dir_name = document.getElementById("page_folder").value;
	if(gname != "" && dir_name != ""){
		for (var i = 0; i < mmenu.length; i++) {
			var check = mmenu[i].name;
			if(check == dir_name) {
				//メニューのグループを設定
				mmenu[i].gname = gname;
				break;
			}
		}
		//分類に割付けオプション
		group_conte_options(gname);
		var msg = "メニューの登録が必要です。";
		document.getElementById("list_message").textContent = msg;
	}
	return false;
}
//メニュー位置調整////////////////////////////////
function kanri_pos_move(dc){
	var frm = document.getElementById("list_edit");
	var sel = frm.pos_up.value;
	if(sel == 1) {
		group_pos_up(dc);
	} else if (sel == 2) {
		menu_pos_up(dc);
	} else if (sel == 3) {
		page_pos_up(dc);
	}
}
function kanri_array_move(dc, arr, item){
	//現在の位置
	var pos_from = 0;
	var pos_to = 0;
	for (var i = 0; i < arr.length; i++) {
		var check = arr[i].name;
		if(check == item) {
			pos_from = i;
			pos_to = i;
			break;
		}
	}
	//移動する位置
	if(dc == 0) {
		// up
		pos_to = pos_from - 1;
		if(pos_to >= 0) {
			var tmp =  arr[pos_to];
			arr[pos_to] = arr[pos_from];
			arr[pos_from] = tmp;
		} else if(pos_from == 0){
			var tmp =  arr.shift();
			arr.push(tmp);
			pos_to = arr.length - 1;
		}
	} else {
		//down
		pos_to = pos_from + 1;
		if(pos_to < arr.length) {
			var tmp =  arr[pos_to];
			arr[pos_to] = arr[pos_from];
			arr[pos_from] = tmp;
		} else if(pos_from == (arr.length - 1)){
			var tmp =  arr.pop();
			arr.unshift(tmp);
			pos_to = 0;
		}
	}
	return pos_to;
}
function page_pos_up(dc){
	var pg_name = document.getElementById("page_select").value;
	if(pg_name != ""){
		var pos = kanri_array_move(dc, page, pg_name);
		var msg = "メニューの登録が必要です。";
		document.getElementById("list_message").textContent = msg;
	}
	//ページオプション
	page_select_options(pg_name);
	return false;
}
function menu_pos_up(dc){
	var dir_name = document.getElementById("page_folder").value;
	var gname = "";
	if(dir_name != ""){
		var pos = kanri_array_move(dc, mmenu, dir_name);
		console.log(pos);
		gname = mmenu[pos].gname;
		var msg = "メニューの登録が必要です。";
		document.getElementById("list_message").textContent = msg;
	}
	//メニューIDオプション
	page_folder_options(dir_name);
	//分類に割付けオプション
	var check = document.getElementById("group_select").value;
	if(gname != "" && gname == check){
		group_conte_options(gname);
	}
	return false;
}
function group_pos_up(dc){
	var grp_name = document.getElementById("group_select").value;
	if(grp_name != ""){
		var pos = kanri_array_move(dc, groups, grp_name);
		var msg = "メニューの登録が必要です。";
		document.getElementById("list_message").textContent = msg;
	}
	//グループオプション
	group_select_options(grp_name);
	return false;
}
//フォルダオプション選択//////////////////////////
function folder_select_change(){
	var obj = document.getElementById("page_folder");
	if(obj != null){
		//メニュー項目
		var dir_name = obj.value;
		var title_name = page_mmenu_title(dir_name);
		//ページ登録フォーム
		var frm = document.getElementById("page_edit");
		frm.dir_name.value = dir_name;
		frm.menu_name.value = title_name;
		frm.page_file.value = "";
		frm.page_title.value = "";
		frm.page_script.value = "";
		frm.page_style.value = "";
		document.getElementById("list_edit").pos_up.value = 2;

		//フォルダ内のxpage.js読み込み
		var today = new Date();
		var mt = today.getMinutes().toString() + Math.floor(Math.random()*1000);
		page = new Array();
		var script = document.createElement("script");
		script.type = 'text/javascript';
		script.src = "./pages/"+dir_name+"/xpage.js?var="+mt;
		var head = document.getElementsByTagName("head");
		var array  = document.getElementsByTagName("script");
		var old_script = array[array.length - 1];
		if( old_script.src.indexOf("/xpage.js") > 0) {
			head[0].replaceChild(script, old_script);
		} else {
			head[0].appendChild(script);
		}

		//読み込み完了後に処理
		setTimeout(function() {
			if(page.length < 1) {
				//読み込み完了待ち
				setTimeout(arguments.callee, 100);
			}else{
				//ページファイルオプション
				page_select_options('');
			}
		}, 100);
	}
	return false;
}
//ページ選択//////////////////////////////////////
function page_select_change(){
	var obj = document.getElementById("page_select");
	if(obj != null){
		//ページ項目
		var pname = obj.value;
		var ptitle = "";
		var pscript = "";
		var pstyle = "";
		for (var i = 0; i < page.length; i++) {
			var check = page[i].name;
			if(check == pname) {
				ptitle = page[i].title;
				if(typeof page[i].script != 'undefined') pscript = page[i].script;
				if(typeof page[i].style != 'undefined') pstyle = page[i].style;
				break;
			}
		}
		//メニュー項目
		var dir_name = pname.split('-').shift();
		var title_name = page_mmenu_title(dir_name);
		//ページ登録フォーム
		var frm = document.getElementById("page_edit");
		frm.dir_name.value = dir_name;
		frm.menu_name.value = title_name;
		frm.page_file.value = pname;
		frm.page_title.value = ptitle;
		frm.page_script.value = pscript;
		frm.page_style.value = pstyle;
		document.getElementById("list_edit").pos_up.value = 3;
	}
	return false;
}
//////////////////////////////////////////////////
//ページ登録//////////////////////////////////////
var page_msg_cnt = 0;
var page_msg = ["ページ登録の手順と機能を説明します。",
"メニューIDは、半角英数字と'_'（アンダーバー）を使用して下さい。",
"メニュー名は、実際のメニューの表示名になります。",
"ページファイルは、メニューIDに'-'（ハイフン）を付けた名前とします。",
"ページタイトルまでが、必須入力になります。",
"登録時、既存のメニューID、ページファイルが無ければ新規作成になります。",
"新規は、メニューIDでフォルダを作成し、ひな型からページファイルを登録します。",
"既存のものがあれば、メニュー名、ページタイトルの更新になります。",
"削除は、メニューから削除するだけで、ファイルは削除しません。",
"一旦削除したファイルは、同じ名称での登録により復活できます。",
"完全な削除は、システム管理者に連絡して下さい。",
"スクリプト、スタイルシートはオプション（管理者経由）です。"];
//ページ登録のガイドメッセージ表示////////////////
function page_guide(){
	if(page_msg_cnt >= page_msg.length) page_msg_cnt = 0;
	document.getElementById("page_message").textContent = page_msg[page_msg_cnt];
	console.log(page_msg_cnt);
	page_msg_cnt = page_msg_cnt + 1;
	return false;
}
var started = false;
//ページ登録開始//////////////////////////////////
function page_action_check(){
	if (started == true) {
		return false;
	}
	started = true;
	document.getElementById("page_message").textContent = "";

	var frm = document.getElementById("page_edit");
	var navid = frm.navid.value;
	frm.token.value = "";
	frm.order.value = "";

	var dir_name = frm.dir_name.value;
	var menu_name = frm.menu_name.value;
	var page_file = frm.page_file.value;
	var page_title = frm.page_title.value;
	var page_script = frm.page_script.value;
	var page_style = frm.page_style.value;

	var msg = "";
	if (dir_name == "" || menu_name == "") {
		msg = "メニューID、メニュー名がブランクです。";
	} else {
		//未設定ならば同じ名前を設定
		if(page_file == "") {
			page_file = dir_name;
			frm.page_file.value = dir_name;
		} else {
			if(page_file.indexOf(dir_name) != 0) {
				msg = "ページファイルは「メニューID-XXX」の形式にして下さい。";
			}
		}
		if(page_title == "") {
			page_title = menu_name;
			frm.page_title.value = menu_name;
		}
	}
	if( msg != "") {
		document.getElementById("page_message").textContent = msg;
		started = false;
		return false;
	}

	//サーバに接続
	var req = "mode=kanri";
	req += "&action=page_check";
	req += "&navid="+encodeURIComponent(navid);
	req += "&page_name="+encodeURIComponent(page_file);
	var resp = xhrSendJson(req);

	var comment = "";
	if((typeof resp) === 'object') {
		var results = resp.results;
		comment = resp.comment;
		if(results == "ok") {
			frm.token.value = resp.token;
			var act = resp.order;
			var act_msg = "";

			if(document.getElementById("delete_page").checked != false) {
				if (act=='update') {
					act_msg = "削除してよろしいですか？";
					act = 'delete';
					comment = "メニューから削除して、ページを無効にします。";
				} else {
					act = '';
					comment = "ページは登録されていません。";
				}
			} else if (act=='update') {
				act_msg = "タイトルを更新しますか？";
			} else if (act=='new_dir') {
				act_msg = "新しいメニューIDを作成しますか？";
				frm.page_file.value = dir_name;
			} else if (act=='new_file') {
				act_msg = "ファイルを追加しますか？";
			}
			if (act!='') {
				frm.order.value = act;
				document.getElementById("page_action_message").textContent = act_msg;
				lock_buttons();
				document.getElementById("page_edit_check").style.cssText = "display: none;";
				document.getElementById("page_edit_confirm").style.cssText = "display: block;";
			}

		}
	}
	document.getElementById("page_message").textContent = comment;

	started = false;
	return false;
}
//ページ登録キャンセル////////////////////////////
function page_action_cancel(){
	if (started == true) {
		return false;
	}
	started = true;
	document.getElementById("page_message").textContent = "";
	page_action_close();
}
//////////////////////////////////////////////////
function page_action_close(){
	unlock_buttons();
	document.getElementById("page_edit_confirm").style.cssText = "display: none;";
	document.getElementById("delete_page").checked = false;
	document.getElementById("page_edit_check").style.cssText = "display: block;";
	document.getElementById("exec_page").checked = false
	started = false;
	return false;
}
//ページ登録実行//////////////////////////////////
function page_action_confirm(){
	if (started == true) {
		return false;
	}
	started = true;
	var act = "";
	document.getElementById("page_message").textContent = "";
	if(document.getElementById("exec_page").checked == false){
		var comment = document.getElementById("page_action_message").textContent;
		comment = "「" + comment + "」が選択されていません。";
		document.getElementById("page_message").textContent = comment;
		started = false;
		return false;
	}
	//設定データの取得
	var frm = document.getElementById("page_edit");
	var navid = frm.navid.value;
	var token = frm.token.value;
	var order = frm.order.value;
	var dir_name = frm.dir_name.value;
	var menu_name = frm.menu_name.value;
	var page_file = frm.page_file.value;
	var page_title = frm.page_title.value;
	var page_script = frm.page_script.value;
	var page_style = frm.page_style.value;
	var page_menu = new Array();
	if(order == 'new_file' || order == 'update') {
		if(page.length > 0){
			var file = page[0].name;
			var dir = file.split('-').shift();
			if(dir_name == dir) {
				//一致していれば、現在のメニューを渡す
				page_menu = page;
			}
		}
	}

	//サーバに接続
	var req = "mode=kanri";
	req += "&action=page_edit";
	req += "&navid="+encodeURIComponent(navid);
	req += "&token="+encodeURIComponent(token);
	req += "&order="+encodeURIComponent(order);
	req += "&dir_name="+encodeURIComponent(dir_name);
	req += "&menu_name="+encodeURIComponent(menu_name);
	req += "&page_file="+encodeURIComponent(page_file);
	req += "&page_title="+encodeURIComponent(page_title);
	req += "&page_script="+encodeURIComponent(page_script);
	req += "&page_style="+encodeURIComponent(page_style);
	var jstr = JSON.stringify(page_menu);
	req += "&page_menu="+encodeURIComponent(jstr);
	var resp = xhrSendJson(req);
	console.log(resp);

	if((typeof resp) === 'object') {
		var results = resp.results;
		document.getElementById("page_message").textContent = resp.comment;
		frm.token.value = resp.token;
		if(results == "ok") {
			//表示の更新
			page = resp.page_menu;
			page_select_options('');
			mmenu = resp.mmenu;
			page_folder_options(dir_name);
			//フォームのクリア
			var items = document.getElementById("page_edit_area").getElementsByTagName("input");
			for(var i=0; i<items.length; i++){
				items[i].value = "";
			}
		}
	}
	page_action_close();
}
//////////////////////////////////////////////////
//分類登録////////////////////////////////////////
var group_msg_cnt = 0;
var group_msg = ["分類登録の手順と機能を説明します。",
"分類IDは、半角英数字を使用して下さい。",
"分類名は、実際に表示する分類名称になります。",
"「メニュー登録」の分類IDを選択すると、登録データを表示します。",
"新しい分類IDは追加、分類IDが同じ場合は上書き、または削除できます。",
];
//ページ登録のガイドメッセージ表示////////////////
function group_guide(){
	if(group_msg_cnt >= group_msg.length) group_msg_cnt = 0;
	document.getElementById("group_message").textContent = group_msg[group_msg_cnt];
	group_msg_cnt = group_msg_cnt + 1;
	return false;
}
//分類登録開始////////////////////////////////////
function group_action_check(){
	if (started == true) {
		return false;
	}
	started = true;
	document.getElementById("group_message").textContent = "";

	var frm = document.getElementById("group_edit");
	var navid = frm.navid.value;
	frm.token.value = "";
	frm.order.value = "";

	var group_id = frm.group_id.value;
	var group_name = frm.group_name.value;

	var msg = "";
	if (group_id == "" || group_name == "") {
		msg = "分類ID、分類名がブランクです。";
		document.getElementById("group_message").textContent = msg;
		started = false;
		return false;
	}

	//サーバに接続
	var req = "mode=kanri";
	req += "&action=group_check";
	req += "&navid="+encodeURIComponent(navid);
	var resp = xhrSendJson(req);

	if((typeof resp) === 'object') {
		var results = resp.results;
		document.getElementById("group_message").textContent = resp.comment;
		if(results == "ok") {
			frm.token.value = resp.token;
			var act = "";
			var act_msg = "";
			if(document.getElementById("delete_group").checked == false){
				act_msg = "登録してよろしいですか？";
				act = 'update';
			} else {
				act_msg = "削除してよろしいですか？";
				act = 'delete';
			}
			frm.order.value = act;
			document.getElementById("group_action_message").textContent = act_msg;
			lock_buttons();
			document.getElementById("group_edit_check").style.cssText = "display: none;";
			document.getElementById("group_edit_confirm").style.cssText = "display: block;";
		}
	}

	started = false;
	return false;
}
//分類登録キャンセル//////////////////////////////
function group_action_cancel(){
	if (started == true) {
		return false;
	}
	started = true;
	document.getElementById("group_message").textContent = "";
	group_action_close();
}
//////////////////////////////////////////////////
function group_action_close(){
	unlock_buttons();
	document.getElementById("group_edit_confirm").style.cssText = "display: none;";
	document.getElementById("delete_group").checked = false;
	document.getElementById("group_edit_check").style.cssText = "display: block;";
	document.getElementById("exec_group").checked = false
	started = false;
	return false;
}
//分類登録実行////////////////////////////////////
function group_action_confirm(){
	if (started == true) {
		return false;
	}
	started = true;
	var act = "";
	document.getElementById("group_message").textContent = "";
	if(document.getElementById("exec_group").checked == false){
		var comment = document.getElementById("group_action_message").textContent;
		comment = "「" + comment + "」が選択されていません。";
		document.getElementById("group_message").textContent = comment;
		started = false;
		return false;
	}
	//設定データの取得
	var frm = document.getElementById("group_edit");
	var navid = frm.navid.value;
	var token = frm.token.value;
	var order = frm.order.value;
	var group_id = frm.group_id.value;
	var group_name = frm.group_name.value;

	//サーバに接続
	var req = "mode=kanri";
	req += "&action=group_edit";
	req += "&navid="+encodeURIComponent(navid);
	req += "&token="+encodeURIComponent(token);
	req += "&order="+encodeURIComponent(order);
	req += "&group_id="+encodeURIComponent(group_id);
	req += "&group_name="+encodeURIComponent(group_name);
	var jstr = JSON.stringify(groups);
	req += "&groups="+encodeURIComponent(jstr);
	var resp = xhrSendJson(req);
	//console.log(resp);

	if((typeof resp) === 'object') {
		var results = resp.results;
		document.getElementById("group_message").textContent = resp.comment;
		frm.token.value = resp.token;
		if(results == "ok") {
			//表示の更新
			groups = resp.groups;
			if(order=='update') {
				group_select_options(group_id);
				group_conte_options(group_id);
			} else {
				group_select_options("");
				document.getElementById("group_conte").innerHTML = "";
			}
		}
	}
	group_action_close();
}
//////////////////////////////////////////////////
//メニュー登録////////////////////////////////////
var list_msg_cnt = 0;
var list_msg = ["メニュー登録の手順と機能を説明します。",
"「分類ID」を選択すると、割付けられたメニューを表示します。",
"「分類に割付け」のメニューを選択して「×」により割付けを解除します。",
"「メニューID」にあるメニューを選択して「←」により、割付けを追加します。",
"「↑」「↓」により、メニューの並びを変更することができます。",
"編集した割付けは「メニュー登録」によりサーバに登録します。",
"登録せず画面を再表示すると、編集内容は失われてしまうので注意して下さい。"];
//ページ登録のガイドメッセージ表示////////////////
function list_guide(){
	if(list_msg_cnt >= list_msg.length) list_msg_cnt = 0;
	document.getElementById("list_message").textContent = list_msg[list_msg_cnt];
	list_msg_cnt = list_msg_cnt + 1;
	return false;
}
//メニュー登録開始////////////////////////////////
function list_action_check(){
	if (started == true) {
		return false;
	}
	started = true;

	var frm = document.getElementById("list_edit");
	var navid = frm.navid.value;
	frm.token.value = "";

	//サーバに接続
	var req = "mode=kanri";
	req += "&action=list_check";
	req += "&navid="+encodeURIComponent(navid);
	var resp = xhrSendJson(req);

	if((typeof resp) === 'object') {
		var results = resp.results;
		document.getElementById("list_message").textContent = resp.comment;
		if(results == "ok") {
			frm.token.value = resp.token;
			lock_buttons();
			document.getElementById("list_edit_check").style.cssText = "display: none;";
			document.getElementById("list_edit_confirm").style.cssText = "display: block;";
		}
	}
	started = false;
	return false;
}
//メニュー登録キャンセル//////////////////////////
function list_action_cancel(){
	if (started == true) {
		return false;
	}
	started = true;
	document.getElementById("list_message").textContent = "";
	list_action_close();
}
//////////////////////////////////////////////////
function list_action_close(){
	unlock_buttons();
	document.getElementById("list_edit_confirm").style.cssText = "display: none;";
	document.getElementById("list_edit_check").style.cssText = "display: block;";
	started = false;
	return false;
}
//メニュー登録実行////////////////////////////////
function list_action_confirm(){
	if (started == true) {
		return false;
	}
	started = true;
	var act = "";
	document.getElementById("list_message").textContent = "";

	//設定データの取得
	var frm = document.getElementById("list_edit");
	var navid = frm.navid.value;
	var token = frm.token.value;

	//サーバに接続
	var req = "mode=kanri";
	req += "&action=list_edit";
	req += "&navid="+encodeURIComponent(navid);
	req += "&token="+encodeURIComponent(token);
	//メニュー
	var mstr = JSON.stringify(mmenu);
	req += "&mmenu="+encodeURIComponent(mstr);
	//分類
	var gstr = JSON.stringify(groups);
	req += "&groups="+encodeURIComponent(gstr);
	//ページ
	var pagenm = document.getElementById("page_select").value;
	if(pagenm != ""){
		req += "&pagenm="+encodeURIComponent(pagenm);
		var pstr = JSON.stringify(page);
		req += "&pmenu="+encodeURIComponent(pstr);
	}
	var resp = xhrSendJson(req);

	if((typeof resp) === 'object') {
		var results = resp.results;
		document.getElementById("list_message").textContent = resp.comment;
		frm.token.value = resp.token;
		if(results == "ok") {
			//setTimeout("window.location.reload(true)", 1000);
			var grp = document.getElementById("group_select").value;
			var dir = document.getElementById("page_folder").value;
			mmenu = resp.mmenu;
			page_folder_options(dir);
			group_conte_options(grp);
		}
	}
	list_action_close();
}
//////////////////////////////////////////////////
//画面のロック
function lock_buttons(){
	var items = document.getElementById("menu_list_area").getElementsByTagName("select");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
	var items = document.getElementById("menu_list_area").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
	var items = document.getElementById("list_edit_check").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
	var items = document.getElementById("group_edit_check").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
	var items = document.getElementById("group_edit_area").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
	var items = document.getElementById("page_edit_check").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
	var items = document.getElementById("page_edit_area").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = true;
	}
}
//画面のアンロック
function unlock_buttons(){
	var items = document.getElementById("menu_list_area").getElementsByTagName("select");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
	var items = document.getElementById("menu_list_area").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
	var items = document.getElementById("list_edit_check").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
	var items = document.getElementById("group_edit_check").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
	var items = document.getElementById("group_edit_area").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
	var items = document.getElementById("page_edit_check").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
	var items = document.getElementById("page_edit_area").getElementsByTagName("input");
	for(var i=0; i<items.length; i++){
		items[i].disabled = false;
	}
}
//////////////////////////////////////////////////
//共通ファンクション
function htmlspecialchars(str){
  return (str + '').replace(/&/g,'&amp;')
                   .replace(/"/g,'&quot;')
                   .replace(/'/g,'&#039;')
                   .replace(/</g,'&lt;')
                   .replace(/>/g,'&gt;'); 
}
/* AJAX 基本ルーチン */
function getXhrObj() {
	var xhrObj;
	if (window.XMLHttpRequest) {
		try {
			xhrObj = new XMLHttpRequest();
		} catch (e) {
			xhrObj = false;
		}
	}else if (window.ActiveXObject){
		try {
			xhrObj = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e){
			try {
				xhrObj = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (ex){
				xhrObj = false;
			}
		}
	}
	return xhrObj;
}
function xhrSendJson(req) {
	var xhrObj = getXhrObj();
	xhrObj.open("post", "prodajax.php", false);
	xhrObj.setRequestHeader('If-Modified-Since', '01 Jan 2000 00:00:00 GMT');
	xhrObj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhrObj.send(req);
	var str = xhrObj.responseText;
	try {
		var resp = JSON.parse(str);
	} catch(e) {
		return str;
	}
	return resp;
}
function xhrSendForm(frmam) {
	req = new FormData(document.forms.namedItem(frmam));
	var xhrObj = getXhrObj();
	xhrObj.open("post", "prodajax.php", false);
	xhrObj.send(req);
	var str = xhrObj.responseText;
	try {
		var resp = JSON.parse(str);
	} catch(e) {
		return str;
	}
	return resp;
}

