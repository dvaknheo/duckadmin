<?php
$href = !isset($children)?$href:'javascript:;';
$name = $name;
?>
<a href="<?=$href?>"><?=$name?></a>
<?php
    if(isset($children)){
?>
<dl class="layui-nav-child">
<?php
        foreach($children as $v){
?>
    <dd><?php __display('inc_menu',$v)?> </dd>
<?php

        }
?>
</dl>
<?php
    }
?>
