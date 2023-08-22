<?php

if(!function_exists('biz_size')){
    /**
     * 计算文件大小（byte）
     * @param int $size
     * @return string
     */
    function biz_size(int $size):string {
        if(floor($size/1048576) > 0){ // M
            return round($size/1024/1024,1).'M';
        }elseif (floor($size/1024) > 0){ // K
            return round($size/1024,1).'k';
        }
        return $size.'b';
    }
}

if (!function_exists('write_member_log')){

    function write_member_log(int $aid,string $title, string $detail){

    }
}
