[nekoViewer](http://neko.tedokon.com)
=================

nekoViewer は Twitter のタイムラインから猫の画像を探してきて表示するアプリです。
Twitter の Search API を使用して、#猫 OR #ねこ のハッシュタグがついた Tweet のうち、
画像へのリンクが乗っているものだけ表示します。
キャッシュ機構には PEAR::Cache_Lite を使用しています。

Quick start
===========

twitteroauth/config.php に COUNSUMER_KEY や ACCESS_TOKEN を設定します。
ファイル一式をサーバーに設置。

Link
====

neko.tedokon.com
