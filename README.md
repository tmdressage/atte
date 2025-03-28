<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# アプリケーション名
・Atte（勤怠管理システム）

## 機能一覧
・新規会員登録<br>
  ・ログイン/ログアウト<br>
・メール認証(新規登録会員のみ)<br>
  ・勤務開始/終了ボタン<br>
  ・休憩開始/終了ボタン<br>
  ・日付別勤怠情報取得(日付毎/ユーザ毎のページネーション)<br>
  
## 使用技術（実行環境）
・OS：Linux（Ubuntu）<br>
  ・環境：Docker Desktop v4.23.0<br>
・言語：PHP 7.4.9<br>
・フレームワーク：Laravel 8<br>
  ・DB：mysql 8.0.26<br>
  ・WEBサーバソフトウェア：nginx 1.21.1<br>
  ・エディタ：VSCode 1.84.0<br>

## テーブル設計
![スクリーンショット 2023-12-21 162115](https://github.com/tmdressage/atte/assets/144135026/3a076f6f-5ab9-4acf-8078-314ae0b2c0ed)

## ER図
![スクリーンショット 2023-12-21 163431](https://github.com/tmdressage/atte/assets/144135026/710bd184-1bc1-4af7-89ba-9d8980b8139a)

## その他
・新規会員登録/ログイン/ログアウト機能は、FortifyとミドルウェアAuthを活用いたしました。<br>
  ・勤務開始/終了ボタンと休憩開始/終了ボタンは、フロントエンド側(ボタンの活性/非活性)とバックエンド側(エラーメッセージ表示)の<br>
  　双方で制御する仕様にいたしました。<br>
  ・メール認証時のメール送信先は、開発用のメールサーバmailhogを使用いたしました。<br>
　
