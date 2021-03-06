<?php
/* Generated by neoan3-cli */

namespace Neoan3\Model;


use Neoan3\Apps\Db;
use Neoan3\Apps\Ops;

class ArticleModel extends IndexModel {
    static function byId($id){
        $tables = ['content'];
        $article = parent::first(Db::easy('article.* #article.insert_date:inserted',['id'=>'$'.$id]));
        if(!empty($article)){
            $article['image'] = [];
            if(!empty($article['image_id'])){
                $article['image'] = ImageModel::undeletedById($article['image_id']);
            }
            $article['rating'] = self::ratingById($id);
            $article['author'] = parent::first(Db::easy('user.*',['id'=>'$'.$article['author_user_id']]));
            $article['comments'] = Db::easy('article_comment.*',['article_id'=>'$'.$id,'^delete_date'],['orderBy'=>['insert_date','asc']]);
            $article['category'] = parent::first(Db::easy('category.*',['id'=>'$'.$article['category_id']]));
            $article['store'] = Db::easy('article_store.*',['article_id'=>'$'.$id,'^delete_date']);
            foreach($tables as $table){
                $article[$table] = Db::easy('article_'.$table.'.*',['article_id'=>'$'.$id,'^delete_date'],['orderBy'=>['sort','ASC']]);
            }
        }

        return $article;
    }
    static function bySlug($link){
        $article = Db::easy('article.id',['slug'=>$link]);
        if(!empty($article)){
            $article = self::byId($article[0]['id']);
        }
        return $article;
    }
    static function ratingById($id){
        $rating = parent::first(Db::ask('>SELECT COUNT(id) as total, AVG(rating) as average FROM article_rating WHERE article_id = UNHEX({{id}})',['id'=>$id]));
        return $rating;

    }

    static function find($condition){
        $articles = [];
        if(!isset($condition['delete_date'])){
            $condition['delete_date'] = '';
        }
        foreach($condition as $key => $value){
            if(strpos($key,'_id')!== false || $key == 'id'){
                $condition[$key] = '$'.$value;
            }
        }
        $ids = Db::easy('article.id',$condition,['limit'=>[0,30]]);
        foreach($ids as $id){
            $articles[] = self::byId($id['id']);
        }

        return $articles;
    }

}
