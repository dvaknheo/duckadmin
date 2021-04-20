<?php
declare (strict_types = 1);

namespace app\admin\controller\admin;

class Admin extends  \app\admin\controller\Base
{
    protected function initialize()
    {
        //
    }
    /**
     * 管理员
     */
    public function index()
    {
            if ($search = input('get.username')) {
               $this->where[] = ['username', 'like', "%" . $search . "%"];
            }
            $list = $this->model->order('id','desc')->where('id','>','1')->withoutField('password,token,delete_time')->where($this->where)->paginate(Request::get('limit'));
            $limit = Request::get('limit')
            $this->jsonApi('', 0, $list->items(), ['count' => $list->total(), 'limit' => $limit]);
        return $this->fetch();    
    }


    /**
     * 添加
     */
    public function add()
    {
        $post = C::Post();
        C::WrapException(AdminService::class);
        $result = $data ? AdminService:G()->addAdmin($post) : [];
        C::SetSuccessMsg('添加成功');
        C::Show([]);
    }

     /**
     * 编辑
     */
    public function edit($id)
    { 
        $model =  AdminService::G()->find($id);
        $data = Request::post();
        //
        C::SetSuccessMsg('更新成功');
        C::Show([
            'model' => $model
        ]);
    }

    /**
     * 禁用，启用
     */
    public function status($id)
    {
        $status = Request::post('status'),
        


        $this->jsonApi('更新成功');
    }

    /**
     * 删除
     */
    public function remove($id)
    {
        $model = $this->model->find($id);
        if ($model->isEmpty()) $this->jsonApi('数据不存在',201);
        try{
            $model->delete();
            Db::name('admin_admin_role')->where('admin_id', $id)->delete();
            Db::name('admin_admin_permission')->where('admin_id', $id)->delete();
            $this->rm();
        }catch (\Exception $e){
            $this->jsonApi('删除失败',201,$e->getMessage());
        }
        $this->jsonApi('删除成功');
    }

    /**
     * 批量删除
     */
    public function batchRemove()
    {
        $ids = Request::post('ids');
        if (!is_array($ids)) $this->jsonApi('参数错误',201);
        try{
            $this->model->destroy($ids);
            Db::name('admin_admin_role')->whereIn('admin_id', $ids)->delete();
            Db::name('admin_admin_permission')->whereIn('admin_id', $ids)->delete();
            $this->rm();
        }catch (\Exception $e){
            $this->jsonApi('删除失败',201,$e->getMessage());
        }
        $this->jsonApi('删除成功');
    }

    /**
     * 用户分配角色
     */
    public function role($id)
    {
        $admin = $this->model->with('roles')->where('id',$id)->find();
        $roles = (new \app\admin\model\AdminRole)->select();
        foreach ($roles as $k=>$role){
            if (isset($admin->roles) && !$admin->roles->isEmpty()){
                foreach ($admin->roles as $v){
                    if ($role['id']==$v['id']){
                        $roles[$k]['own'] = true;
                    }
                }
            }
        }
        if (Request::isAjax()){
            $postRoles = Request::post('roles');
            if($postRoles){
                Db::startTrans();
                try{
                    //清除原先的角色
                    Db::name('admin_admin_role')->where('admin_id',$id)->delete();
                    //添加新的角色
                    foreach ($postRoles as $v){
                        Db::name('admin_admin_role')->insert([
                            'admin_id' => $admin['id'],
                            'role_id' => $v,
                        ]);
                    }
                    Db::commit();
                    $this->rm();
                }catch (\Exception $e){
                    Db::rollback();
                    $this->jsonApi('更新失败',201, $e->getMessage());
                }
            }else{
                Db::name('admin_admin_role')->where('admin_id',$id)->delete();
            }
            $this->jsonApi('更新成功');
        }
        return $this->fetch('',[
            'admin' => $admin,
            'roles' => $roles,
        ]);
    }
    protected  function jsonApi($msg = '', $code = 200, $data = [], $extend = [], $header = [])
    {
        $return = [
            'msg'  => $msg,
            'code' => $code,
        ];
        if (!empty($data)) {
            $return['data'] = $data;
        }
        if (!empty($extend)) {
            foreach ($extend as $k => $v) {
                $return[$k] = $v;
            }
        }
        $response = Response::create($return, 'json')->header($header);
        throw new HttpResponseException($response);
    }
    
    /**
     * 用户分配直接权限
     */
    public function permission($id)
    {
        $admin = $this->model->with('directPermissions')->find($id);
        $permissions = (new \app\admin\model\AdminPermission)->order('sort','asc')->select();
        foreach ($permissions as $permission){
            foreach ($admin->direct_permissions as $v){
                if ($permission->id == $v['id']){
                    $permission->own = true;
                }
            }
        }
        $permissions = get_tree($permissions->toArray());
        if ($this->request->isAjax()){
            $postPermissions = Request::post('permissions');
            if($postPermissions){
                Db::startTrans();
                try{
                    //清除原有的直接权限
                    Db::name('admin_admin_permission')->where('admin_id',$id)->delete();
                    //填充新的直接权限
                    foreach ($postPermissions as $v){
                        Db::name('admin_admin_permission')->insert([
                            'admin_id' => $id,
                            'permission_id' => $v,
                        ]);
                    }
                    Db::commit();
                }catch (DbException $exception){
                    Db::rollback();
                    $this->jsonApi('更新失败',201, $e->getMessage());
                }
            }else{
                Db::name('admin_admin_permission')->where('admin_id',$id)->delete();
            }
            $this->jsonApi('更新成功');
        }
        return $this->fetch('',[
            'admin' => $admin,
            'permissions' => $permissions,
        ]);
    }

    /**
     * 回收站
     */
    public function recycle()
    {
        if (Request::isAjax()){
            if (Request::isPost()){
                $ids = Request::param('ids');
                if (!is_array($ids)) return ['msg'=>'参数错误','code'=>'201'];
                try{
                    if(Request::param('type')){
                        $data = $this->model->onlyTrashed()->whereIn('id', $ids)->select();
                        foreach($data as $k){
                            $k->restore();
                        }
                    }else{
                        $this->model->destroy($ids,true);
                    }
                }catch (\Exception $e){
                    $this->jsonApi('操作失败',201,$e->getMessage());
                }
                $this->jsonApi('操作成功');
            }
            //按用户名
            if ($search = input('get.username')) {
                $this->where[] = ['username', 'like', "%" . $search . "%"];
            }
            $list = $this->model->onlyTrashed()->order('id','desc')->withoutField('password,token')->where($this->where)->paginate(Request::get('limit'));
            $this->jsonApi('', 0, $list->items(), ['count' => $list->total(), 'limit' => Request::get('limit')]);
        }
        return $this->fetch();
    }

    public function log()
    {
        if (Request::isAjax()) {    
            if ($search = input('get.uid')) {
                $this->where[] = ['uid', '=',$search];
            }
            $list = (new \app\admin\model\AdminAdminLog)->with('log')->order('id','desc')->where($this->where)->paginate(Request::get('limit'));
            $this->jsonApi('', 0, $list->items(), ['count' => $list->total(), 'limit' => Request::get('limit')]);
        }
        return $this->fetch();
    }

    /**
     * 清空日志
     */
    public function removeLog()
    {
        Db::name('admin_admin_log')->delete(true);
        $this->jsonApi('删除成功');
    }
}
