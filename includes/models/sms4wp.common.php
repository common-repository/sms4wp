<?php
if (!defined('ABSPATH')) exit;

function sms4wp_query_string() {
    global $_REQUEST;

    $qstr    = '';
    $orderby = '';
    $order   = '';

    if ( isset($_REQUEST['s']) ) {
        $s = htmlspecialchars( trim( $_REQUEST['s'] ) );

        if ( preg_match('~%[0-9A-F]{2}~i', $s) ) // urlencode check
            $s = urlencode( $s );

        $qstr .= '&s=' . $s;
    }
    
    if ( isset($_REQUEST['sf']) ) {
        $sf = htmlspecialchars( trim( $_REQUEST['sf'] ) );

        if ( preg_match('~%[0-9A-F]{2}~i', $sf) ) // urlencode check
            $sf = urlencode( $sf );

        $qstr .= '&sf=' . $sf;
    }
        
    if ( isset($_REQUEST['ss']) ) {
        $ss = htmlspecialchars( trim( $_REQUEST['ss'] ) );

        if ( preg_match('~%[0-9A-F]{2}~i', $ss) ) // urlencode check
            $ss = urlencode( $ss );

        $qstr .= '&ss=' . $ss;
    }
    
    if ( isset($_REQUEST['paged']) ) {
        $paged = htmlspecialchars( trim( $_REQUEST['paged'] ) );

        if ( preg_match('~%[0-9A-F]{2}~i', $paged) ) // urlencode check
            $paged = urlencode( $paged );

        $qstr .= '&paged=' . $paged;
    }
   
    if ( isset($_REQUEST['orderby']) ) {
        $orderby = htmlspecialchars( trim( $_REQUEST['orderby'] ) );

        if ( preg_match('~%[0-9A-F]{2}~i', $orderby) ) // urlencode check
            $orderby = urlencode( $orderby );

        $qstr .= '&orderby=' . $orderby;
    }
   
    if ( isset($_REQUEST['order']) ) {
        $order = htmlspecialchars( trim( $_REQUEST['order'] ) );

        if ( preg_match('~%[0-9A-F]{2}~i', $order) ) // urlencode check
            $order = urlencode( $order );

        $qstr .= '&order=' . $order;
    }

    return $qstr;
}

// 
function sms4wp_order_no_pad( $str, $depth, $len = 3 ) {
    $len = intval( $len );
    $str = str_pad( $str, $len, '0', STR_PAD_LEFT );

    return $str;
}

// 
function sms4wp_trim( $val ) {

    if ( is_array( $val ) ) {
        $res = array();

        foreach ( $val as $key=>$value ) {
            if ( is_array($value) )
                $res[$key] = sms4wp_trim( $value );
            else
                $res[$key] = trim( $value );
        }
    }
    else {
        $res = trim( $val );
    }

    return $res;
}

// 
function sms4wp_htmlspecialchars( $val ) {

    if ( is_array( $val ) ) {
        $res = array();

        foreach ( $val as $key=>$value ) {
            if ( is_array($value) )
                $res[$key] = sms4wp_trim( $value );
            else
                $res[$key] = sanitize_text_field( $value );
        }
    }
    else {
        $res = sanitize_text_field( $val );
    }

    return $res;
}

if ( ! function_exists('sms4wp_get_hp')) {
    function sms4wp_get_hp( $cellphone, $hyphen = 1 ) {
        if ( ! sms4wp_is_hp( $cellphone ) ) 
            return '';

        if ( $hyphen ) 
            $preg = "$1-$2-$3"; 
        else 
            $preg = "$1$2$3";

        $cellphone = preg_replace( "/[^0-9]/i", "", trim($cellphone) );
        $cellphone = preg_replace( "/^(01[016789])([0-9]{3,4})([0-9]{4})$/", $preg, $cellphone );

        return $cellphone;
    }
}
if ( ! function_exists('sms4wp_iconv_euckr')) {
    // CHARSET 변경 : utf-8 -> euc-kr
    function sms4wp_iconv_euckr($str) {
        return iconv('utf-8', 'euc-kr', $str);
    }
}
if ( ! function_exists('sms4wp_utf2euc')) {
    function sms4wp_utf2euc( $str ) {
        return iconv( "UTF-8","cp949//IGNORE", $str );
    }
}
if ( ! function_exists('sms4wp_is_ie')) {
    function sms4wp_is_ie() {
        return isset( $_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false );
    }
}

if ( ! function_exists('sms4wp_is_hp')) {
    function sms4wp_is_hp( $hp ) {
        $hp = preg_replace( "/[^0-9]/i", "", trim($hp) );
        if ( preg_match( "/^(01[016789])([0-9]{3,4})([0-9]{4})$/", $hp ) )
            return true;
        else
            return false;
    }
}

