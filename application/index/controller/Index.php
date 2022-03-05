<?php

namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\controller\Frontend;
use think\Session;
use app\common\model\Personage;
use app\common\model\User;

class Index extends Frontend
{

    public function index()
    {



  return $this->fetch();
    }

    public function client_id(){

            $message=Db::name('personage')->select();

foreach ($message as $key=>$val){
    $updata['type']=$val['type'];

    $res=Db::name('personageyuan')->where('client_id',$val['client_id'])->update($updata);
    if($res){
        dump($val['client_id']."<br>更新成功");
    }else{
        dump($val['client_id']."<br>更新失败");
    }

}



    }

    public function gengxinkehuhao(){
        $names=input('names');
        $updata['type']=1;
        $updata['username']=$names;
        $updata['status']=1;
        $message=Db::name('personage')->where($updata)->select();

        foreach ($message as $key=>$val){


            $res=Db::name('personage')->where('client_id',$val['client_id'])->where($updata)->count();
            if($res==1){
                dump($val['client_id']."<br>正常");
            }else{
                dump($val['client_id']."<br>不正常".$res);
            }

        }



    }

    public function userup(){
        $names=input('names');
        $User = new User();

        $updata=[];
        $datas=$User->select();
        foreach ($datas as $key=>$v){
            $updata[]=['id'=>$v['id'],'iscs'=>1,'allOrderNumber'=>0,'shoudanOrderNumber'=>0,'daikuanOrderNumber'=>0,'teamid'=>'','teamname'=>'无','status'=>1];
        }


        $message=$User->isUpdate(true)->saveALL($updata,false);
        $cont=$User->where('iscs',1)->count();
        dump($message);




    }

    public function typeli(){
        $names=input('names');
        $Personage = new Personage();
        $where['type']=1;
        $updata=[];
        $datas=$Personage->where($where)->select();
foreach ($datas as $key=>$v){
    $updata[]=['id'=>$v['id'],'type'=>9];
}


//        $message=$Personage->isUpdate(true)->saveALL($updata,false);
        $cont=$Personage->where('type',1)->count();
        dump($cont);
//
//        foreach ($message as $key=>$val){
//
//
//            $res=Db::name('personage')->where('client_id',$val['client_id'])->update($updata);
//            if($res==1){
//                dump($val['client_id']."<br>正常");
//            }else{
//                dump($val['client_id']."<br>不正常".$res);
//            }
//
//        }



    }



    public function gengxingpaihangshoudan()
    {
        $client_id = input('client_id');
        $where['client_id']=$client_id;
        $where['status']=1;
        $where['type']=2;
        $where['sort']=0;
        if($client_id){

            $datasa = Db::name('personage')->where($where)->find();
//            dump($datasa);die();
if($datasa){
            if($datasa['user_id']){
                $dd['user'] = Db::name('user')->where('id',$datasa['user_id'])->find();

                $upu['shoudanOrderNumber']=$dd['user']['shoudanOrderNumber']-1;
                $updateu = Db::name('user')->where('id',$datasa['user_id'])->update($upu);
if($updateu){
    $ddshow['user']=$client_id.'个人排行数据更新成功';
}else{
    $ddshow['user']=$client_id.'个人排行失败';
}
            }

            if($datasa['teamid']){
                $dd['teams'] = Db::name('categoryuser')->where('id',$datasa['teamid'])->find();
                $upt['shoudanOrderNumber']=$dd['teams']['shoudanOrderNumber']-1;
                $updatet = Db::name('categoryuser')->where('id',$datasa['teamid'])->update($upt);
                if($updatet){
                    $ddshow['team']=$client_id.'团队排行数据更新成功';
                }else{
                    $ddshow['team']=$client_id.'团队排行失败';
                }
            }

            if($datasa['zhihangid']){
                $dd['zhihiang'] = Db::name('categoryuser')->where('id',$datasa['zhihangid'])->find();
                $upz['shoudanOrderNumber']=$dd['zhihiang']['shoudanOrderNumber']-1;
                $updatez = Db::name('categoryuser')->where('id',$datasa['zhihangid'])->update($upz);
                if($updatez){
                    $ddshow['zhihang']=$client_id.'团队排行数据更新成功';
                }else{
                    $ddshow['zhihang']=$client_id.'团队排行失败';
                }
            }
            $updatap['sort']=1;
            $datasp = Db::name('personage')->where($where)->update($updatap);
            if($datasp){
                $ddshow['kehu']=$client_id.'客户排行数据更新成功';
            }else{
                $ddshow['kehu']=$client_id.'客户排行失败';
            }
            dump($ddshow);
}else{
    dump('以更新');
}
        }

    }



