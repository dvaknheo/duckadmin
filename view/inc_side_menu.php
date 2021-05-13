<?php
foreach($nodes as $v) {
    $href= !isset($v['children'])?$v['href']:'javascript:;';
    $name = $v['name'];
?>
<li class="layui-nav-item">
    <a href="<?=$href?>"><?=$v['name']?></a>
<?php
    if(isset($v['children'])){
?>
    <dl class="layui-nav-child">
<?php
        foreach($v['children'] as $v){
?>
        <dd><?php __display('inc_menu',$v)?> </dd>
<?php
        }
?>
    </dl>
<?php
    }
}