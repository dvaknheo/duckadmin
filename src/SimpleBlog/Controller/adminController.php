<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace SimpleBlog\Controller;

use SimpleBlog\Business\ArticleBusiness;
use SimpleBlog\Business\AdminBusiness;
class adminController
{
    public function __construct()
    {
        $controller = Helper::getRouteCallingClass();
        $action = Helper::getRouteCallingMethod();
        $path = Helper::PathInfo();
        Helper::Admin()->checkAccess($controller,$action, __url($path));
        $data = [
            'url_articles' => 'admin/articles',
            'url_comments' => 'admin/comments',
            'url_logs' => 'admin/logs',
        ];
        
        array_walk($data, function (&$v) {
            $v = __url($v);
        });
        $data['url_logout'] =Helper::Admin()->urlForLogout();
        
        Helper::setViewHeadFoot('admin/inc_head', 'admin/inc_foot');
        Helper::assignViewData($data);
    }
    public function action_index()
    {
        Helper::Show([], 'admin/main');
    }

    public function action_articles()
    {
        $url_add = __url('admin/article_add');
        list($list, $total) = ArticleBusiness::_()->getArticleList(Helper::PageNo());
        $list = Helper::_()->recordsetUrl($list, [
            'url_edit' => 'admin/article_edit?id={id}',
            'url_delete' => 'admin/article_delete?id={id}',
        ]);
        Helper::Show(get_defined_vars(), 'admin/article_list');
    }
    public function action_article_add()
    {
        if(!Helper::POST()){
            Helper::Show(get_defined_vars());
            return;
        }
        AdminBusiness::_()->addArticle(Helper::POST('title'), Helper::POST('content'));
        Helper::Show302('admin/articles');
    }
    public function action_article_edit()
    {
        if(!Helper::POST()){
            $article = AdminBusiness::_()->getArticle(Helper::GET('id',0));
            Helper::ControllerThrowOn(!$article, "找不到文章");
            $article['title'] = __h($article['title']);
            $article['content'] = __h($article['content']);
            Helper::Show(get_defined_vars(), 'admin/article_update');
            return;
        }
        AdminBusiness::_()->updateArticle(Helper::POST('id'), Helper::POST('title'), Helper::POST('content'));
        Helper::Show302('admin/articles');
    }
    public function do_action_article_delete()
    {
        AdminBusiness::_()->deleteArticle(Helper::POST('id'));
        Helper::Show302('admin/articles');
    }

    public function action_logs()
    {
        list($list, $total) = AdminBusiness::_()->getLogList(Helper::PageNo());
        
        $list = Helper::_()->recordsetUrl($list, [
            'url_edit' => 'admin/article_edit?id={id}',
            'url_delete' => 'admin/article_delete?id={id}',
        ]);
        
        Helper::Show(get_defined_vars());
    }
    public function action_comments()
    {
        list($list, $total) = AdminBusiness::_()->getCommentList(Helper::PageNo());
         
        $list = Helper::_()->recordsetUrl($list, [
            'url_edit' => 'admin/article_edit?id={id}',
            'url_delete' => 'admin/delete_comments?id={id}',
        ]);
        Helper::Show(get_defined_vars());
    }
    public function action_delete_comments()
    {
        AdminBusiness::_()->deleteComment(Helper::POST('id'));
        Helper::Show302('admin/comments');
    }
}
