<h2>DemoUsers 主页</h2>
<p>欢迎,<strong><?=__h($__logined_name ?? '')?></strong>!(用户 id: <?=__h((string)($__logined_id ?? ''))?>)</p>
<p><a href="<?=__h($__logined_url_logout ?? '')?>">登出</a></p>
