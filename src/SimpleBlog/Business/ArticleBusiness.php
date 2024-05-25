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
        $ret = ArticleModel::_()->getList([], 1, 10);
        $ret = [$ret['data'],$ret['count']];
        return $ret;
    }
    public function getArticleList($page = 1, $page_size = 10)
    {
        $ret = ArticleModel::_()->getList([], $page, $page_size);
        
        $ret = [$ret['data'],$ret['count']];
        return $ret;
    }
    public function getArticleFullInfo($id, $page = 1, $page_size = 10)
    {
        $art = ArticleModel::_()->get($id);
        $data = CommentModel::_()->getListByArticle($id, $page, $page_size);
        
        $comments = $data['data'];
        $ids = array_column($data['data'],'id');
        $names = !empty($ids)? Helper::_()->getUsernames($ids) :[];
        foreach($comments as &$v){
            $v['username']= $names[$v['id']];
        }
        unset($v);
        
        $art['comments'] = $comments;
        $art['comments_total'] = $data['count'];
        return $art;
    }
}
