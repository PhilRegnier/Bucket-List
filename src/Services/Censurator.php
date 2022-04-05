<?php

namespace App\Services;

class Censurator
{
    public function purify($sentence): string
    {
        //$jurons= file('../data/bad_words.txt','r');
        //$jurons = explode("\n", $file,true);
        //var_dump($jurons);

        $jurons[] = 'connard';
        $jurons[] = 'enculé';

        foreach ($jurons as $juron)
        {
            if (strpos($sentence, $juron))  {
                var_dump($juron);
                str_replace($juron, '***', $sentence);
            }
        }
        return $sentence;
    }
}