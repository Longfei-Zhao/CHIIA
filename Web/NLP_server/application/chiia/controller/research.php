<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/05/2018
 * Time: 11:26 PM
 */

namespace app\chiia\controller;

use think\Controller;
use think\Db;
use app\chiia\controller\Base;
use think\Session;
use app\chiia\model\Article as ArticleModel;
use think\Url;

class research extends Base{

    public function logout(){
        session(null);
        return $this->success('Logout success.','chiia/index/login');
    }

    public function statistic(){
        $userID = Session::get('userID');
        $count_article = Db::query('SELECT COUNT(articleID) AS article_count FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $count_labeled = Db::query('SELECT COUNT(articleID) AS article_count FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status <> 0',[$userID]);
        $count_unlabeled = Db::query('SELECT COUNT(articleID) AS article_count FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status = 0',[$userID]);

        $count_article = $count_article[0]['article_count'];
        $count_labeled = $count_labeled[0]['article_count'];
        $count_unlabeled = $count_unlabeled[0]['article_count'];

        $result = Db::table('NLP_ML')->where('id',1)->select();
        $accuracy = $result[0]['accuracy']*100;
        $performance = sprintf("%.0f",$accuracy);
        $chartInfo = $result[0]['TERMFREQ'];
        $this->assign('chartInfo',$chartInfo);
        $this->assign('performance',$performance);


        $this->assign('count_article',$count_article);
        $this->assign('count_labeled',$count_labeled);
        $this->assign('count_unlabeled',$count_unlabeled);

        $value = [];
        $current = date('Y-m-d');
        $current_year = date('Y');
        $tmp_date = $current_year.'-01-01';
        $current_year_count = Db::table('NLP_ARTICLE')->where('date','<=',$current)->where('date','>=',$tmp_date)->count();
        for($i=5;$i>0;$i--){
            $tmp_year = $current_year- $i;
            $tmp_lower_date = $tmp_year.'-01-01';
            $tmp_upper_date = $tmp_year.'-12-31';
            $tmp_year_count = Db::table('NLP_ARTICLE')->where('date','<=',$tmp_upper_date)->where('date','>=',$tmp_lower_date)->count();
            $array = [strval($tmp_year), $tmp_year_count];
            $value[] = $array;
        }
        $array = [ $current_year, $current_year_count,];
        $value[] = $array;
        $yearly_count = json_encode($value);
        $this->assign('yearlyCount',$yearly_count);

        return $this->fetch();
    }

    public function articleIndex(){
        $userID = Session::get('userID');
        $result = Db::query("SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? ORDER BY articleID DESC LIMIT 100",[$userID]);
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function searchArticle(){
        $data= input('post.');
        $userID = Session::get('userID');

        $AN = input('post.AN','');
        $articleID = input('post.articleID','');
        $title = input('post.title','');
        $author = input('post.author','');
        $fromDate = input('post.fromDate','');
        $toDate = input('post.toDate','');
        $blog = input('post.blog','');
        $website = input('post.website','');
        $Dowjones = input('post.Dowjones','');
        $publication = input('post.publication','');
        $unlabeled = (input('post.unlabeled','') != '') ? (int)input('post.unlabeled') : 9;
        $labeledR = (input('post.labeledR','') !='') ? (int)input('post.labeledR') : 9;
        $labeledIR = (input('post.labeledIR','') !='') ? (int)input('post.labeledIR') : 9;
        $upperLikelihood = (input('post.upperLikelihood','') !='') ? (float)input('post.upperLikelihood') : '';
        $lowerLikelihood = (input('post.lowerLikelihood','') !='') ? (float)input('post.lowerLikelihood') : '';

        $ANlike = '%'.$AN.'%';
        $articleIDlike = '%'.$articleID.'%';
        $titlelike = '%'.$title.'%';
        $authorlike = '%'.$author.'%';


        $result=Db::query("SELECT * FROM NLP_ARTICLE as A NATURAL JOIN NLP_JOBLIST AS J 
                              WHERE (J.userID=?)
                              AND (A.AN LIKE ? OR ?='')
                              AND (A.articleID LIKE ? OR ?='')
                              AND (A.title LIKE ? OR ?='')
                              AND (A.author LIKE ? OR ?='')
                              AND (A.date >=? OR ?='')
                              AND (A.date <=? OR ?='')
                              AND ((A.source=?
                              OR A.source=?
                              OR A.source=?
                              OR A.source=?)
                              OR (?='' AND ?='' AND  ?='' AND ?=''))
                              AND ((A.status=?
                              OR A.status=?
                              OR A.status=?)
                              OR (?=9 AND ?=9 AND ?=9))
                              AND (A.likelyhood >=? OR ?='')
                              AND (A.likelyhood <=? OR ?='')",
            [$userID,$ANlike, $AN, $articleIDlike,$articleID,$titlelike,$title,$authorlike,$author,$fromDate,$fromDate,$toDate,$toDate,
                $blog, $website,$Dowjones,$publication,$blog, $website,$Dowjones,$publication,
                $unlabeled,$labeledR,$labeledIR,$unlabeled,$labeledR,$labeledIR,
                $upperLikelihood,$upperLikelihood,$lowerLikelihood,$lowerLikelihood]);

        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'url' => $tmp['url'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');


        $this->assign('articleData',$value);
        return $this->fetch();
    }

    public function articlelist($result){
        ini_set('memory_limit','4096M');
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function unlabeledarticlelist(){
        $userID = Session::get('userID');
        $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status = 0',[$userID]);
        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'url' => $tmp['url'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');
        return action('articleList',['result'=>$value]);
    }

    public function allarticlelist(){
        $userID = Session::get('userID');
        $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'url' => $tmp['url'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');
        return action('articleList',['result'=>$value]);
    }

    public function labeledarticlelist(){
        $userID = Session::get('userID');
        $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status <> 0',[$userID]);
        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'url' => $tmp['url'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');
        return action('articleList',['result'=>$value]);
    }

    public function task(){
        $userID = Session::get('userID');
        $data= input('get.');
        $id = isset($data['articleID'])? (int)$data['articleID'] : 0;
        $method = isset($data['method']) ? $data['method'] : '';

        $max= Db::query('SELECT MAX(articleID) AS maxID FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $min= Db::query('SELECT MIN(articleID) AS minID FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $max = $max[0]['maxID'];
        $min = $min[0]['minID'];


        if($id < $min){
            $id = $min;
        }elseif($id >= $max){
            $id = $max;
        }

        if($method == ''){
            $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND articleID=?',[$userID,$id]);

            if($result){
                $this->assign('article', $result);
                return $this->fetch();
            }
        }else{
            while(true){
                $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND articleID=?',[$userID,$id]);

                if($result){
                    $this->assign('article', $result);
                    return $this->fetch();
                }

                if($method == 'next'&& $id<$max){
                    $id = $id+1;
                }elseif($method == 'last' && $id>$min){
                    $id = $id-1;
                }
            }
        }
    }

    public function viewWebsiteContent(){
        ini_set('memory_limit','256M');
        $data= input('post.');
        $id = $data['articleID'];

        $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

        if($result){
            $this->assign('article', $result);
            return $this->fetch();
        }
    }

    public function labelRelevent(){
        $data = input('get.');
        $id = $data['articleID'];
        $user = Session::get('username');

        $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->update(['status'=>1, 'assign'=>'1',
            'labeledby'=> $user,'labeledtime' => date("Y-m-d"),'likelyhood'=>1.0]);

        if($result){
            return $this->success('Labeled success');
        }
        else{
            return $this->error('Labeled failed');
        }
    }

    public function labelIrrelevent(){
        $data = input('get.');
        $id = $data['articleID'];
        $user = Session::get('username');

        $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->update(['status'=>2, 'assign'=>'1',
            'labeledby'=> $user,'labeledtime' => date("Y-m-d"),'likelyhood'=>0]);

        if($result){
            return $this->success('Labeled success');
        }
        else{
            return $this->error('Labeled failed');
        }
    }

}