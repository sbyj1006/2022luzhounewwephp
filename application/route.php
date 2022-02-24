<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +-------------------------------------------------------------------

use think\Route;
//// 注册路由到index模块的News控制器的read操作

Route::rule('/','index');

Route::rule('abouts','Index/About/index');
Route::rule('about/:id','Index/About/index');
Route::rule('teams_info/:id','Index/About/teams_info');

Route::rule('newsc','Index/News/index');//
Route::rule('news/:id','Index/News/index');//
Route::rule('news_detail/:id','Index/News/news_detail');

Route::rule('jishu','Index/Jishu/index');//
Route::rule('jishus/:id','Index/Jishu/index');//


Route::rule('teams','Index/Team/index');//
Route::rule('team/:id','Index/Team/team_list');//
Route::rule('team_detail/:id','Index/Team/team_detail');


Route::rule('cpzx','Index/Cpzx/index');//
Route::rule('cpzxs/:id','Index/Cpzx/index');//
Route::rule('cpzxs_detail/:id','Index/Cpzx/cpzxs_detail');

Route::rule('fuli','Index/Fuli/index');//


Route::rule('joinus','Index/Join/index');//
Route::rule('join/:id','Index/Join/index');//

Route::rule('contact','Index/Contact/index');//



