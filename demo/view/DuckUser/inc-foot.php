
<script src="/layui/layui.js"></script>
<script>
var error=<?=json_encode($error)?>;
</script>
<script>
layui.use('layer', function(){
  var layer = layui.layer;
  if(error){
  layer.msg(error,{icon:2});
  }
});     

</script>
  </div>
<div class="layui-footer" style ="background-color:#FAFAFA;padding:1em;text-align:center;">
感谢 <a href="https://github.com/sentsin/layui/"> LayUI </a> 前端支持，为我这个不懂得好看的能勉强做出来
感谢 pearadmin  让我可以 copy  idea.
</div>
    </body>
</html>
