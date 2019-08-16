/**

 @Name：layuiAdmin 主页示例
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：GPL-2

 */


layui.define(function (exports) {
    var $ = layui.$
    layui.use('table', function () {
        var table = layui.table;

        table.render({
            elem: '#rank_table'
            , url: 'kw_report_data.php'
            , cols: [[
                {field: 'keyword', width: 180, title: '关键词'}
                , {
                    field: 'engines', width: 180, title: '搜索引擎', templet: function (res) {
                        return '<img style="height: 40px;" src="./keywordModule/image/' + res.engines + '.png">';
                    }
                }
                , {field: 'client', width: 180, title: '引擎来源'}
                , {field: 'rank', title: '排名', minWidth: 250}
                , {field: 'source', width: 180, title: '主要来源'}
                //, {field: 'collect_count', width: 180, title: '收录量'}
                , {
                    field: 'engines', width: 180, title: '操作', templet: function (res) {
                        return '<a href="' + res.url + '" target="_blank" style="color: #3366FF">查看结果</a>';
                    }
                }
            ]]
            , page: true
            , limit : 30
        });
        $("body").on('click', '.layui-form-radio', function () {
            console.log($('form').serialize());
            var client = $("input[name='client']:checked").val();
            var engines = $("input[name='engines']:checked").val();
            //执行重载
            table.reload('rank_table', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                , where: {
                    client: client,
                    engines: engines,
                }
            });
        })
    });
    //区块轮播切换
    layui.use(['admin', 'carousel'], function () {
        var $ = layui.$
            , admin = layui.admin
            , carousel = layui.carousel
            , element = layui.element
            , device = layui.device();

        //轮播切换
        $('.layadmin-carousel').each(function () {
            var othis = $(this);
            carousel.render({
                elem: this
                , width: '100%'
                , arrow: 'none'
                , interval: othis.data('interval')
                , autoplay: othis.data('autoplay') === true
                , trigger: (device.ios || device.android) ? 'click' : 'hover'
                , anim: othis.data('anim')
            });
        });

        element.render('progress');

    });
    //图表
    layui.use(['carousel', 'echarts'], function () {
        var $ = layui.$
            , carousel = layui.carousel
            , echarts = layui.echarts,
            data_json = eval('(' + $("#data_json").val() + ')');
            console.log(data_json)
        var echartsApp = [], options = [
            {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    x: 'center',
                    y: 'bottom',
                    data: ['上线情况']
                },
                polar: [
                    {
                        indicator: [
                            {text: '百度PC'+(typeof data_json['Baidu']['pc'] != 'undefined' ? data_json['Baidu']['pc'] + '个' : '等待数据返回'), max: data_json['max']},
                            {text: '360PC'+(typeof data_json['Haosou']['pc'] != 'undefined' ? data_json['Haosou']['pc'] + '个' : '等待数据返回'), max: data_json['max']},
                            {text: '搜狗PC'+(typeof data_json['Sogou']['pc'] != 'undefined' ? data_json['Sogou']['pc'] + '个' : '等待数据返回'), max: data_json['max']},
                            {text: '百度移动'+(typeof data_json['Baidu']['mobile'] != 'undefined' ? data_json['Baidu']['mobile'] + '个' : '等待数据返回'), max: data_json['max']},
                            {text: '360移动'+(typeof data_json['Haosou']['mobile'] != 'undefined' ? data_json['Haosou']['mobile'] + '个' : '等待数据返回'), max: data_json['max']},
                            {text: '搜狗移动'+(typeof data_json['Sogou']['mobile'] != 'undefined' ? data_json['Sogou']['mobile'] + '个' : '等待数据返回'), max: data_json['max']}
                        ],
                        radius: "66%"
                    }
                ],
                series: [
                    {
                        type: 'radar',
                        center: ['50%', '50%'],
                        itemStyle: {
                            normal: {
                                areaStyle: {
                                    type: 'default'
                                }
                            }
                        },
                        data: [
                            {value: [data_json['Baidu']['pc'], data_json['Haosou']['pc'], data_json['Sogou']['pc'], data_json['Baidu']['mobile'], data_json['Haosou']['mobile'], data_json['Sogou']['mobile']], name: '上线情况'},
                            //{value: [data_json['Baidu']['pc'], 0, data_json['Sogou']['pc'], 80, 30, 50], name: '上线情况'},
                        ]
                    }
                ]
            }
        ]
            , elemDataView = $('#LAY-index-pageone').children('div')
            , renderDataView = function (index) {
            echartsApp[index] = echarts.init(elemDataView[index], layui.echartsTheme);
            echartsApp[index].setOption(options[index]);
            window.onresize = echartsApp[index].resize;
        };
        //没找到DOM，终止执行
        if (!elemDataView[0]) return;

        renderDataView(0);
    });
    layui.use(['carousel', 'echarts'], function () {
        var $ = layui.$
            , carousel = layui.carousel
            , echarts = layui.echarts,
            data_json = eval('(' + $("#data_json").val() + ')');
        var echartsApp = [], options = [
            {
                tooltip: {
                    trigger: 'item',
                },
                legend: {
                    orient: 'horizontal', // 'vertical'
                    icon: 'pie',
                    // orient: 'vertical',
                    x: 'center',
                    y: 'bottom',
                    selectedMode: true,
                    data: ['PC端'+data_json['pc'], '移动端'+data_json['mobile']]
                },
                series: [
                    {
                        name: '关键词上线情况',
                        center: ['50%', '50%'],
                        type: 'pie',
                        radius: ['50%', '65%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center',
                            },
                            emphasis: {
                                show: true,
                                textStyle: {
                                    fontSize: '20',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: [
                            {value: data_json['mobile'], name: '移动端'+data_json['mobile']},
                            {value: data_json['pc'], name: 'PC端'+data_json['pc']},
                        ]
                    }
                ]
            }
        ]
            , elemDataView = $('#LAY-index-pageone1').children('div')
            , renderDataView = function (index) {
            echartsApp[index] = echarts.init(elemDataView[index], layui.echartsTheme);
            echartsApp[index].setOption(options[index]);
            window.onresize = echartsApp[index].resize;
        };
        //没找到DOM，终止执行
        if (!elemDataView[0]) return;

        renderDataView(0);
    });
    exports('report', {})
});