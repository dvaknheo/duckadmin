<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Business;

use SimpleBlog\Model\ArticleModel;
use SimpleBlog\Model\CommentModel;

class ArticleBusiness extends Base
{
    public function getRecentArticle()
    {
        return ArticleModel::_()->getList([], 1, 10);
    }
    public function getArticleList($page = 1, $page_size = 10)
    {
        $ret = ArticleModel::_()->getList([], $page, $page_size);
        return $ret;
    }
    public function getArticleFullInfo($id, $page = 1, $page_size = 10)
    {
        $art = ArticleModel::_()->get($id);
        if(!$art){
            return [];
        }
        [$total,$comments] = CommentModel::_()->getListByArticle($id, $page, $page_size);
        
        $ids = array_column($comments,'user_id');
        $names = Helper::UserService()->batchGetUsernames($ids);
        foreach($comments as &$v){
            $v['username']= $names[$v['user_id']]??'--';
        }
        unset($v);
        
        $art['comments'] = $comments;
        $art['comments_total'] = $total;
        return $art;
    }
}
