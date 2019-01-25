<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/03/2018
 * Time: 4:09 PM
 */

namespace app\chiia\Controller;

use think\Controller;
use think\Db;
use app\chiia\controller\Base;
use think\Session;
use app\chiia\model\Article as ArticleModel;
use think\Url;
use think\Request;

class Main extends Base{

    public function articleIndex(){
        $result = Db::table('NLP_ARTICLE')->order('articleID desc')->limit(100)->select();
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function searchArticle(){

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
        $labeledby = input('post.labeledby','');
        $upperLikelihood = (input('post.upperLikelihood','') !='') ? (float)input('post.upperLikelihood') : '';
        $lowerLikelihood = (input('post.lowerLikelihood','') !='') ? (float)input('post.lowerLikelihood') : '';

        $ANlike = '%'.$AN.'%';
        $articleIDlike = '%'.$articleID.'%';
        $titlelike = '%'.$title.'%';
        $authorlike = '%'.$author.'%';
        $labeledbylike = '%'.$labeledby.'%';


        $result=Db::query("SELECT * FROM NLP_ARTICLE as A
                              WHERE (A.AN LIKE ? OR ?='')
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
                              AND (A.labeledby LIKE ? OR ?='')
                              AND (A.likelyhood >=? OR ?='')
                              AND (A.likelyhood <=? OR ?='')",
            [$ANlike, $AN, $articleIDlike,$articleID,$titlelike,$title,$authorlike,$author,$fromDate,$fromDate,$toDate,$toDate,
                $blog, $website,$Dowjones,$publication,$blog, $website,$Dowjones,$publication,
                $unlabeled,$labeledR,$labeledIR,$unlabeled,$labeledR,$labeledIR,$labeledbylike,$labeledby,
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


    public function articleList($result){
        $this->assign('articleData',$result);
        ini_set('memory_limit','4096M');
        return $this->fetch();
    }

    public function unLabeledArticleList(){
        $result = Db::table('NLP_ARTICLE')->where('status',0)->order('articleID desc')->limit(1000)->select();
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

    public function allArticleList(){
        $result = Db::table('NLP_ARTICLE')->order('articleID desc')->limit(1000)->select();
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

    public function labeledArticleList(){
        $result = Db::table('NLP_ARTICLE')->where('status',1)->whereOr('status',2)->order('articleID desc')->limit(1000)->select();
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

    public function statistic(){
        $count_article = Db::table('NLP_ARTICLE')->count();
        $count_labeled = Db::table('NLP_ARTICLE')->where('status',1)->whereOr('status',2)->count();
        $count_unlabeled = Db::table('NLP_ARTICLE')->where('status',0)->count();
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

    public function task(){
        ini_set('memory_limit','256M');
        $data= input('get.');
        $id = isset($data['articleID'])? (int)$data['articleID'] : 0;
        $method = isset($data['method']) ? $data['method'] : '';

        //$max_query='db.getCollection(\'ARTICLE\').aggregate({"$group":{id : \'max\',max:value:{"$max":"id"}}})';
        //$min_query='db.getCollection(\'ARTICLE\').aggregate({"$group":{id : \'min\',max:value:{"$min":"id"}}})';

        $max = Db::table('NLP_ARTICLE')->max('articleID');
        $min = Db::table('NLP_ARTICLE')->min('articleID');

        if($id < $min){
            $id = $min;
        }elseif($id >= $max){
            $id = $max;
        }

        if($method == ''){
            $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

            if($result){
                $this->assign('article', $result);
                return $this->fetch();
            }
        }else{
            while(true){
                $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

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


    public function logout(){
        session(null);
        return $this->success('Logout success.','chiia/index/login');
    }


}


