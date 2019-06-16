<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ArticleModel;
use Neoan3\Model\UserModel;

class ArticleList extends Neoan {
    private function evalFilter($filter,$currentUser=false){
        $articles = [];
        $sql = 'SELECT id FROM article WHERE ';
        $variables = [];
        if(isset($filter['author'])){
            $authorSearch = UserModel::find(['user_name'=>$filter['author']]);
            if(empty($authorSearch)){
                return [];
            }
            $author = $authorSearch[0];
            $variables['author'] = $author['id'];
            $sql .= 'author_user_id = UNHEX({{author}}) ';
            if($currentUser != $author['id'] || ( isset($filter['public']) && $filter['public']) ){
                $sql .= ' AND is_public = 1 AND publish_date IS NOT NULL ';
            }
        }
        // filter deleted
        $sql .= ' AND delete_date IS NULL ';
        // modifiers
        if(isset($filter['orderBy'])){
            $parts = explode(',',$filter['orderBy']);
            $what = strtolower(trim($parts[0]));
            $how = isset($parts[1]) ? strtoupper(trim($parts[1])) : 'ASC';
            $possible = [
                'date'=>'publish_date',
                'title'=>'name'
            ];
            if(isset($possible[$what]) && ($how == 'ASC' || $how == 'DESC')){
                $sql .= 'ORDER BY '.$possible[$what].' '.$how . ' ';
            }

        }
        if(isset($filter['limit'])){

        } else {
            $sql .= ' LIMIT 300';
        }
//        Db::debug();
        $list = Db::ask('>'.$sql,$variables);
        foreach ($list as $item){
            $articles[] = ArticleModel::byId($item['id']);
        }
        return $articles;
    }
    function getArticleList($filter){
        try{
            $jwt = Stateless::validate();
            $userId = $jwt['jti'];
        } catch (RouteException $e){
            $userId = false;
        }
        $articles = $this->evalFilter($filter,$userId);
        return $articles;
    }


}
