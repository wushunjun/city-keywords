<?php
/**
 * 关键词排名查询1
 */
require_once(dirname(__FILE__) . "/config.php");
require_once(DEDEINC . "/datalistcp.class.php");
$dsql->Execute('me',"Select task_id,status,add_time,`type` From `#@__keywords_task` WHERE `status` = 0 ORDER BY aid DESC; ");
$data = [];
while($arr = $dsql->GetArray())
{
    $row[] = $arr;
}
$type_arr = [
    1=>['type'=>1,'url'=>'https://www.baidu.com/s?ie=utf-8&tn=baidu&','name'=>'BaiduPc','qname'=>'wd','pname'=>'pn'],
    2=>['type'=>2,'url'=>'https://m.baidu.com/s?ie=utf-8&tn=baidu&','name'=>'BaiduMobile','qname'=>'wd','pname'=>'pn'],
    3=>['type'=>3,'url'=>'https://www.so.com/s?','name'=>'HaosouPc','qname'=>'q','pname'=>'pn'],
    4=>['type'=>4,'url'=>'https://m.so.com/s?','name'=>'HaosouMobile','qname'=>'q','pname'=>'pn'],
    5=>['type'=>5,'url'=>'https://www.sogou.com/sogou?ie=utf8','name'=>'SogouPc','qname'=>'query','pname'=>'page'],
    6=>['type'=>6,'url'=>'https://m.sogou.com/sogou?ie=utf8','name'=>'SogouMobile','qname'=>'keyword','pname'=>'page'],
];
foreach($row as $k=>$v){
    $data1 = [
        'taskid' => $v['task_id'],
    ];
    $post_data1 = json_encode($data1);
    $result = json_decode(request_post('http://apidata.chinaz.com/batchapi/GetApiData', $post_data1), true);
    //echo json_encode($result['Result']['Data']);die;
    $status = $result['StateCode'];
    if ($status) {
        $type = $v['type'];
        //$dsql->ExecuteNoneQuery("DELETE FROM `#@__keywords_rank` WHERE `type` = $type");
        $name = $type_arr[$v['type']]['name'];
        foreach ($result['Result']['Data'] as $key => $val) {
            $engines = str_replace("Pc", "", str_replace("Mobile", "", $name));
            if(in_array($engines, ['Baidu','Sogou','Haosou'])){
                $keyword = $val['Keyword'];
                $collect_count = $val['Result']['CollectCount'];
                $client = stripos($name, 'Mobile') !== false ? '移动' : 'PC';
                $rank_str = '';
                $page = 0;
                foreach ($val['Result']['Ranks'] as $item => $value) {
                    $rank = explode('-', $value['RankStr']);
                    $rank_str .= '第' . $rank[0] . '页，第' . $rank[1] . '条；';
                    $source = $value['Url'];
                    if($item == 0){
                        $page = $rank[0];
                    }
                }
                $url = '';
                $type_obj = $type_arr[$type];
                switch($type){
                    case 1:
                        $url = $type_obj['url'] . '&' . $type_obj['qname'] .'=' . $keyword . '&' . $type_obj['pname'] . '=' . ($page - 1) * 10;
                        break;
                    case 2:
                        $url = $type_obj['url'] . '&' . $type_obj['qname'] .'=' . $keyword . '&' . $type_obj['pname'] . '=' . ($page - 1) * 10;
                        break;
                    default:
                        $url = $type_obj['url'] . '&' . $type_obj['qname'] .'=' . $keyword . '&' . $type_obj['pname'] . '=' . $page;
                        break;
                }
                $time = time();
                $query = "INSERT INTO `#@__keywords_rank`(keyword,engines,collect_count,client,source,rank,time,url,`type`) Values('$keyword','$engines','$collect_count','$client','$source','$rank_str','$time','$url','$type')";
                $dsql->ExecuteNoneQuery($query);
            }
        }
        $dsql->ExecuteNoneQuery("update `#@__keywords_task` set status = 1 WHERE `type` = $type");
    }
}
$row = $dsql->GetOne("Select task_id,status,add_time From `#@__keywords_task` WHERE status = 1 ORDER BY aid DESC limit 1 ; ");
$row['add_time'] = date('Y-m-d H:i:s',$row['add_time']);
$query_data = "select * from #@__keywords_rank $where order by engines desc";
$dsql->Execute('me',$query_data);
/*$data = [
    'Baidu' => ['pc' => 0,'mobile' => 0],
    'Sogou' => ['pc' => 0,'mobile' => 0],
    'Haosou' => ['pc' => 0,'mobile' => 0],
];*/
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
    $keywords_count = (count($rank_arr) - 1);
    $data[$arr['engines']][$index] += $keywords_count;
    if($data[$arr['engines']][$index] > $max){
        $max = $data[$arr['engines']][$index];
    }
    $data[$index] += $keywords_count;
    $totle += $keywords_count;
}
$data['totle'] = $totle;
$data['before_three'] = $before_three;
//$data['max'] = str_replace(substr($max,-1),0,$max) + 10;
$data['max'] = ceil($max/10) * 10 + 10;
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
