<?php


namespace Nouralhadi\Stemmer\Http\Helpers;


class ISRIStemmer {
    // Prefixes
    private $p3 = [ 'وال', 'ولل', 'بال', 'كال'];
    private $p2 = ['ال', 'لل'];
    private $p1 = ['ل', 'ب', 'ف', 'س', 'و', 'ي', 'ت', 'ن', 'ا'];

    // Suffixes
    private $s3 = ['تمل', 'همل', 'تان', 'تين', 'كمل'];
    private $s2 = ['ون','ات','ان','ين','تن','كم','هن','نا','يا','ها','تم','كن','ني','وا','ما','هم'];
    private $s1 = ['ة', 'ه', 'ي', 'ك', 'ت', 'ا', 'ن'];

    // Patterns
    private $pr4 = [
        0 => ['م'],
        1 => ['ا'],
        2 => ['ا', 'و', 'ي'],
        3 => ['ة'],
    ];

    private $pr53 = [
        0 => ['ا', 'ت'],
        1 => ['ا', 'ي', 'و'],
        2 => ['ا', 'ت', 'م'],
        3 => ['م', 'ي', 'ت'],
        4 => ['م', 'ت'],
        5 => ['ا', 'و'],
        6 => ['ا', 'م']
    ];



    public function test(){
        $word = 'أأأأ';
        foreach ($this->re_initial_hamza as $hamza){
            $word = $this->mb_str_replace($hamza,'ا',$word);
        }

        return $word;
    }

    // Hamza and Tashkeel
    private $re_short_vowels = ['ً' , ' ٌ' ,'ٍ' ,'َ' ,'ُ' ,'ِ' ,'ّ' ,'ْ'];
    private $re_initial_hamza = ['إ','آ', 'ئ', 'ؤ', 'ء'];

    // Stop Words
    private $stop_words = [
        'يكون','وليس','وكان','كذلك','التي','وبين','عليها','مساء','الذي','وكانت','ولكن','والتي','تكون','اليوم','اللذين','عليه','كانت','لذلك','أمام','هناك','منها','مازال','لازال','لايزال','مايزال','اصبح','أصبح','أمسى','امسى','أضحى','اضحى','مابرح','مافتئ','ماانفك','لاسيما','ولايزال','الحالي','اليها','الذين','فانه','والذي','وهذا','لهذا','فكان','ستكون','اليه','يمكن','بهذا','الذى', 'الله'
    ];



    // Stemming a word token using the ISRI stemmer.
    public function stem($token){

        // Remove Tashkeel
        $token = $this->norm($token, 1);

        // Return Stop Words
        if (in_array($token,$this->stop_words)){
            return $token;// . '<br />' . $log;
        }

        // Remove length 3 and length 2 prefix / suffix (Order 3->2)
        $token = $this->pre32($token);
        $token = $this->suf32($token);

        // remove connective ‘و’ if it precedes a word beginning with ‘و’
        $token = $this->waw($token);

        // Replace Hamza With Alef Mamdoda
        $token = $this->norm($token, 2);

        // if 4 <= word length <= 7, then stem; otherwise, no stemming unless starting with Al-Ta3reef
        if (mb_strlen($token) > 7){
            $token = $this->reduce_the($token);
        }
        if (mb_strlen($token) == 4){
            $token = $this->pro_w4($token);
        }
        else if (mb_strlen($token) == 5){
            $token = $this->pro_w53($token);
            $token = $this->end_w5($token);
        }
        else if (mb_strlen($token) == 6){
            $token = $this->pro_w6($token);
            $token = $this->end_w6($token);
        }
        else if (mb_strlen($token) == 7){
            $token = $this->suf1($token);
            if (mb_strlen($token) == 7){
                $token = $this->pre1($token);
            }
            if (mb_strlen($token) == 6){
                $token = $this->pro_w6($token);
                $token = $this->end_w6($token);
            }
        }
        return $this->reduce_doubles($token);
    }

