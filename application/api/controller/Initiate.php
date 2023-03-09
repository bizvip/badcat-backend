<?php

namespace app\api\controller;

use app\common\controller\Api;
// use think\Request;
use think\Db;
use think\Config;

class Initiate extends Api
{
    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    // protected $noNeedLogin = [];
    // 无需鉴权的接口,*表示全部
    // protected $noNeedRight = '*';
    
    protected $noNeedLogin = ['*'];
    
    public function request_to_send()
    {
         $termsinfo = [
            // 禁止登录
            'login_allow'         => Config::get("site.allow_login"),
            // 禁止注册
            'registration_allow'         => Config::get("site.allow_registration"),
            // 压屏公告开关
            'Announcement_open'         => Config::get("site.annou_switch"),
            // 压屏公告富文本
            'Announcement_content'      => Config::get("site.annou_text"), 
            // 压屏窗A开关
            'pressing_screen_switch'    => Config::get("site.pressing_switch"),
            // 压屏窗B开关
            'presbing_screen_switch'    => Config::get("site.presbing_switch"),
            // 在线客服链接
            'online_service'      => Config::get("site.chat"), 
            // 大嘴鸟播放音乐
            'toucan_music'      => Config::get("site.big_bird_music"),
            // 允许吃瓜投稿
            'water_allow_release'      => Config::get("site.water_btn_switch"),
            // 允许图片投稿
            'photo_allow_release'      => Config::get("site.photo_btn_switch"),
            // 允许短文投稿
            'duanwen_allow_release'      => Config::get("site.duanwen_btn_switch"),
            // 允许问答投稿
            'wenda_allow_release'      => Config::get("site.wenda_btn_switch"),
            // 吃瓜投稿底部提示	
            'water_page_bottom_tip'      => Config::get("site.water_page_tips"),
            // 发布图片底部提示	
            'photo_page_bottom_tip'      => Config::get("site.photo_page_tips"),
            // 发布短文底部提示	
            'duanwen_page_bottom_tip'      => Config::get("site.duanwen_page_tips"),
            // 发布问答底部提示	
            'wenda_page_bottom_tip'      => Config::get("site.wenda_page_tips"),
            // 吃瓜专区主界面轮播图
            'water_banner_page'      => Config::get("site.page_banner_switch"),
            // 吃瓜专区主界面时间显示类型
            'water_data_type'      => Config::get("site.water_data_display"),
            // 吃瓜专区详情页时间显示类型
            'water_details_data_type'      => Config::get("site.water_details_data_display"),
            // 吃瓜专区收费方式
            'water_of_type'      => Config::get("site.type_of_water"),
            // 吃瓜详情页轮播广告
            'water_details_banner_switch'      => Config::get("site.water_details_banner"),
            // 图片专区主界面时间显示类型
            'photo_data_type'      => Config::get("site.photo_data_display"),
            // 图片专区详情页时间显示类型
            'photo_details_data_type'      => Config::get("site.photo_details_data_display"),
            // 图片专区收费方式
            'photo_of_type'      => Config::get("site.type_of_pictures"),
            // 图片专区图片上显示数量
            'photo_of_number'      => Config::get("site.number_of_pictures"),
            // 图片专区顶部通告
            'photo_notice'      => Config::get("site.notice_of_pictures"),
            // 图片专区顶部通告开关
            'photo_notice_switch'      => Config::get("site.notice_of_pictures_switch"),
            // 图片详情页轮播广告
            'photo_details_banner_switch'      => Config::get("site.photo_details_banner"),
            // 短文专区主界面时间显示类型
            'duanwen_data_type'      => Config::get("site.duanwen_data_display"),
            // 短文专区详情页时间显示类型
            'duanwen_details_data_type'      => Config::get("site.duanwen_details_data_display"),
            // 短文详情页轮播广告
            'duanwen_details_banner_switch'      => Config::get("site.duanwen_details_banner"),
            // 问答专区主界面时间显示类型
            'wenda_data_type'      => Config::get("site.wenda_data_display"),
            // 问答专区详情页时间显示类型
            'wenda_details_data_type'      => Config::get("site.wenda_details_data_display"),
            // 问答详情页轮播广告
            'wenda_details_banner_switch'      => Config::get("site.wenda_details_banner"),
            // ASMR专区收费方式
            'asmr_of_type'      => Config::get("site.type_of_asmr"),
            // 短视频界面预览视频
            'short_video_vurl'      => Config::get("site.myopic_video_previewing"),
            // 短视频状态
            'short_video_status'      => Config::get("site.myopic_state"),
            // 短视频收费方式
            'short_video_of_type'      => Config::get("site.type_of_myopic"),
            // 注册页面提示文案
            'reg_text'      => Config::get("site.register_bot_text"),
            // 未登录用户背景图
            'not_image'      => Config::get("site.not_logged_in_img"),
            // 未登录用户头像
            'not_avatar'      => Config::get("site.not_logged_in_avatar"),
            // 分享链接
            'fxlj'      => Config::get("site.fxlj"),
            // 60秒看世界 
            'look'      => Config::get("site.lookword"),
            // 应用是否全部免费
            'free_choice'      => Config::get("site.down_mode"), 
            // 应用下载方式
            'download_able'      => Config::get("site.down_method"), 
            // 非会员下载弹窗提示内容
            'notification_tips'      => Config::get("site.ordinary_tips"), 
            // 未登录下载弹窗提示内容
            'not_logged_in_tips'      => Config::get("site.sigin_tips"), 
            // 预览视频上方图片
            'display_diagram'      => Config::get("site.detailed_picture"), 
            // 预览视频上方图片展示
            'display_diagram_switch'      => Config::get("site.detailed_picture_switch"), 
            // 预览视频是否展示
            'previewing_video_switch'      => Config::get("site.video_exhibition"), 
            // 预览视频展示对象
            'previewing_video'      => Config::get("site.video_exhibition_object"), 
            // 更新时间显示类型
            'uptime_display'      => Config::get("site.publish_display"),
            // 首页影片列表样式
            'movie_list_type'      => Config::get("site.movie_list_style"),
            // 余页影片列表样式
            'movie_rest_list_type'      => Config::get("site.movie_rest_list_style"),
            // 影视界面通告
            'movie_gg_notice'      => Config::get("site.movie_page_notice"),
            // 影视界面通告开关
            'movie_gg_switch'      => Config::get("site.movie_list_notice_switch"),
            // 播放页面通告
            'movie_gg'      => Config::get("site.movie_notice"),
            // 播放页面通告开关
            'movie_switch'      => Config::get("site.movie_notice_switch"),
            // 影视收费方式
            'movie_of_type'      => Config::get("site.type_of_movie"),
            // 直播界面轮播图开关
            'live_banner_switch'      => Config::get("site.live_switch_banner"),
            // 直播间游戏开关
            'live_room_game_switch'      => Config::get("site.live_game_btn_switch"),
            // 直播间礼物开关
            'live_room_gift_switch'      => Config::get("site.live_gift_btn_switch"),
            // 进入/离开直播间滚动方式
            'scroll_mode'      => Config::get("site.scroll_mode_type"),
            // 主播认证弹窗
            'anchor_tips'      => Config::get("site.player_tips"),
            // 广告栏标题
            'ad_outer_frame_title'      => Config::get("site.ad_column_title"),
            // 广告栏副标题
            'ad_outer_frame_subheading'      => Config::get("site.ad_column_subheading"),
            // 广告栏LOGO
            'ad_outer_frame_img'      => Config::get("site.ad_column_img"),
            // 支付确认弹窗
            'payment_confirmation'      => Config::get("site.pay_confirm_tog"),
            // 支付阅读倒计时
            'pay_reading_countdown'      => Config::get("site.pay_countdown_reading"),
            // 分享链接
            'extension_url'      => Config::get("site.extension_share_url"),
            // 分享背景图片
            'extension_img'      => Config::get("site.extension_share_img"),
            // 分享LOGO
            'extension_logo'      => Config::get("site.extension_share_logo"),
            // 分享二维码
            'extension_qr'      => Config::get("site.extension_share_qr_code"),
        ];
        $this->success("success",$termsinfo);
    }
     
}