//-- 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL --//
function sms4wp_get_pagination( $write_pages, $cur_page, $total_page, $url, $add="", $starget="" ) {
    if( $starget ) {
        $url = preg_replace('#&amp;'.$starget.'=[0-9]*#', '', $url) . '&amp;'.$starget.'=';
    }
    
    $str = '';
    if ( $cur_page > 1 ) {
        $str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start">처음</a>'.PHP_EOL;
    }

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ( $end_page >= $total_page ) 
        $end_page = $total_page;

    if ( $start_page > 1 ) 
        $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev">이전</a>'.PHP_EOL;

    if ( $total_page > 1 ) {
        for ( $k = $start_page; $k <= $end_page; $k++ ) {
            if ( $cur_page != $k )
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">페이지</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>'.PHP_EOL;
        }
    }

    if ( $total_page > $end_page ) 
        $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next">다음</a>'.PHP_EOL;

    if ( $cur_page < $total_page ) {
        $str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end">맨끝</a>'.PHP_EOL;
    }

    if ( $str )
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}


//-- 페이지 출력 --//
function sms4wp_pagination( $args = array() ) {
    extract( $args );

    $max_num_pages = 0;

    $total        = intval( trim($total) );
    $paged        = intval( trim($paged) );
    $page_rows    = intval( trim($page_rows) );
    $pagenum_link = trim($pagenum_link);

    if ( $total > 0 && $page_rows ) {
        $max_num_pages = ceil( $total / $page_rows );
    }

    if ( $max_num_pages < 2 ) {
        return;
    }

    $paged      = $paged ? intval( $paged ) : 1;
    $query_args = array();
    $url_parts  = explode( '?', $pagenum_link );
    $page_rows  = $page_rows ? intval( $page_rows ) : 20;

    $pagenum_next_disabled = '';
    $pagenum_prev_disabled = '';
    $pagenum_next = 0;

    if ( $paged > 1 ) {
        $pagenum_prev = $paged - 1;
    }
    else {
        $pagenum_prev = $paged;
        $pagenum_prev_disabled = 'disabled';
    }

    if ( $paged < $max_num_pages ) {
        $pagenum_next = $paged + 1;
    }
    else {
        $pagenum_next = $max_num_pages;
        $pagenum_next_disabled = 'disabled';
    }

    $links = '
            <!-- span class="displaying-num">' . $page_rows . ' items</span -->
            <span class="pagination-links"><a class="first-page ' . $pagenum_prev_disabled . '" title="Go to the first page" href="' . $pagenum_link . '&amp;paged=1">«</a>
            <a class="prev-page ' . $pagenum_prev_disabled . '" title="Go to the previous page" href="' . $pagenum_link . '&amp;paged=' . $pagenum_prev . '">‹</a>
            <span class="paging-input"><input class="current-page" title="Current page" type="text" name="paged" value="' . $paged . '" size="1"> of <span class="total-pages">' . $max_num_pages . '</span></span>
            <a class="next-page ' . $pagenum_next_disabled . '" title="Go to the next page" href="' . $pagenum_link . '&amp;paged=' . $pagenum_next . '">›</a>
            <a class="last-page ' . $pagenum_next_disabled . '" title="Go to the last page" href="' . $pagenum_link . '&amp;paged=' . $max_num_pages . '">»</a></span>
    ';

    return $links;
}

if ( ! function_exists('sms4wp_get_esc_attr') ) :
    /**
     * Escaping for HTML attributes.
     * @param  [string] $text    [input text]
     * @param  [string] $default [default text]
     * @return [string]          [Escaping for HTML]
     */
    function sms4wp_get_esc_attr( $text, $default = '' ) {
        if ( empty($text) )
            $text = $default;

        $text = esc_attr( $text );

        return $text;
    }
endif;

if ( ! function_exists('sms4wp_get_select_reply_number') ) :
    /**
     * 기본설정에서 등록한 발신번호 목록을 셀렉트박스로 출력하기
     * @param  [string] $select_name  [셀렉트박스 이름]
     * @param  [string] $option_value [선택된 값]
     * @param  [string] $option_name  [셀렉트박스 최초 옵션명]
     * @return [html]                 [셀렉트박스]
     */
    function sms4wp_get_select_reply_number( $select_name, $option_value = '', $option_name = '' ) {
        global $sms4wp_config;

        $select_name  = sms4wp_get_esc_attr( $select_name, 'reply_number' );
        $option_name  = sms4wp_get_esc_attr( $option_name, '선택하세요.' );

        $selectHtml = '<select name="'. $select_name .'"><option value="">'. $option_name .'</option>'. PHP_EOL;

        for ( $c = 0; $c < count( $sms4wp_config['sms4wp_reply_number'] ); $c++ ) { 
            if ( $sms4wp_config['sms4wp_reply_use'][$c] == 200 || $sms4wp_config['sms4wp_reply_use'][$c] == 500 ) {
                $selected = '';
                if ( $option_value == $sms4wp_config['sms4wp_reply_number'][$c] )
                    $selected = 'selected';
                $selectHtml .= '<option value="'. $sms4wp_config['sms4wp_reply_number'][$c] .'" '. $selected .'>'. $sms4wp_config['sms4wp_reply_number'][$c]. '</value>'. PHP_EOL;
            }
        }

        $selectHtml .= '</select>'. PHP_EOL;

        return $selectHtml;
    }
endif;
