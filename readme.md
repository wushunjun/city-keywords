## 自定义标签说明
* {city}：分站城市名称，用于需要体现不同城市分站名称的地方
* {banner_id}：替换轮播栏目id
* {product_id}：替换产品栏目id
* {news_id}：替换新闻栏目id
## 分站列表输出
```
{dede:loop table='dede_city_site' sort='' row='50' if=''}
 <a href="/city/[field:pinyin/].html">[field:name/]</a>
{/dede:loop}
```