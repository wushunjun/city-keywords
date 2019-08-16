<?php
/**
 * 城市管理
 */
require_once(dirname(__FILE__) . "/config.php");
require_once(DEDEINC . "/datalistcp.class.php");
if(empty($dopost)) $dopost = '';
if($dopost=='ajax'){
    $start = ($page - 1) * $limit;
    $query_count = "select * from #@__city_site";
    $query_data = "select * from #@__city_site limit $start,$limit";
    $dsql->Execute('count',$query_count);
    $count = $dsql->GetTotalRow($rsid="count");
    $dsql->Execute('me',$query_data);
    $data = [];
    while($arr = $dsql->GetArray())
    {
        $data[] = $arr;
    }
    echo json_encode(['code' => 0, 'msg' => '', 'data' => $data, 'count' => $count]);
}else{
    $query = "select * from #@__city_site";
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN."/templets/city_list.htm");
    $dlist->display();
}
