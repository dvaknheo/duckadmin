<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace DuckAdmin\SimpleBlog\Controller;

use DuckAdmin\SimpleBlog\Business\ArticleBusiness;
use DuckAdmin\SimpleBlog\Business\AdminBusiness;
use DuckPhp\Foundation\Controller\AdminControllerBase;

class adminController extends AdminControllerBase
{
    public function index()
    {
        Helper::Show([], 'admin/main');
    }
    public function articles()
    {
        $url_add = __url('admin/article_add');
        [$count,$list]= ArticleBusiness::_()->getArticleList(Helper::PageNo());
        $list = Helper::_()->recordsetUrl($list, [
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
        AdminBusiness::_()->addArticle(Helper::POST('title'), Helper::POST('content'));
        Helper::Show302('admin/articles');
    }
    public function article_edit()
    {
        if(!Helper::POST()){
            $article = AdminBusiness::_()->getArticle(Helper::GET('id',0));
            Helper::ThrowOn(!$article, "找不到文章");
            $article['title'] = __h($article['title']);
            $article['content'] = __h($article['content']);
            Helper::Show(get_defined_vars(), 'admin/article_update');
            return;
        }
        AdminBusiness::_()->updateArticle(Helper::POST('id'), Helper::POST('title'), Helper::POST('content'));
        Helper::Show302('admin/articles');
    }
    public function article_delete()
    {
        if(!Helper::POST()){
            return;
        }
        AdminBusiness::_()->deleteArticle(Helper::POST('id'));
        Helper::Show302('admin/articles');
    }
    public function comments()
    {
        [$total,$list] = AdminBusiness::_()->getCommentList(Helper::PageNo());
         
        $list = Helper::_()->recordsetUrl($list, [
            'url_edit' => 'admin/article_edit?id={id}',
            'url_delete' => 'admin/delete_comments?id={id}',
        ]);
        Helper::Show(get_defined_vars());
    }
    public function delete_comments()
    {
        AdminBusiness::_()->deleteComment(Helper::GET('id'));
        Helper::Show302('admin/comments');
    }
}
