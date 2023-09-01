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

if (!function_exists('hide_phone')){
    /**
     * 掩盖手机号
     * @param string $phone
     * @return string
     */
    function hide_phone(string $phone):string{
        if (strlen($phone) != 11){
            return $phone;
        }
        return substr($phone,0,3).'****'.substr($phone,-4);
    }
}