    public function gengxingpaihangdaikuan()
    {
        $client_id = input('client_id');
        $where['client_id']=$client_id;
        $where['status']=1;
//        $where['type']=3;
        $where['sort']=0;
        if($client_id){

            $datasa = Db::name('personage')->where($where)->find();
//            dump($datasa);die();
            if($datasa){
                if($datasa['user_id']){
                    $dd['user'] = Db::name('user')->where('id',$datasa['user_id'])->find();

                    $upu['daikuanOrderNumber']=$dd['user']['daikuanOrderNumber']-1;
                    $updateu = Db::name('user')->where('id',$datasa['user_id'])->update($upu);
                    if($updateu){
                        $ddshow['user']=$client_id.'个人排行数据更新成功'.$upu['daikuanOrderNumber'];
                    }else{
                        $ddshow['user']=$client_id.'个人排行失败'.$upu['daikuanOrderNumber'];
                    }
                }

                if($datasa['teamid']){
                    $dd['teams'] = Db::name('categoryuser')->where('id',$datasa['teamid'])->find();
                    $upt['daikuanOrderNumber']=$dd['teams']['daikuanOrderNumber']-1;
                    $updatet = Db::name('categoryuser')->where('id',$datasa['teamid'])->update($upt);
                    if($updatet){
                        $ddshow['team']=$client_id.'团队排行数据更新成功'.$upt['daikuanOrderNumber'];
                    }else{
                        $ddshow['team']=$client_id.'团队排行失败'.$upt['daikuanOrderNumber'];
                    }
                }

                if($datasa['zhihangid']){
                    $dd['zhihiang'] = Db::name('categoryuser')->where('id',$datasa['zhihangid'])->find();
                    $upz['daikuanOrderNumber']=$dd['zhihiang']['daikuanOrderNumber']-1;
                    $updatez = Db::name('categoryuser')->where('id',$datasa['zhihangid'])->update($upz);
                    if($updatez){
                        $ddshow['zhihang']=$client_id.'团队排行数据更新成功'.$upz['daikuanOrderNumber'];
                    }else{
                        $ddshow['zhihang']=$client_id.'团队排行失败'.$upz['daikuanOrderNumber'];
                    }
                }
                $updatap['sort']=1;
                $datasp = Db::name('personage')->where($where)->update($updatap);
                if($datasp){
                    $ddshow['kehu']=$client_id.'客户排行数据更新成功';
                }else{
                    $ddshow['kehu']=$client_id.'客户排行失败';
                }
                dump($ddshow);
            }else{
                dump('以更新');
            }
        }

    }


    public function chap()
    {
        $where['id']=array('between',array('967','1145'));
        $where['type']=3;
        $datasa = Db::name('personage')->where($where)->select();
//        $whereb['id']=array('between',array('967','1145'));
//        $whereb['type']=3;
//        $datasb = Db::name('personage')->where($whereb)->select();
        $dd=[];
        if($datasa){
            foreach ($datasa as $key=>$val){
                $wherecf['client_id']=$val['client_id'];
                $upd['type']=1;
//                $datascf = Db::name('personage')->where($wherecf)->select();
$updata= Db::name('personage')->where($wherecf)->update($upd);
                if($updata){
                $dd['chenggong'][$key]=$val['id'].','.$val['username'].','.$val['client_id'].','.'收单客户调整成功'.','.$val['client_name'];
                }else{
                    $dd['shibai'][$key]=$val['id'].'失败';
                }
            }
        }

        return json_encode($dd);

    }

