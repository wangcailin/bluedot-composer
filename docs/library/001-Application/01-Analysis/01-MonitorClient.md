## 用户行为监测

> 维护人员：**Kang**  
> 创建时间：2022-03-29

### 接口简介

创建用户行为记录

### Method

&emsp;bool&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;create()&nbsp;<font color="#888">【添加一条用户行为记录】</font>

---

### Detail

<!-- <table><tr><td bgcolor=#ccc>void&emsp;<b>create()</b>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td></tr></table> -->

```
create():bool

```

添加一条用户行为记录

---

##### Parameters

| 参数名           | 类型   | 必填 | 描述                                      | 参考值  |
| :--------------- | :----- | :--- | :---------------------------------------- | ------- |
| type             | int    | 是   | 类型：1=H5,2=微信公众号,3=小程序          | -       |
| user_id          | int    | 否   | -                                         | -       |
| unionid          | string | 否   | -                                         | -       |
| openid           | string | 否   | -                                         | -       |
| page_title       | string | 否   | -                                         | -       |
| keywords         | int[]  | 否   | 标签 ID                                   | [1,2,3] |
| page_description | string | 否   | -                                         | -       |
| page_url         | string | 否   | 页面 url                                  | -       |
| page_referer_url | string | 否   | 页面 referer url                          | -       |
| page_param       | json   | 否   | 页面参数                                  | -       |
| page_event_key   | string | 否   | 页面点击按钮名称                          | -       |
| page_event_type  | string | 否   | 事件类型：view=浏览,click=点击,share=分享 | -       |
| wechat_user_name | string | 否   | 公众号 user_name                          | -       |
| wechat_appid     | string | 否   | 公众号 appid                              | -       |

##### Return Value

---

&emsp;bool

<!-- ##### 返回正确 JSON 示例

```javascript
{
    "state": {
        "code": 10200,
        "msg": "ok"
    },
    "data": {
        "id": 307,  //流水id
        "real_name": "Tevin",  //用户名称
        "mobile": "暂无",  //用户手机
        "origin": "暂无",  //用户来源
        "created_at": "2016-04-04 20:00:00",  //加入时间
        "last_login": "2016-05-22 15:30:21",  //最后登录时间
        "log": []  //日志列表
    }
}
```

##### 返回错误 JSON 示例

```javascript
{
    "state": {
        "code": 10500
        "msg": "服务器未知报错"
    }
}
``` -->
