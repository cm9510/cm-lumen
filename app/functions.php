<?php

if(!function_exists('biz_size')){
    /**
     * 计算文件大小（byte）
     * @param int $size
     * @return string
     */
    function biz_size(int $size) {
        if(floor($size/1048576) > 0){ // M
            return round($size/1024/1024,1).'M';
        }elseif (floor($size/1024) > 0){ // K
            return round($size/1024,1).'k';
        }
        return $size.'b';
    }
}
