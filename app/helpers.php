<?php 

/**
 * Cyrillic to latin transliteration
 * @param cryllic_text
 * @return latin_text
 */
if ( !function_exists('cryllicToLatin'))
{
    function cryllicToLatin($cryllic_text)
    {
        $cryllic = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];

        $latin = [
            'a','b','v','g','d','ye','yo','j','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','x','ts','ch','sh','sh','','','','e','yu','ya',
            'A','B','V','G','D','Ye','Yo','J','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','X','Ts','Ch','Sh','Sh','','','','E','Yu','Ya'
        ];

        return str_replace($cryllic, $latin, $cryllic_text);
    }
}