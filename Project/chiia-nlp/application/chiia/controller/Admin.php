<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 14/03/2018
 * Time: 8:31 PM
 */

namespace app\chiia\controller;

use app\chiia\model\Spider;
use app\chiia\validate\User;
use think\console\command\make\Model;
use think\Controller;
use think\Db;
use app\chiia\model\User as UserModel;
use app\chiia\validate\User as UserValidate;
use app\chiia\validate\Spider as SpiderValidate;
use app\chiia\model\Article as ArticleMode;


class Admin extends Controller{

    public function add(){
        return $this->fetch();

    }

    public function insert(){

        $data = input('post.');
        $val = new UserValidate();
        if (!$val -> check($data)){
            $this->error($val->getError());
            exit;
        }

        $user = new UserModel($data);
        $result = $user->allowField(true)->save();
        if($result){
            $this->success('Success','admin/userList');
        }else{
            $this->error('Failed');
        }

    }

    public function update(){

        $data = input('post.');
        $id = input("post.userID");

        $val = new UserValidate();
        if (!$val -> check($data)){
            $this->error($val->getError());
            exit;
        }

        $user = new UserModel();
        $result = $user->allowField(true)->save($data,['id' => $id]);

        if($result){
            $this->success('Update Successfully', 'admin/userList');
        } else {
            $this->error('Update Failed');
        }
    }


    public function userManagement(){
        return $this->fetch();
    }

    public function userList(){
        $result = Db::table('NLP_USER')->select();

        foreach($result as &$i){
            $count = Db::table('NLP_JOBLIST')->where('userID',$i['userID'])->count('userID');
            $i['count'] = $count;
        }

        $this->assign('userData',$result);
        return $this->fetch();
    }

    public function editUser(){
        $id = input('get.userID');
        $data = Db::table('NLP_USER')->where('userID',$id)->select();
        $this->assign('data',$data);


        return $this->fetch();
    }

    public function deleteUser(){
        $id = input('get.userID');
        $result = false;

        $usersArticle = Db::table('NLP_JOBLIST')->where('userID',$id)->select();

        foreach ($usersArticle as $k){
            Db::table('NLP_ARTICLE')->where('articleID', $k['articleID'])->update(['assign'=>'0']);
        }

        $temp_result = Db::table("NLP_JOBLIST")->where('userID',$id)->delete(true);

        if($temp_result){
            $result = UserModel::destroy($id,true);
        }

        if($result){
            $this->success('Delete Successfully', 'admin/userList');
        } else {
            $this->error('Delete Failed');
        }
    }

