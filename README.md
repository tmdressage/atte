# アプリケーション名
　Atte（勤怠管理システム）

## 作成した目的
　初級模擬案件提出用
 
## アプリケーションURL
　http://localhost/login
  
  ※本人確認メールはmailhogに送信されます。
  http://localhost:8025/

## 他のリポジトリ  
　無し

## 機能一覧
　・新規会員登録
  ・ログイン/ログアウト
　・メール認証(新規登録会員のみ)
  ・勤務開始/終了ボタン
  ・休憩開始/終了ボタン
  ・日付別勤怠情報取得(日付毎/ユーザ毎のページネーション)
  
## 使用技術（実行環境）
　OS：Linux（Ubuntu）
  環境：Docker Desktop v4.23.0
　言語：PHP 7.4.9
　フレームワーク：Laravel 8
  DB：mysql 8.0.26
  WEBサーバソフトウェア：nginx 1.21.1
  エディタ：VSCode 1.84.0


## テーブル設計
![スクリーンショット 2023-12-21 162115](https://github.com/tmdressage/atte/assets/144135026/3a076f6f-5ab9-4acf-8078-314ae0b2c0ed)

## ER図
![スクリーンショット 2023-12-21 163431](https://github.com/tmdressage/atte/assets/144135026/710bd184-1bc1-4af7-89ba-9d8980b8139a)

## 環境構築
　運営側でご用意いただいた環境（coachtech-material/laravel-docker-template.git）をベースにして、
  docker-compose.ymlと.envにmailhog用の設定を追加いたしました。
  ![スクリーンショット 2023-12-21 170105](https://github.com/tmdressage/atte/assets/144135026/30311bc8-4876-4942-bcc3-41c21613db47)
  ![スクリーンショット 2023-12-21 170407](https://github.com/tmdressage/atte/assets/144135026/fc998dab-4463-42de-8e51-0f828aa8d815)

## その他
お手数ですがご採点の程よろしくお願い申し上げます。以下補足事項です。
　・新規会員登録/ログイン/ログアウト機能は、FortifyとミドルウェアAuthを活用いたしました。
  ・勤務開始/終了ボタンと休憩開始/終了ボタンは、フロントエンド側(ボタンの活性/非活性)とバックエンド側(エラーメッセージ表示)の双方で制御する仕様にいたしました。
  ・メール認証時のメール送信先は、開発用のメールサーバmailhogを使用いたしました。
　・追加実装項目の「ユーザページ」「AWS」「環境切り分け」は未実装です。
  
　
