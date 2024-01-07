<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Controller;

use SimpleBlog\Business\ArticleBusiness;
use SimpleBlog\Business\UserBusiness;

class MainController
{
    public function __construct()
    {
    }
    public function action_index()
    {
        $url_reg = Helper::User()->urlForRegist();
        
        $url_login = Helper::User()->urlForLogin();
        $url_logout = Helper::User()->urlForLogout();
        $url_admin = __url('admin/index');
        $user = Helper::_()->getUserData();
        list($articles, $total) = ArticleBusiness::_()->getRecentArticle(Helper::PageNo());
        
        $articles = Helper::_()->recordsetH($articles, ['title']);
        $articles = Helper::_()->recordsetUrl($articles, ['url' => 'article/{id}']);
        
        Helper::Show(get_defined_vars(), 'main');
    }
    public function action_article()
    {
        $id = Helper::GET('id',1);
        $id = (int)$id;
        
        $article = ArticleBusiness::_()->getArticleFullInfo($id, Helper::PageNo(), Helper::PageWindow());
        if (!$article) {
            Helper::Show404();
            return;
        }
        $article['comments'] = Helper::_()->recordsetH($article['comments'], ['content','username']);
        $html_pager = Helper::PageHtml($article['comments_total']);
        $url_add_comment = __url('addcomment');
        $user = Helper::_()->getUserData();
        $url_login_to_commment = Helper::User()->UrlForLogin(__url("article/$id"));
        Helper::Show(get_defined_vars(), 'article');
    }
    public function action_addcomment()
    {
        if(!Helper::POST()){return;}
        $uid = Helper::UserId();
        UserBusiness::_()->addComment($uid, Helper::POST('article_id'), Helper::POST('content'));
        Helper::Show302('article/'.Helper::POST('article_id'));
    }
    public function action_delcomment()
    {
        if(!Helper::POST()){return;}
        
        $uid = Helper::UserId();
        UserBusiness::_()->deleteCommentByUser($uid, Helper::POST('id'));
        Helper::Show302('');
    }

}