    public function chaxccf()
    {
        $zhuanhua = Db::name('personage')->where('type',1)->select();
        dump(count($zhuanhua));
        foreach ($zhuanhua as $key => $val) {
            $cha = Db::name('personage')->where('client_id',$val['client_id'])->where('type',1)->where('username',$val['username'])->where('teamname',$val['teamname'])->select();
if(count($cha)>1){
    $del=Db::name('personage')->where('id',$val['id'])->delete();
    if($del){
    $chongfu['chenggong'][$key]=$val['id'].'-'.$val['type'].'-'.$val['teamname'].'-'.$val['username'].'-'.$val['client_id'].'成功';
    }else{
        $chongfu['shibai'][$key]=$val['id'].'失败';
    }
}
        }
dump($chongfu);
    }
    public function chazhuanhuacf()
    {
        $zhuanhua = Db::name('personage_zhuanhua')->select();
        $chongfu=[];
        foreach ($zhuanhua as $key=>$val){
            $cha = Db::name('personage')->where('client_id',$val['client_id'])->find();
            $users = Db::name('user')->where('nickname',$val['username'])->find();
            $update['type']=1;
            $uppersonage=Db::name('personage')->where('client_id',$val['client_id'])->update($update);
//
            unset($cha['id']);
            $cha['type']=$val['type'];
            $cha['username']=$users['nickname'];
            $cha['user_id']=$users['id'];
            $cha['teamid']=$users['teamid'];
            $cha['zhihangid']=$users['zhihangid'];
            $cha['zhihangname']=$users['zhihangname'];
            $cha['teamname']=$users['teamname'];

            $addpersonage=Db::name('personage')->insert($cha);

            if($addpersonage && $uppersonage){
//                $zhuanhuadata[$key]=$val['client_id'].'-'.$val['username'].'-'.$val['type'].'<br>';
                $zhuanhuadata[$key]=$val['id'].'-'.$users['nickname'].'-'.$users['zhihangid'].'-'.$users['zhihangname'].'-'.$users['teamid'].'-'.$users['teamname'].'成功<br>';

            }else{
              $no[$key]=$val['id'].'<br>';
            }
        }
        dump($zhuanhuadata);dump($no);
    }

        public function chazhuanhua(){
        $zhuanhua=Db::name('personage_zhuanhua')->select();

        foreach ($zhuanhua as $key=>$val){
$xzyes=Db::name('personage_yuanzhixinzeng')->where('client_id',$val['client_id'])->find();
            $xzyeschuli=Db::name('personage')->where('client_id',$val['client_id'])->find();
            $xzyeszhuanhua=Db::name('personage_zhuanhua')->where('client_id',$val['client_id'])->find();
            $zhuanhuayuan[$key]['shify']=$xzyes['username'].'-'.$xzyes['client_id'];
            $zhuanhuayuan[$key]['shifychuli']=$xzyeschuli['username'].'-'.$xzyeschuli['client_id'];
            $zhuanhuayuan[$key]['zhuanhua']=$xzyeszhuanhua['username'].'-'.$xzyeszhuanhua['client_id'];

            if($val['username']!=$xzyeschuli['username']){
                $butong[$key]=$val['client_id'].'-'.$val['id'];
            }

        }


        dump($butong);dump($zhuanhuayuan);
    }

public function checkxinzheng(){
    $users = Db::name('user')->select();

    if($users){
    foreach ($users as $key=>$val){

        $res=Db::name('personage')->where('user_id',$val['id'])->where('type',1)->count();

        if($res){
            $updata['allOrderNumber']=$res;
            $users = Db::name('user')->where('id',$val['id'])->update($updata);
            if($users){
                $showdata['xinzheng'][$key]=$val['nickname'].'新增排行数据更新成功：'.$val['allOrderNumber'].'-记录：'.$res;
            }else{
                $showdata['xinzheng'][$key]=$val['nickname'].'新增排行数据更新失败：'.$val['allOrderNumber'].'-记录：'.$res;
            }

        }

    }
    }
    exit(json_encode(['showdata'=>$showdata]));
}