    // Normalize A Word
    public function norm($word, $num = 3){
        if ($num == 1){
            $word = $this->mb_str_replace($this->re_short_vowels,'',$word);
        }
        else if ($num == 2){
            foreach ($this->re_initial_hamza as $hamza){
                $word = $this->mb_str_replace($hamza,'أ',$word);
            }
        }
        else if ($num == 3){
            $word = $this->mb_str_replace($this->re_short_vowels,'',$word);
            foreach ($this->re_initial_hamza as $hamza){
                $word = $this->mb_str_replace($hamza,'ا',$word);
            }
        }
        return $word;
    }

    // remove length three and length two prefixes in this order
    public function pre32($word){
        if (mb_strlen($word) >= 6){
            foreach($this->p3 as $pre3){
                if (mb_substr($word,0,3) == $pre3){
                    return mb_substr($word,3);
                }
            }

        }
        if (mb_strlen($word) >= 5){
            foreach ($this->p2 as $pre2){
                if (mb_substr($word,0,2) == $pre2){
                    return mb_substr($word,2);
                }
            }
        }
        return $word;
    }

    // remove length three and length two suffixes in this order
    public function suf32($word){

        if (mb_strlen($word) >= 6){
            foreach ($this->s3 as $suf3){
                if (!mb_strrpos($word, $suf3)) continue;
                $idx = mb_strrpos($word,$suf3);
                if ( mb_strlen($suf3) + $idx == mb_strlen($word) ) {
                    return mb_substr($word,0,mb_strlen($word)-3);
                }
            }
        }
        if (mb_strlen($word) >= 5){
            foreach ($this->s2 as $suf2){
                if (!mb_strrpos($word, $suf2)) continue;
                $idx = mb_strrpos($word,$suf2);
                if ( mb_strlen($suf2) + $idx == mb_strlen($word) ) {
                    return mb_substr($word,0,mb_strlen($word)-2);
                }
            }
        }
        return $word;
    }

    // remove connective ‘و’ if it precedes a word beginning with ‘و’
    function waw($word){
        if (mb_strlen($word) >= 4 && mb_substr($word,0,2) == 'وو'){
            $word = mb_substr($word,1);
        }
        return $word;
    }


    // process length four patterns and extract length three roots
    function pro_w4($word){
        /* مفعل */
        if (in_array($word[0].$word[1], $this->pr4[0])){
            $word = mb_substr($word,1);
        }

        /* فاعل */
        else if (in_array($word[2].$word[3], $this->pr4[1])){
            $word = $this->remove_char($word,1);
        }

        /* فعال - فعول - فعيل */
        else if (in_array($word[4].$word[5], $this->pr4[2])){
            $word = $this->remove_char($word,2);
        }

        /* فعلة */
        else if (in_array($word[6].$word[7], $this->pr4[3])){
            $word = mb_substr($word,0,mb_strlen($word)-1);
        }
        /* أفعل */
        else if ($word[0].$word[1] == 'أ'){
            $word = $this->generate_from_indexes($word,1,2,3);
        }
        else{
            $word = $this->suf1($word);
            if (mb_strlen($word) == 4){
                $word = $this->pre1($word);
            }

        }
        return $word;

    }

