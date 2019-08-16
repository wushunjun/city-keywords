<?php
/**
 * 城市管理
 */
require_once(dirname(__FILE__) . "/config.php");
require_once(DEDEINC . "/datalistcp.class.php");
if(empty($dopost)) $dopost = '';
if($dopost=='info'){
    $dlist = new DataListCP();
    if(isset($id) && $id){
        $query = "select * from #@__city_site where id=".$id;
        $info = $dsql->GetOne($query);
    }
    $dlist->SetTemplet(DEDEADMIN."/templets/city_info.htm");
    $dlist->display();
}elseif($dopost=='save'){
    if(isset($id) && $id){
        $query = "update #@__city_site set `name`='{$name}',pinyin='{$pinyin}',banner_id='{$banner_id}',product_id='{$product_id}',news_id='{$news_id}',is_master='{$is_master}' where id=".$id;
        if(!$dsql->ExecuteNoneQuery($query))
        {
            ShowMsg("更新数据库city_site表时出错，请检查！".$dsql->GetError(),"javascript:;");
            exit();
        }
    }else{
        $query = "insert into #@__city_site set `name`='{$name}',pinyin='{$pinyin}',banner_id='{$banner_id}',product_id='{$product_id}',news_id='{$news_id}',is_master='{$is_master}'";
        if(!$dsql->ExecuteNoneQuery($query))
        {
            ShowMsg("插入数据库city_site表时出错，请检查！".$dsql->GetError(),"javascript:;");
            exit();
        }
    }

    //返回成功信息
    $msg = "    　　请选择你的后续操作：
    <a href='city_do.php?dopost=info'><u>添加分站</u></a>
    &nbsp;&nbsp;
    <a href='city_list.php'><u>查看分站列表</u></a>
    &nbsp;&nbsp;
  ";
    $msg = "<div style=\"line-height:36px;height:36px\">{$msg}</div>";
    $wintitle = "操作成功！";
    $wecome_info = "分站管理::添加/编辑分站";
    $win = new OxWindow();
    $win->AddTitle("操作成功：");
    $win->AddMsgItem($msg);
    $winform = $win->GetWindow("hand","&nbsp;",false);
    $win->Display();
}
