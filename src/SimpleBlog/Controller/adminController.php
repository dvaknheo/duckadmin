<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Controller;

use SimpleBlog\Business\AdminBusiness;
use SimpleBlog\Business\ArticleBusiness;
class adminController
{
    public function __construct()
    {
        $method = Helper::getRouteCallingMethod();
        if (in_array($method, ['login'])) {
            return;
        }
        try {
            Helper::G()->Admin();
            //如果没登录，到登录页面
        } catch(\Exception $ex) {
            Helper::ExitRouteTo('admin/login?r=admin/'.$method);
            return;
        }
        $data = [
            'url_articles' => 'admin/articles',
            'url_comments' => 'admin/comments',
            'url_users' => 'admin/users',
            'url_logs' => 'admin/logs',
            'url_logout' => 'admin/logout',
            'url_changepass' => 'admin/reset_password',
        ];
        array_walk($data, function (&$v) {
            $v = __url($v);
        });
        Helper::setViewHeadFoot('admin/inc_head', 'admin/inc_foot');
        Helper::assignViewData($data);
    }
    public function index()
    {
        Helper::Show([], 'admin/main');
    }

    public function articles()
    {
        $url_add = __url('admin/article_add');
        list($list, $total) = ArticleBusiness::G()->getArticleList(Helper::PageNo());
        $list = Helper::RecordsetUrl($list, [
            'url_edit' => 'admin/article_edit?id={id}',
            'url_delete' => 'admin/article_delete?id={id}',
        ]);
        Helper::Show(get_defined_vars(), 'admin/article_list');
    }
    public function article_add()
    {
        if(!Helper::POST()){
            Helper::Show(get_defined_vars());
            return;
        }
        AdminBusiness::G()->addArticle(Helper::POST('title'), Helper::POST('content'));
        Helper::ExitRouteTo('admin/articles');
    }
    public function article_edit()
    {
        if(!Helper::POST()){
            $article = AdminBusiness::G()->getArticle(Helper::GET('id',0));
            //Helper::ThrowOn(!$article, "找不到文章"); => TODO
            $article['title'] = __h($article['title']);
            $article['content'] = __h($article['content']);
            Helper::Show(get_defined_vars(), 'admin/article_update');
            return;
        }
        AdminBusiness::G()->updateArticle(Helper::POST('id'), Helper::POST('title'), Helper::POST('content'));
        Helper::ExitRouteTo('admin/articles');
    }
    public function do_article_delete()
    {
        AdminBusiness::G()->deleteArticle(Helper::POST('id'));
        Helper::ExitRouteTo('admin/articles');
    }
    public function users()
    {
        list($list, $total) = AdminBusiness::G()->getUserList(Helper::PageNo());
        $csrf_token = '';
        foreach ($list as  &$v) {
            $v['url_delete'] = __url("admin/delete_user?id={$v['id']}&_token={$csrf_token}");
        }
        Helper::Show(get_defined_vars());
    }
    public function delete_user()
    {
        AdminBusiness::G()->deleteUser(Helper::REQUEST('id'));
        Helper::ExitRouteTo('admin/users');
    }
    public function logs()
    {
        list($list, $total) = AdminBusiness::G()->getLogList(Helper::PageNo());
        
        $list = Helper::RecordsetUrl($list, [
            'url_edit' => 'admin/article_edit?id={id}',
            'url_delete' => 'admin/article_delete?id={id}',
        ]);
        
        Helper::Show(get_defined_vars());
    }
    public function comments()
    {
        list($list, $total) = AdminBusiness::G()->getCommentList(Helper::PageNo());
        Helper::Show(get_defined_vars());
    }
    public function delete_comments()
    {
        AdminBusiness::G()->deleteComment(Helper::POST('id'));
        Helper::ExitRouteTo('admin/comments');
    }
}