    // process length five patterns and extract length three roots
    function pro_w53($word){
        /* افتعل - افاعل */
        if (in_array($word[4].$word[5], $this->pr53[0]) &&  $word[0].$word[1] == 'ا'){
            $word = $this->generate_from_indexes($word,1,3,4);
        }
        /* مفعول - مفعال - مفعيل */
        else if (in_array($word[6].$word[7], $this->pr53[1]) && $word[0].$word[1] == 'م'){
            $word = $this->generate_from_indexes($word,1,2,4);
        }
        /* مفعلة - تفعلة - افعلة */
        else if (in_array($word[0].$word[1], $this->pr53[2]) && $word[8].$word[9] == 'ة'){
            $word = $this->generate_from_indexes($word,1,2,3);
        }
        /* مفتعل - يفتعل - تفتعل */
        else if (in_array($word[0].$word[1], $this->pr53[3]) && $word[4].$word[5] == 'ت'){
            $word = $this->generate_from_indexes($word,1,3,4);
        }
        /* مفاعل - تفاعل */
        else if (in_array($word[0].$word[1], $this->pr53[4]) && $word[4].$word[5] == 'ا'){
            $word = $this->generate_from_indexes($word,1,3,4);
        }
        /* فعولة - فعالة */
        else if (in_array($word[4].$word[5], $this->pr53[5]) && $word[8].$word[9] == 'ة'){
            $word = $this->generate_from_indexes($word,0,1,3);
        }
        /* انفعل - منفعل */
        else if (in_array($word[0].$word[1], $this->pr53[6]) && $word[2].$word[3] == 'ن'){
            $word = $this->generate_from_indexes($word,2,3,4);
        }
        /* افعال */
        else if ($word[6].$word[7] == 'ا' && $word[0].$word[1] == 'ا'){
            $word = $this->generate_from_indexes($word,1,2,4);
        }
        /* فعلان */
        else if ($word[8].$word[9] == 'ن' && $word[6].$word[7] == 'ا'){
            $word = $this->generate_from_indexes($word,0,1,2);
        }
        /* تفعيل */
        else if ($word[6].$word[7] == 'ي' && $word[0].$word[1] == 'ت'){
            $word = $this->generate_from_indexes($word,1,2,4);
        }
        /* فعاول */
        else if ($word[6].$word[7] == 'و' && $word[2].$word[3] == 'ا'){
            $word = $this->generate_from_indexes($word,0,2,4);
        }
        /* فواعل */
        else if ($word[4].$word[5] == 'ا' && $word[2].$word[3] == 'و'){
            $word = $this->generate_from_indexes($word,0,3,4);
        }
        /* فعائل */
        else if ($word[6].$word[7] == 'أ' && $word[4].$word[5] == 'ا'){
            $word = $this->generate_from_indexes($word,0,1,4);
        }
        /* فاعلة */
        else if ($word[8].$word[9] == 'ة' && $word[2].$word[3] == 'ا'){
            $word = $this->generate_from_indexes($word,0,2,3);
        }
        /* فعالي */
        else if ($word[8].$word[9] == 'ي' && $word[4].$word[5] == 'ا'){
            $word = $this->generate_from_indexes($word,0,1,3);
        }
        /* فعلاء */
        else if ($word[8].$word[9] == 'أ' && $word[6].$word[7] == 'ا'){
            $word = $this->generate_from_indexes($word,0,1,2);
        }
        else{
            // If possible normalize short suffix
            $word = $this->suf1($word);
            // If still possible normalize short suffix
            if (mb_strlen($word) == 5){
                $word = $this->pre1($word);
            }
        }
        return $word;
    }

    // process length five patterns and extract length four roots
    function pro_w54($word){
        /* تفعلل - افعلل - مفعلل */
        if (in_array($word[0].$word[1], $this->pr53[2])){
            $word = mb_substr($word,1);
        }
        /* فعللة */
        else if ($word[8].$word[9] == 'ة'){
            $word = mb_substr($word,0,4);
        }
        /* فعالل */
        else if ($word[4].$word[5] == 'ا'){
            $word = mb_substr($word,0,2) . mb_substr($word,3);
        }
        return $word;
    }

    // ending step (word of length five)
    function end_w5($word){
        if (mb_strlen($word) == 4){
            $word = $this->pro_w4($word);
        }
        else if (mb_strlen($word) == 5){
            $word = $this->pro_w54($word);
        }
        return $word;
    }

