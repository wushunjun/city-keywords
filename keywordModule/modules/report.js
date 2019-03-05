/**

 @Name：layuiAdmin 主页示例
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：GPL-2

 */


layui.define(function (exports) {
    var $ = layui.$
    layui.use('table', function(){
        var table = layui.table;

        table.render({
            elem: '#rank_table'
            ,url:'kw_report_data.php'
            ,cols: [[
                {field:'keyword', width:180, title: '关键词'}
                ,{field:'engines', width:180, title: '搜索引擎', templet: function(res){
                    return '<img style="height: 40px;" src="./keywordModule/image/'+res.engines+'.png">';
                }}
                ,{field:'client', width:180, title: '引擎来源'}
                ,{field:'rank', title: '排名', minWidth: 250}
                ,{field:'source', width:180, title: '主要来源'}
                ,{field:'collect_count', width:180, title: '收录量'}
            ]]
            ,page: true
        });
        $("body").on('click','.layui-form-radio',function(){console.log($('form').serialize());
            var client = $("input[name='client']:checked").val();
            var engines = $("input[name='engines']:checked").val();
            //执行重载
            table.reload('rank_table', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                ,where: {
                    client: client,
                    engines: engines,
                }
            });
        })
    });
});