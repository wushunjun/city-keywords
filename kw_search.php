<?php
/**
 * 关键词排名查询1
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
//获取站长key和域名
$row = $dsql->GetOne("Select value From `#@__sysconfig` where varname='zz_key'; ");
$row1 = $dsql->GetOne("Select value From `#@__sysconfig` where varname='domain_name'; ");
$zz_key =  $row['value'];
$domain_name =  $row1['value'];
if(!$zz_key){
    ShowMsg("请先设置站长工具接口key！","-1");
    exit();
}
if(!$domain_name){
    ShowMsg("请先设置好要查询的域名！","-1");
    exit();
}
if(empty($dopost)) $dopost = '';
if($dopost=='save'){
    $old_task = $dsql->GetOne("Select task_id,status,add_time From `#@__keywords_task` ORDER BY aid DESC limit 1 ; ");
    if($old_task['add_time'] - time() < 7*24*3600){
        echo -1;
        exit();
    }
    $keywords = implode('|',$keywords);
    $url = 'http://apidata.chinaz.com/BatchAPI/AllRanking';
    $data = [
        'key' => $zz_key,
        'domainName' => $domain_name,
        'keywords' => $keywords,
    ];
    $post_data = json_encode($data);
    $res  = json_decode(request_post($url,$post_data),true);
    if($res['StateCode'] == 1){
        $task_id = $res['TaskID'];
        $time = time();
        $query = "INSERT INTO `#@__keywords_task`(task_id,add_time) Values('$task_id','$time')";
        $dsql->ExecuteNoneQuery($query);
        /*$url1 = 'http://apidata.chinaz.com/batchapi/GetApiData';
        $data1 = [
            'taskid' => $res['TaskID'],
        ];
        $post_data1 = json_encode($data1);
        $status = 0;
        while($status == 0){
            $result  = json_decode(request_post($url1,$post_data1),true);
            $status = $result['StateCode'];
        }
        $dsql->ExecuteNoneQuery("DELETE FROM `#@__keywords_rank`");
        foreach($result['Result']['Data'] as $k=>$v){
            foreach($v as $key=>$val){
                $keyword = $val['Keyword'];
                $engines = str_replace("Pc","",str_replace("Mobile","",$key));
                $collect_count = $val['Result']['CollectCount'];
                $client = stripos($key, 'Mobile') !== false ? '移动' : 'PC';
                $rank_str = '';
                foreach($val['Result']['Ranks'] as $item=>$value){
                    $rank = explode('-',$value['RankStr']);
                    $rank_str .= '第'.$rank[0].'页，第'.$rank[1].'条；';
                    $source = $value['Url'];
                }
                $time = time();
                $query = "INSERT INTO `#@__keywords_rank`(keyword,engines,collect_count,client,source,rank,time) Values('$keyword','$engines','$collect_count','$client','$source','$rank_str','$time')";
                $dsql->ExecuteNoneQuery($query);
            }
        }*/
        echo 1;exit();
    }else{
        echo 0;exit;
    }
}else{
    $query = "select * from #@__keywords_rank group by keyword";
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN."/templets/kw_search.htm");
    $dlist->SetSource($query);
    $dlist->display();
}
/**
 * 模拟post提交
 */
function request_post($url, $post_data) {
    $curlPost = $post_data;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$curlPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($curlPost))
    );

    $result = curl_exec($ch);
    return $result;
}
/*$url = 'http://apidata.chinaz.com/BatchAPI/AllRanking';
$data = [
    'key' => 'fdd7ba00833d425cb845a44ea7b55f50',
    'domainName' => 'caixiansheng365.com',
    'keywords' => '菜鲜生|菜鲜生365',
];
$post_data = json_encode($data);
$res  = request_post($url,$post_data);


$url1 = 'http://apidata.chinaz.com/batchapi/GetApiData';
$data1 = [
    'taskid' => '633c2a5813be46dd9c6edd5d',
];
$post_data1 = json_encode($data1);
$res  = request_post($url1,$post_data1);echo $res;*/