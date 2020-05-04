<?php

# 送信先アドレス
$mailto = "[Sorry, it is secret.]";
# 送信後画面からの戻り先
$toppage = "./index.html";

#-----------------------------------------------------------
#　入力情報の受け取りと加工
#-----------------------------------------------------------
$name = $_POST["name"];
$email = $_POST["email"];
$tel = $_POST["tel"];
$comment = $_POST["comment"];
$privacy = $_POST["privacy"];

# 無効化
$name = htmlentities($name, ENT_QUOTES, "UTF-8");
$email = htmlentities($email, ENT_QUOTES, "UTF-8");
$tel = htmlentities($tel, ENT_QUOTES, "UTF-8");
$comment = htmlentities($comment, ENT_QUOTES, "UTF-8");

# 改行処理
$name = str_replace("\r\n", "", $name);
$email = str_replace("\r\n", "", $email);
$tel = str_replace("\r\n", "", $tel);
$comment = str_replace("\r\n", "<br>", $comment);
$comment = str_replace("\r", "<br>", $comment);
$comment = str_replace("\n", "<br>", $comment);

# 入力チェック
if($name == ""){error("名前が未入力です。");}
if($email == ""){error("メールアドレスが未入力です。");}
if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){error("メールアドレスが正しくありません。");}
if($tel == ""){error("電話番号が未入力です。");}
if(!preg_match("/^0+\d{1,4}(-|)\d{1,4}(-|)\d{4}$/", $tel)){error("電話番号が正しくありません。");}
//if(!preg_match("/(^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$)|(^[0-9]{2,4}[0-9]{2,4}[0-9]{3,4}$)/", $email)){error("電話番号が正しくありません。");}
if($comment == ""){error("お問い合わせ内容が未入力です。");}
if($privacy == ""){error("プライバシーポリシーに同意をお願い致します。");}

# 分岐チェック
if($_POST["mode"] == "post"){conf_form();}
else if($_POST["mode"] == "send"){send_form();}

#-----------------------------------------------------------
#　確認画面
#-----------------------------------------------------------
function conf_form(){
  global $name;
  global $email;
  global $tel;
  global $comment;
  global $privacy;
  
  # テンプレート読み込み
  $conf = fopen("tmpl/conf.tmpl", "r") or die;
  $size = filesize("tmpl/conf.tmpl");
  $data = fread($conf, $size);
  fclose($conf);
  
  # 文字置き換え
  $data = str_replace("!name!", $name, $data);
  $data = str_replace("!email!", $email, $data);
  $data = str_replace("!tel!", $tel, $data);
  $data = str_replace("!comment!", $comment, $data);
  $data = str_replace("!privacy!", $privacy, $data);
  
  # 表示
  echo $data;
  exit;
}

#-----------------------------------------------------------
#　エラー画面
#-----------------------------------------------------------
function error($msg){
  $error = fopen("tmpl/error.tmpl", "r");
  $size = filesize("tmpl/error.tmpl");
  $data = fread($error, $size);
  fclose($error);
  
  #　文字置き換え
  $data = str_replace("!message!", $msg, $data);
  
  # 表示
  echo $data;
  exit;
}

#-----------------------------------------------------------
#　CSV書込み
#-----------------------------------------------------------
function send_form(){
  global $name;
  global $email;
  global $tel;
  global $comment;
  global $privacy;
  global $date;
  global $ip;
  
  # 時間とIPアドレスの取得
  date_default_timezone_set('Asia/Tokyo');
  $date = date("Y/m/d H:i:s");
  $ip = getenv("REMOTE_ADDR");
  
  $user_input = array($name, $email, $tel, $comment, $privacy, $date, $ip);
  mb_convert_variables("SJIS", "UTF-8", $user_input);
  $fh = fopen("[Sorry, it is secret.]", "a");
  flock($fh, LOCK_EX);
  fputcsv($fh, $user_input);
  flock($fh, LOCK_UN);
  fclose($fh);
  
  #メール送信
  send_mail();
  
  # テンプレート読み込み
  $conf = fopen("tmpl/send.tmpl", "r") or die;
  $size = filesize("tmpl/send.tmpl");
  $data = fread($conf, $size);
  fclose($conf);
  
  # 文字置き換え
  global $toppage;
  $data = str_replace("!top!", $toppage, $data);
  
  # 表示
  echo $data;
  exit;
}

#-----------------------------------------------------------
#　メール送信
#-----------------------------------------------------------
function send_mail(){
  
  global $name;
  global $email;
  global $tel;
  global $comment;
  global $privacy;
  global $date;
  
  $comment = str_replace("&lt;br&gt;", "\n", $comment);//または<br>の代わりに\nの改行コードを指定する。
  
  # 本文(お客様用メール)
  $body_customer = <<< _FORM_
$name 様

株式会社このみち お問い合わせ窓口です。
この度はお問い合わせを頂きまして、誠にありがとうございます。

下記の内容で、お問い合わせを承りました。

==================================================================

■お問い合わせ日時：$date

■お名前：$name

■メールアドレス：$email

■電話番号：$tel

■お問い合わせ内容：
$comment

■プライバシーポリシー：$privacy

==================================================================

内容を確認させて頂き、担当よりご連絡させて頂きます。

お問い合わせの内容によっては、ご回答にお時間がかかる場合や、ご回答できない場合がございます。
あらかじめご了承ください。


_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/
株式会社このみち / CONOMITI Inc.
お問い合わせ窓口

E-mail: [Sorry, it is secret.]
Address: [Sorry, it is secret.]
TEL: [Sorry, it is secret.]
URL: http://www.conomiti.co.jp
_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/
_FORM_;
  
  # 本文(このみち用メール)
  $body_conomiti = <<< _FORM_
  お問い合わせフォームより、次のとおり連絡がありました。

==================================================================

■お問い合わせ日時：$date

■お名前：$name

■メールアドレス：$email

■電話番号：$tel

■お問い合わせ内容：
$comment

■プライバシーポリシー：$privacy

==================================================================
_FORM_;
  
  #返信送信共通
  global $mailto;
  mb_language("japanese");
  mb_internal_encoding("UTF-8");
  
  #返信（お客様用）
  $name_forreply = "株式会社このみち お問い合わせ窓口";
  $name_forreply = mb_encode_mimeheader($name_forreply);
  $mail_forreply = "[Sorry, it is secret.]";
  $mailfrom_forreply = "From:".$name_forreply."<".$mail_forreply.">";
  $subject_forreply = "【このみち】お問い合わせの送信内容";
  mb_send_mail($email, $subject_forreply, $body_customer, $mailfrom_forreply);
  
  #送信（このみち用）
  $name_sendonly = "送信専用アドレス";
  $name_sendonly = mb_encode_mimeheader($name_sendonly);
  $mail_sendonly = "[Sorry, it is secret.]";
  $mailfrom_sendonly = "From:".$name_sendonly."<".$mail_sendonly.">";
  $subject_sendonly = "【お問い合わせフォーム】お問い合わせがありました";
  mb_send_mail($mailto, $subject_sendonly, $body_conomiti, $mailfrom_sendonly);
}
?>