    public function assignIndex(){
        $result = Db::table('NLP_ARTICLE')->where('status','=',0)->where('assign','=',0)->order('articleID desc')->limit(100)->select();
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function unassignedList(){
        $data= input('post.');

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
        $upperLikelihood = (input('post.upperLikelihood','') !='') ? (float)input('post.upperLikelihood') : '';
        $lowerLikelihood = (input('post.lowerLikelihood','') !='') ? (float)input('post.lowerLikelihood') : '';

        $ANlike = '%'.$AN.'%';
        $articleIDlike = '%'.$articleID.'%';
        $titlelike = '%'.$title.'%';
        $authorlike = '%'.$author.'%';

//        dump($fromDate);
//        dump($toDate);
//        die;


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
                              AND A.status=0
                              AND A.assign=0
                              AND (A.likelyhood >=? OR ?='')
                              AND (A.likelyhood <=? OR ?='')",
            [$ANlike, $AN, $articleIDlike,$articleID,$titlelike,$title,$authorlike,$author,$fromDate,$fromDate,$toDate,$toDate,
                $blog, $website,$Dowjones,$publication,$blog, $website,$Dowjones,$publication,
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
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');
        $this->assign('data',$value);
        return $this->fetch();
    }

//    public function unassignedJobList(){
//        $result = Db::table('NLP_ARTICLE')->where('assign',0)->where('status=0 OR status=2')->select();
//        $this->assign('data',$result);
//        return $this->fetch();
//    }

    public function selectWorker(){
        $data = input('post.');
        $articleID = $data['chosenArticle'];

        $sql1 = "SELECT u.userID , u.username, COUNT(u.userID) AS undoJOB FROM NLP_USER AS u RIGHT OUTER JOIN (SELECT articleID,userID FROM NLP_ARTICLE AS a NATURAL JOIN NLP_JOBLIST WHERE a.status=0) AS r ON u.userID = r.userID GROUP BY u.userID ORDER BY undoJOB DESC";
        $result1 = Db::query($sql1);

        $sql2 = "SELECT userID, username FROM NLP_USER";
        $result2 = Db::query($sql2);

        $tmp = [];
        foreach ($result1 as $i){
            array_push($tmp, $i['userID']);
        }

        foreach($result2 as $k){
            if(!in_array($k['userID'],$tmp))
                array_push($result1,['userID'=>$k['userID'], 'username'=> $k['username'],'undoJOB'=>0]);
        }

        $this->assign('userUndoJobNo',$result1);
        $this->assign('chosenArticle' ,$articleID);

        return $this->fetch();
    }

    public function updateJoblist(){
        $data = input('post.');

        $articles = $data['chosenArticle'];
        $userID =$data['chosenWorker'];

        foreach ($articles as $articleID){
            $result1 = Db::table('NLP_ARTICLE')->where('articleID', $articleID)->update(['assign'=>'1']);
            $result2 = Db::table('NLP_JOBLIST')->insert(['articleID'=>$articleID, 'userID'=>$userID, 'assignedDate'=> date("Y-m-d")]);
        }

        if( $result1 && $result2){
            $this->success('Assigned Successfully', 'admin/viewJobList');
        } else {
            $this->error('Assigned Failed');
        }
        return $this->fetch();
    }

    public function viewJobList(){
        $sql = "SELECT articleID, title, userID, username, assignedDate FROM (NLP_JOBLIST NATURAL JOIN NLP_ARTICLE)NATURAL JOIN NLP_USER";
        $result = Db::query($sql);

        $this->assign('data', $result);

        return $this->fetch();
    }


    public function spiderSetting(){
        $result = Db::table('NLP_SPIDER')->order("id desc")->select();
        foreach ($result as &$tmp){
            $tmp['progress'] = $tmp['progress']*100;
            $tmp['progress'] = sprintf("%.2f",$tmp['progress']);
            $tmp['progress'] = $tmp['progress'].'%';
        }
        $this->assign('record',$result);
        return $this->fetch();
    }

    public function getProgress(){
        $current = Db::table('NLP_SPIDER')->order('id desc')->limit(1)->select();
        $result = $current[0]["progress"]*100;
        $num = sprintf("%.2f",$result);
        return $num;
    }

    public function getLog(){
        $current = Db::table('NLP_SPIDER')->order('id desc')->limit(1)->select();
        $crawlerLog = $current[0]["log"];
        $segmentLog = explode("\n",$crawlerLog);
        $count = count($segmentLog);
        $viewLog = [];
        if($count>100){
            for($i = $count-100; $i<$count; $i++){
                $viewLog[] = "<br>".$segmentLog[$i];
            }
        }else{
            for($i =0; $i<$count;$i++){
                $viewLog[] = "<br>".$segmentLog[$i];
            }
        }
        return $viewLog;
    }

    public function addSpider(){
        return $this->fetch();
    }

    public function insertSpider(){
        $data = input('post.');
        $val = new SpiderValidate();
        if (!$val -> check($data)){
            $this->error($val->getError());
            exit;
        }

        $user = new Spider($data);
        $result = $user->allowField(true)->save();
        if($result){
            $this->success('Success','admin/spidersetting');
        }else{
            $this->error('Failed');
        }

    }

    public function updateSpider(){
        $data = input('post.');
        $id = input("post.id");

        $val = new SpiderValidate();

        if (!$val -> check($data)){
            $this->error($val->getError());
            exit;
        }

        $spider = new Spider();
        $result = $spider->allowField(true)->save($data,['id' => $id]);

        if($result){
            $this->success('Update Successfully', 'admin/spiderSetting');
        } else {
            $this->error('Update Failed');
        }

    }

    public function mlSetting(){
        return $this->fetch();
    }

    public function startSpider(){
        ignore_user_abort(true);
        set_time_limit(0);

        exec("cd Crawler/headless;nohup python crawler.py & 2>&1",
            $result,$status);

        if($status != 0 ){
            $this->error('Error','spiderSetting','',2);

        } else {
            $this->success('execute successfully! Please wait for a while!','spiderSetting','',2);
        }

    }

    public function getModelLog(){
        $current = Db::table('NLP_ML')->order('id desc')->limit(1)->select();
        $crawlerLog = $current[0]["log"];
        $segmentLog = explode("\n",$crawlerLog);
        $count = count($segmentLog);
        $viewLog = [];
        if($count>100){
            for($i = $count-100; $i<$count; $i++){
                $viewLog[] = "<br>".$segmentLog[$i];
            }
        }else{
            for($i =0; $i<$count;$i++){
                $viewLog[] = "<br>".$segmentLog[$i];
            }
        }
        return $viewLog;
    }

    public function startML(){
        ignore_user_abort(true);
        set_time_limit(0);

        exec("cd Crawler/headless;nohup python train.py & 2>&1",
            $result,$status);

        if($status != 0 ){
            $this->error('Error','mlSetting','',2);

        } else {
            $this->success('execute successfully! Please wait for a while!','mlSetting','',2);
        }

    }

}