    public function checkshoudan(){
        $users = Db::name('user')->select();

        if($users){
            foreach ($users as $key=>$val){

                $res=Db::name('personage')->where('user_id',$val['id'])->where('type',2)->count();

                if($res){
                    $updata['shoudanOrderNumber']=$res;
                    $users = Db::name('user')->where('id',$val['id'])->update($updata);
                    if($users){
                        $showdata['xinzheng'][$key]=$val['nickname'].'新增排行数据更新成功：'.$val['shoudanOrderNumber'].'-记录：'.$res;
                    }else{
                        $showdata['xinzheng'][$key]=$val['nickname'].'新增排行数据更新失败：'.$val['shoudanOrderNumber'].'-记录：'.$res;
                    }

                }

            }
        }
        exit(json_encode(['showdata'=>$showdata]));
    }



    public function checkdaikuan(){
        $users = Db::name('user')->select();

        if($users){
            foreach ($users as $key=>$val){

                $res=Db::name('personage')->where('user_id',$val['id'])->where('type',3)->count();

                if($res != $val['daikuanOrderNumber']){
                    $showdata['xinzheng'][$key]=$val['nickname'].'贷款排行数据：'.$val['daikuanOrderNumber'].'-贷款记录：'.$res;
                }

            }
        }
        exit(json_encode(['showdata'=>$showdata]));
    }

    public function client_id_dian(){

        $message=Db::name('personage')->select();

        foreach ($message as $key=>$val){
            $updata['dian']=1;

            $res=Db::name('personage')->where('id',$val['id'])->update($updata);
            if($res){
                dump($val['client_id']."<br>更新成功");
            }else{
                dump($val['client_id']."<br>更新失败");
            }

        }



    }

    public function datecheck()
    {
        $listd=Db::name('personage')->select();
foreach ($listd as $key=>$val){

    $time = (intval($val['addtimeb'])-25569)*24*60*60; //获得秒数

    $res=Db::name('personage')->where('id',$val['id'])->update(array('addtime'=>date('Y-m-d', intval($time))));
if($res){
    dump("<br>更新成功2");
}else{
    dump("<br>更新失败2");
}
//    dump("<br>".$val['client_id'].$val['addtimeb']."-".date('Y-m-d', intval($time))."<br>");

}

    }


    public function datecheckkh()
    {
        $listd=Db::name('personage')->where('type',1)->select();
        foreach ($listd as $key=>$val){


            $res=Db::name('personage')->where('id',$val['id'])->update(array('client_id'=>"KH".$val['client_id']));
            if($res){
                dump("<br>更新成功3");
            }else{
                dump("<br>更新失败3");
            }
//    dump("<br>".$val['client_id'].$val['addtimeb']."-".date('Y-m-d', intval($time))."<br>");

        }

    }

    public function datecheckteamname()
    {
        $listd=Db::name('personage')->where('teamname','蔺支行营销科')->select();
        foreach ($listd as $key=>$val){


            $res=Db::name('personage')->where('id',$val['id'])->update(array('teamname'=>"古蔺支行营销科"));
            if($res){
                dump("<br>更新成功3".$val['id']);
            }else{
                dump("<br>更新失败3".$val['id']);
            }
//    dump("<br>".$val['client_id'].$val['addtimeb']."-".date('Y-m-d', intval($time))."<br>");

        }

    }

    public function upuserquannian(){
        $listd=Db::name('user')->select();
//        dump($listd);die();
        foreach ($listd as $key=>$val){

            $updatas['all_allOrderNumber']=$val['allOrderNumber'];
            $updatas['all_shoudanOrderNumber']=$val['shoudanOrderNumber'];
            $resb=$listd=Db::name('user')->where('id',$val['id'])->update($updatas);

            if($resb){

                dump("<br>更新成功55".$resb['nickname']);
            }else{
                dump("<br>更新失败33".$resb['nickname']);
            }
        }

    }