    // process length six patterns and extract length three roots
    function pro_w6($word){
        $cpre3 = mb_substr($word,0,3);
        /* استفعل - مستفعل */
        if ($cpre3 == 'مست' || $cpre3 == 'است'){
            $word = $this->generate_from_indexes($word,3,4,5);
        }
        /* مفعالة */
        else if ($word[0].$word[1] == 'م' && $word[6].$word[7] == 'ا' && $word[10].$word[11] == 'ة'){
            $word = $this->generate_from_indexes($word,1,2,4);
        }
        /* افتعال */
        else if ($word[0].$word[1] == 'ا' && $word[4].$word[5] == 'ت' && $word[8].$word[9] == 'ا'){
            $word = $this->generate_from_indexes($word,1,3,5);
        }
        /* افعوعل */
        else if ($word[0].$word[1] == 'ا' && $word[6].$word[7] == 'و' && $word[4].$word[5] == $word[8].$word[9]){
            $word = $this->generate_from_indexes($word,1,4,5);
        }
        /* تفاعيل */
        else if ($word[0].$word[1] == 'ت' && $word[4].$word[5] == 'ا' && $word[8].$word[9] == 'ي'){
            $word = $this->generate_from_indexes($word,1,3,5);
        }
        /* مفاعيل */
        else if ($word[0].$word[1] == 'م' && $word[4].$word[5] == 'ا' && $word[8].$word[9] == 'ي'){
            $word = $this->generate_from_indexes($word,1,3,5);
        }
        else{
            // If possible normalize short suffix
            $word = $this->suf1($word);
            // If still possible normalize short suffix
            if (mb_strlen($word) == 6){
                $word = $this->pre1($word);
            }
        }
        return $word;
    }

    // process length six patterns and extract length four roots
    function pro_w64($word){
        /* افعلال */
        if ($word[0].$word[1] == 'ا' && $word[8].$word[9] == 'ا'){
            $word = mb_substr($word,1,3) . ($word[10].$word[11]);
        }
        /* متفعلل */
        else if (mb_substr($word,0,2) == 'مت'){
            $word = mb_substr($word,2);
        }
        return $word;
    }

    // ending step (word of length six)
    function end_w6($word){
        if (mb_strlen($word) == 5){
            $word = $this->pro_w53($word);
            $word = $this->end_w5($word);
        }
        else if (mb_strlen($word) == 6){
            $word = $this->pro_w64($word);
        }
        return $word;
    }


    // normalize short suffix
    function suf1($word){
        foreach ($this->s1 as $sf1){
            $len = mb_strlen($word) - 1;
            $lst = $word[2*$len].$word[(2*$len)+1];
            if ($lst == $sf1){
                return mb_substr($word,0,$len);
            }
        }
        return $word;
    }


    // normalize short prefix
    function pre1($word){
        foreach ($this->p1 as $sp1){
            if ($word[0].$word[1] == $sp1){
                return mb_substr($word,1);
            }
        }
        return $word;
    }

    // Remove duplicates in last token if any
    private function reduce_doubles($word){
        if (mb_strlen($word) <=3 ) return $word;
        $pos = [];
        for ($i = 1; $i < mb_strlen($word); $i++){
            $j = $i-1;
            $prev = $word[2*$j] . $word[(2*$j)+1];
            $curr = $word[2*$i] . $word[(2*$i)+1];
            if ($prev == $curr) array_push($pos,$i);
        }

        $removed = 0;
        foreach ($pos as $i){
            $word = $this->remove_char($word,$i-$removed);
            $removed++;
        }

        return $word;
    }

    // If starting with `the` making total length more than 7 remove the
    private function reduce_the($word){
        if ($word[0].$word[1] == 'ا' && $word[2].$word[3] == 'ل'){
            return mb_substr($word,2);
        }
        return $word;
    }

    // A helper function to create three chars root
    private function generate_from_indexes($word,$i,$j,$k){
        return $word[2*$i].$word[(2*$i)+1]
            .$word[2*$j].$word[(2*$j)+1]
            .$word[2*$k].$word[(2*$k)+1];
    }

    // A helper function to remove char from MB String
    private function remove_char($word,$index){
        $word = mb_substr($word,0,$index) . mb_substr($word,$index+1);
        return $word;
    }


    // Implementation of the missing MB String Replace
    private function mb_str_replace($search, $replace, $subject, &$count = 0) {
        if (!is_array($subject)) {
            $searches = is_array($search) ? array_values($search) : array($search);
            $replacements = is_array($replace) ? array_values($replace) : array($replace);
            $replacements = array_pad($replacements, count($searches), '');
            foreach ($searches as $key => $search) {
                $parts = mb_split(preg_quote($search), $subject);
                $count += count($parts) - 1;
                $subject = implode($replacements[$key], $parts);
            }
        } else {
            foreach ($subject as $key => $value) {
                $subject[$key] = $this->mb_str_replace($search, $replace, $value, $count);
            }
        }
        return $subject;
    }


}
