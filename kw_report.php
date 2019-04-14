<?php
/**
 * 关键词排名查询1
 */
require_once(dirname(__FILE__) . "/config.php");
require_once(DEDEINC . "/datalistcp.class.php");
$row = $dsql->GetOne("Select task_id,status,add_time From `#@__keywords_task` ORDER BY aid DESC limit 1 ; ");
if ($row['status'] == 0) {
    $url1 = 'http://apidata.chinaz.com/batchapi/GetApiData';
    $data1 = [
        'taskid' => $row['task_id'],
    ];
    $post_data1 = json_encode($data1);
    $result = json_decode(request_post($url1, $post_data1), true);
    $status = $result['StateCode'];
    if ($status) {
        $dsql->ExecuteNoneQuery("DELETE FROM `#@__keywords_rank`");
        foreach ($result['Result']['Data'] as $k => $v) {
            foreach ($v as $key => $val) {
                $engines = str_replace("Pc", "", str_replace("Mobile", "", $key));
                if(in_array($engines, ['Baidu','Sogou','Haosou'])){
                    $keyword = $val['Keyword'];
                    $collect_count = $val['Result']['CollectCount'];
                    $client = stripos($key, 'Mobile') !== false ? '移动' : 'PC';
                    $rank_str = '';
                    foreach ($val['Result']['Ranks'] as $item => $value) {
                        $rank = explode('-', $value['RankStr']);
                        $rank_str .= '第' . $rank[0] . '页，第' . $rank[1] . '条；'; 
                        $source = $value['Url'];
                    }
                    $time = time();
                    $query = "INSERT INTO `#@__keywords_rank`(keyword,engines,collect_count,client,source,rank,time) Values('$keyword','$engines','$collect_count','$client','$source','$rank_str','$time')";
                    $dsql->ExecuteNoneQuery($query);
                }
            }
        }
        $dsql->ExecuteNoneQuery("update `#@__keywords_task` set status = 1");
    }
}
$row = $dsql->GetOne("Select task_id,status,add_time From `#@__keywords_task` WHERE status = 1 ORDER BY aid DESC limit 1 ; ");
$row['add_time'] = date('Y-m-d H:i:s',$row['add_time']);
$query_data = "select * from #@__keywords_rank $where order by engines desc";
$dsql->Execute('me',$query_data);
$data = [];
$max = $totle = $before_three = 0;
while($arr = $dsql->GetArray())
{
    switch ($arr['client']) {
        case 'PC':
            $index = 'pc';
            break;
        case '移动':
            $index = 'mobile';
            break;
        
        default:
            break;
    }
    $rank_arr = explode('；', $arr['rank']);
    foreach($rank_arr as $k=>$v){
        if(stripos($v, '第1页') !== false || stripos($v, '第2页') !== false || stripos($v, '第3页') !== false){
            $before_three ++;
        }
    }
    $data[$arr['engines']][$index] += (count($rank_arr) - 1);
    if($data[$arr['engines']][$index] > $max){
        $max = $data[$arr['engines']][$index];
    }
    $data[$index] += $data[$arr['engines']][$index];
    $totle += $data[$arr['engines']][$index];
}
$data['totle'] = $totle;
$data['before_three'] = $before_three;
$data['max'] = str_replace(substr($max,-1),0,$max) + 10;
$data_json = json_encode($data);
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN . "/templets/kw_report.htm");
$dlist->display();
/**
 * 模拟post提交
 */
function request_post($url, $post_data)
{
    $curlPost = $post_data;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($curlPost))
    );

    $result = curl_exec($ch);
    return $result;
}
