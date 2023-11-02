<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Controller;

use SimpleBlog\Business\ArticleBusiness;
use SimpleBlog\Business\UserBusiness;
use SimpleBlog\ControllerEx\SessionManager;

class MainController
{
    public function index()
    {
        $url_reg = __url('register');
        $url_login = __url('login');
        $url_logout = __url('logout');
        $url_admin = __url('admin/index');

        $user = SessionManager::G()->getCurrentUser();
        list($articles, $total) = ArticleBusiness::G()->getRecentArticle(Helper::PageNo());
        
        $articles = Helper::RecordsetH($articles, ['title']);
        $articles = Helper::RecordsetUrl($articles, ['url' => 'article/{id}']);
        
        Helper::Show(get_defined_vars(), 'main');
    }
    public function article()
    {
        $id = Helper::GET('id',1);
        
        $article = ArticleBusiness::G()->getArticleFullInfo($id, Helper::PageNo(), Helper::PageSize());
        if (!$article) {
            Helper::Exit404();
            return;
        }
        $article['comments'] = Helper::RecordsetH($article['comments'], ['content','username']);
        $html_pager = Helper::PageHtml($article['comments_total']);
        $url_add_comment = __url('addcomment');
        Helper::Show(get_defined_vars(), 'article');
    }
    public function _old_reg()
    {
        Helper::setViewHeadFoot('user/inc_head.php', 'user/inc_foot.php');
        Helper::Show(get_defined_vars(), 'user/reg');
    }
    public function _old_do_changepass()
    {
        $uid = SessionManager::G()->getCurrentUid();
        //TODO 
    }
    public function do_addcomment()
    {
        $uid = SessionManager::G()->getCurrentUid();
        UserBusiness::G()->addComment($uid, Helper::POST('article_id'), Helper::POST('content'));
        Helper::ExitRouteTo('article/'.Helper::POST('article_id'));
    }
    public function do_delcomment()
    {
        $uid = SessionManager::G()->getCurrentUid();
        UserBusiness::G()->deleteCommentByUser($uid, Helper::POST('id'));
        Helper::ExitRouteTo('');
    }

}