    public function upuserteamidsd()
    {
        $wheres['allOrderNumber']=['>',0];
        $listd=Db::name('user')->where($wheres)->select();
//        dump($listd);die();
        foreach ($listd as $key=>$val){


            $res=Db::name('personage')->where('user_id',$val['id'])->find();
            $resa=Db::name('categoryuser')->where('name',$res['teamname'])->where('levels',2)->find();
            if($resa){
                $updatas['teamid']=$resa['id'];
                $updatas['teamname']=$resa['name'];
                $updatas['iszhjs']=2;
                $updatas['iscs']=2;
                $resb=$listd=Db::name('user')->where('id',$val['id'])->update($updatas);

                if($resb){



                    dump("<br>更新成功5".$res['username']);
                }else{
                    dump("<br>更新失败3".$val['nickname']);
                }

            }else{
                dump("<br>没有找到团队".$val['id']);
            }

        }

    }

    public function upkehuuser()
    {
        $listd=Db::name('personage')->select();
        foreach ($listd as $key=>$val){


            $updataal=Db::name('user')->where('nickname',$val['username'])->find();
            $updata['user_id']=$updataal['id'];

            if ($val['zhihangname']==$updataal['zhihangname']){

                $updata['zhihangid']=$updataal['zhihangid'];
            }else{

                $updata['zhihangid']=Db::name('categoryuser')->where('name',$val['zhihangname'])->where('levels',1)->value('id');
            }
            if ($val['teamname']==$updataal['teamname']){
                $updata['teamid']=$updataal['teamid'];
            }else{
                dump($teamname[$key]="团队名称不同".$val['id']);
                $updata['teamid']=Db::name('categoryuser')->where('name',$val['teamname'])->where('levels',2)->value('id');
            }


            $res=Db::name('personage')->where('id',$val['id'])->update($updata);
            if($res){
                dump("<br>更新成功ID:".$updata['user_id']);
            }else{
                dump("<br>更新失败".$val['username']);
            }
//    dump("<br>".$val['client_id'].$val['addtimeb']."-".date('Y-m-d', intval($time))."<br>");

        }

    }

    public function upkehuuserteamzhihang()
    {
        $listd=Db::name('personage')->select();
        foreach ($listd as $key=>$val){
            $upser=Db::name('user')->where('nickname',$val['username'])->find();

            $updata['user_id']=$upser['id'];
            $updata['teamid']=$upser['teamid'];
            $updata['zhihangid']=$upser['zhihangid'];
            $res=Db::name('personage')->where('id',$val['id'])->update($updata);
            if($res){
                dump("<br>更新成功ID:".$updata['user_id']);
            }else{
                dump("<br>更新失败");
            }
//    dump("<br>".$val['client_id'].$val['addtimeb']."-".date('Y-m-d', intval($time))."<br>");

        }

    }



    public function userteam()
    {
        $listd=Db::name('user')->select();
        foreach ($listd as $key=>$val){
            $upsera=Db::name('categoryuser')->where(array('id'=>$val['teamid'],'levels'=>2))->find();
            $upserb=Db::name('categoryuser')->where(array('id'=>$val['zhihangid'],'levels'=>1))->find();

if($upsera){
    $updata['teamid']=$upsera['id'];
    $updata['teamname']=$upsera['name'];
}else{
    $updata['teamid']=73;
    $updata['teamname']='其他团队';
}
            if($upserb){
                $updata['zhihangid']=$upserb['id'];
                $updata['zhihangname']=$upserb['name'];
            }else{
                $updata['zhihangid']=8;
                $updata['zhihangname']='其他支行';
            }

            $res=Db::name('user')->where('id',$val['id'])->update($updata);
            if($res){
                dump("<br>更新成功ID:".$val['id']);
            }else{
                dump("<br>更新失败");
            }
//    dump("<br>".$val['client_id'].$val['addtimeb']."-".date('Y-m-d', intval($time))."<br>");

        }

    }


}
