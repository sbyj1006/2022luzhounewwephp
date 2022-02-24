<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Session;

/**
 * 线路套餐
 *
 * @icon fa fa-circle-o
 */
class Personage extends Backend
{

    /**
     * SuitPrice模型对象
     * @var \app\admin\model\Personage
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Personage;
        $this->view->assign("statusList", $this->model->getStatusList());

        $this->view->assign("typeList", $this->model->getTypeList());

    }


    public function index()
    {

        //设置过滤方法

        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)

                ->count();


            $list = $this->model
                ->where($where)

                ->limit($offset, $limit)
                ->select();
//dump($list);die();
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();
    }
    /**
     * 数据导入
     */
    public function import(){
        return parent::import();
    }
    /**
     * 添加
     */
    public function add()
    {

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }

                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }


    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {


            $params = $this->request->post("row/a");


            if ($params) {

                $pers=Db::name('personage')->where('client_id',$params['client_id'])->find();
if($params['status']==1){
                if($pers['type']==1){
                    if($pers['dian']==0) {
//新增客户
                        $puser = Db::name('user')->where('id', $pers['user_id'])->find();
                        $pteam = Db::name('categoryuser')->where('id', $puser['teamid'])->find();
                        $pzhihang = Db::name('categoryuser')->where('id', $puser['zhihangid'])->find();

                        if ($puser && $pteam && $pzhihang) {

                            $addallOrderNumbera['allOrderNumber'] = $puser['allOrderNumber'] + 1;
                            $rea = Db::name('user')->where('id', $pers['user_id'])->update($addallOrderNumbera);

                            $addallOrderNumberb['allOrderNumber'] = $pteam['allOrderNumber'] + 1;
                            $reb = Db::name('categoryuser')->where('id', $puser['teamid'])->update($addallOrderNumberb);

                            $addallOrderNumberc['allOrderNumber'] = $pzhihang['allOrderNumber'] + 1;
                            $rec = Db::name('categoryuser')->where('id', $puser['zhihangid'])->update($addallOrderNumberc);
                            if ($rea && $reb && $rec) {
                                $updian['dian'] = 1;
                                $persup = Db::name('personage')->where('client_id', $params['client_id'])->update($updian);
                                if ($persup) {

                                    $mes='审核通过';
                                } else {

                                    $mes='审核未通过';
                                }

                            } else {
                               $mes='数据绑定不全';
                            }


                        } else {
                            $mes='数据绑定不全';

                        }
                    }else{
                        $mes='已入库计数';
                    }
//
                }elseif($pers['type']==2){
//                  收单客户
                    $puser=Db::name('user')->where('id',$pers['user_id'])->find();
                    $pteam=Db::name('categoryuser')->where('id',$puser['teamid'])->find();
                    $pzhihang=Db::name('categoryuser')->where('id',$puser['zhihangid'])->find();

                    if($puser && $pteam && $pzhihang){

                        $addallOrderNumbera['shoudanOrderNumber']=$puser['shoudanOrderNumber']+1;
                        $rea=Db::name('user')->where('id',$pers['user_id'])->update($addallOrderNumbera);

                        $addallOrderNumberb['shoudanOrderNumber']=$pteam['shoudanOrderNumber']+1;
                        $reb=Db::name('categoryuser')->where('id',$puser['teamid'])->update($addallOrderNumberb);

                        $addallOrderNumberc['shoudanOrderNumber']=$pzhihang['shoudanOrderNumber']+1;
                        $rec=Db::name('categoryuser')->where('id',$puser['zhihangid'])->update($addallOrderNumberc);
                        if($rea && $reb && $rec){
                            $updian['dian']=1;
                            $persup=Db::name('personage')->where('client_id',$params['client_id'])->update($updian);
                            if ($persup) {

                                $mes='审核通过';
                            } else {

                                $mes='审核未通过';
                            }

                        }else{
                            $mes='数据绑定不全';

                        }


                    }else{
                        $mes='数据绑定不全';
                    }

//
                }elseif($pers['type']==3){
//贷款客户
                    $puser=Db::name('user')->where('id',$pers['user_id'])->find();
                    $pteam=Db::name('categoryuser')->where('id',$puser['teamid'])->find();
                    $pzhihang=Db::name('categoryuser')->where('id',$puser['zhihangid'])->find();

                    if($puser && $pteam && $pzhihang){

                        $addallOrderNumbera['daikuanOrderNumber']=$puser['daikuanOrderNumber']+1;
                        $rea=Db::name('user')->where('id',$pers['user_id'])->update($addallOrderNumbera);

                        $addallOrderNumberb['daikuanOrderNumber']=$pteam['daikuanOrderNumber']+1;
                        $reb=Db::name('categoryuser')->where('id',$puser['teamid'])->update($addallOrderNumberb);

                        $addallOrderNumberc['daikuanOrderNumber']=$pzhihang['daikuanOrderNumber']+1;
                        $rec=Db::name('categoryuser')->where('id',$puser['zhihangid'])->update($addallOrderNumberc);
                        if($rea && $reb && $rec){
                            $updian['dian']=1;
                            $persup=Db::name('personage')->where('client_id',$params['client_id'])->update($updian);
                            if ($persup) {

                                $mes='审核通过';
                            } else {

                                $mes='审核未通过';
                            }

                        }else{
                            $mes='数据绑定不全';
                            dump('数据绑定不全');
                        }


                    }else{
                        $mes='数据绑定不全';
                    }

//

                }
}

                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
//                    dump($mes);
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();

            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    $count += $v->delete();
                }
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
