<?
function synonimize($text = null)
{
    $baseWord = file('baseWord.txt');
    $StopWord = file('stopWord.txt');

    //стоп
    $arStopWords = [];
    if (is_array($StopWord) && count($StopWord) > 0) {
        foreach ($StopWord as $l) {
            $arStopWords[] = '#(^|[\n ])' . trim(preg_quote($l, '/')) . '($|[\n ])#ui';
        }
    }

    //замена
    $arWords = [];
    if (is_array($baseWord) && count($baseWord) > 0) {
        foreach ($baseWord as $l) {
            //$arWords[explode('|', $l)[0]] = explode('|', $l)[1];
            $arWords[] = $l;
        }
    }
    $res = [];
    if (is_array($arWords) && count($arWords) > 0) {
        foreach ($arWords as $search => $st) {
            $ex = explode(',', $st);
            if(count($ex) > 0){
                foreach($ex as $v){
                    if(!empty(trim($v))) {
                        //(?!\=) - не содержит равно
                        $keys_arr[$search][] = '#(^|[\n ]|\.|,|\?|\!)' . preg_quote(trim($v), '/') . '($|[\n ]|\.|,|\?|\!)#iu';

                        $res[$search][] = $v;
                        $replace[$search][] = $v;
                    }
                }
            }
        }
    }

    //исключения
    $text = preg_replace_callback($arStopWords, function ($matches) {
        if (!empty($matches[0])) {

            //если с заглавной
            $rest = mb_substr($matches[0], 0, 1, 'UTF8');
            if (mb_strtolower($rest, 'utf-8') != $rest) {
                $forReplaceStop_ex = explode(' ', $matches[0]);
                $matches[0] = mb_convert_case($forReplaceStop_ex[0], MB_CASE_TITLE, "UTF-8");
                if (count($forReplaceStop_ex) > 1) {
                    unset($forReplaceStop_ex[0]);
                    $matches[0] = $matches[0] . ' ' . implode(' ', $forReplaceStop_ex);
                }
            }
            //вывод
            if($_POST['mark'] == 'Y') {
                return ' <span style="background-color: red; color: white;">@' . trim($matches[0]) . '@</span> ';
            }
            else{
                return ' @' . trim($matches[0]) . '@ ';
            }
        }
    }, $text);

    //замена
    foreach($keys_arr as $kk => $keys) {
        $text = preg_replace_callback($keys, function ($matches) use ($replace, $kk) {
            $matches[0] = trim($matches[0]);
            if (!empty($matches[0])) {
                $forReplace = '';
                $rest = mb_substr($matches[0], 0, 1, 'UTF8');

                //если с заглавной
                if (mb_strtolower($rest, 'utf-8') != $rest) {
                    //если искомое слово в верхнем регистре, меняем на нижний нижний регистр
                    $matches[0] = mb_strtolower($matches[0]);
                }

                $temp_for_replace = $replace[$kk];

                $temp_for_replace = array_flip($temp_for_replace);
                unset($temp_for_replace[trim($matches[0])]);
                $temp_for_replace = array_flip($temp_for_replace);
                $temp_for_replace = array_values($temp_for_replace);


                $number = rand(0, count($temp_for_replace) - 1);
                $forReplace = trim($temp_for_replace[$number]);

                //echo ' колчичество: '.count($temp_for_replace).'номер: '.$number.' искомое: '.$matches[0].' заменяем: '.$temp_for_replace[$number].'<br>';

                //если с заглавной, результат замены в верхний регистр если искомое слово было в верхенем
                // меняем регистр буквы только для первого слова.
                if (mb_strtolower($rest, 'utf-8') != $rest) {
                    $forReplace_ex = explode(' ', $forReplace);
                    $forReplace = mb_convert_case($forReplace_ex[0], MB_CASE_TITLE, "UTF-8");
                    if (count($forReplace_ex) > 1) {
                        unset($forReplace_ex[0]);
                        $forReplace = $forReplace . ' ' . implode(' ', $forReplace_ex);
                    }
                }
                if (!empty($forReplace)) {

                    //знаки пунктуации
                    $fe[0] = substr($matches[0], 0, 1);
                    if($fe[0] != ' ' && $fe[0] != '.' && $fe[0] != ',' && $fe[0] != '!' && $fe[0] != '?'){
                        $fe[0] = '';
                    }
                    $fe[1] = substr($matches[0], -1, 1);
                    if($fe[1] != ' ' && $fe[1] != '.' && $fe[1] != ',' && $fe[1] != '!' && $fe[1] != '?'){
                        $fe[1] = '';
                    }
                    $forReplace = $fe[0].$forReplace.$fe[1];


                    if($_POST['mark'] == 'Y') {
                        return ' <span style="background-color: yellow; cursor: help;" title="' . $matches[0] . '">@' . $forReplace . '@</span> ';
                    }
                    else{
                        return ' @' . $forReplace . '@ ';
                    }
                }
            }
        }, $text);
    }

    $text = preg_replace('#@#','',$text);
    return $text;
}


function synonimizeOne($text = null)
{
    $baseWord = file('baseWord.txt');
    //$StopWord = file('stopWord.txt');

    //замена
    $arWords = [];
    if (is_array($baseWord) && count($baseWord) > 0) {
        foreach ($baseWord as $l) {
            //$arWords[explode('|', $l)[0]] = explode('|', $l)[1];
            $arWords[] = $l;
        }
    }
    $res = [];
    if (is_array($arWords) && count($arWords) > 0) {
        foreach ($arWords as $search => $st) {

            $ex = explode('|', $st);
            $startWord = $ex[0];
            $replaceWord = explode(',', $ex[1]);
            if(count($replaceWord) > 0){
                foreach($replaceWord as $v){
                    if(!empty(trim($v))) {
                        //(?!\=) - не содержит равно
                        $keys_arr[$startWord][] = '#(^|[\n ]|\.|,|\?|\!)' . preg_quote(trim($v), '/') . '($|[\n ]|\.|,|\?|\!)#iu';
                        if(!empty(trim($v))) {
                            $res[$startWord][] = $v;
                            $replace[$startWord][] = $v;
                        }
                    }
                }
            }
        }
    }

    //замена
    foreach($keys_arr as $kk => $keys) {
        $text = preg_replace_callback($keys, function ($matches) use ($replace, $kk) {
            $matches[0] = trim($matches[0]);
            if (!empty($matches[0])) {
                $forReplace = '';
                $rest = mb_substr($matches[0], 0, 1, 'UTF8');

                //если с заглавной
                if (mb_strtolower($rest, 'utf-8') != $rest) {
                    //если искомое слово в верхнем регистре, меняем на нижний нижний регистр
                    $matches[0] = mb_strtolower($matches[0]);
                }

                $temp_for_replace = $replace[$kk];

                $temp_for_replace = array_flip($temp_for_replace);
                unset($temp_for_replace[trim($matches[0])]);
                $temp_for_replace = array_flip($temp_for_replace);
                $temp_for_replace = array_values($temp_for_replace);


                $number = rand(0, count($temp_for_replace) - 1);
                $forReplace = trim($temp_for_replace[$number]);

                //echo ' колчичество: '.count($temp_for_replace).'номер: '.$number.' искомое: '.$matches[0].' заменяем: '.$temp_for_replace[$number].'<br>';

                //если с заглавной, результат замены в верхний регистр если искомое слово было в верхенем
                // меняем регистр буквы только для первого слова.
                if (mb_strtolower($rest, 'utf-8') != $rest) {
                    $forReplace_ex = explode(' ', $forReplace);
                    $forReplace = mb_convert_case($forReplace_ex[0], MB_CASE_TITLE, "UTF-8");
                    if (count($forReplace_ex) > 1) {
                        unset($forReplace_ex[0]);
                        $forReplace = $forReplace . ' ' . implode(' ', $forReplace_ex);
                    }
                }
                if (!empty($forReplace)) {

                    //знаки пунктуации
                    $fe[0] = substr($matches[0], 0, 1);
                    if($fe[0] != ' ' && $fe[0] != '.' && $fe[0] != ',' && $fe[0] != '!' && $fe[0] != '?'){
                        $fe[0] = '';
                    }
                    $fe[1] = substr($matches[0], -1, 1);
                    if($fe[1] != ' ' && $fe[1] != '.' && $fe[1] != ',' && $fe[1] != '!' && $fe[1] != '?'){
                        $fe[1] = '';
                    }
                    $forReplace = $fe[0].$forReplace.$fe[1];


                    if($_POST['mark'] == 'Y') {
                        return ' <span style="background-color: yellow; cursor: help;" title="' . $matches[0] . '">@' . $forReplace . '@</span> ';
                    }
                    else{
                        return ' @' . $forReplace . '@ ';
                    }
                }
            }
        }, $text);
    }

    $text = preg_replace('#@#','',$text);
    return $text;
}
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

function syn($text = null) {
    $syn = file ('./baseWord.txt');
    foreach ($syn as $one) {
        $ww = explode('|', trim($one));
        $vars = explode(',', trim($ww[1]));
        $word = strtolower(trim($ww[0]));
        $swords[$word] = $vars;
    }
    $callback = function ($matches)use ($swords)
    {
        // проверка на регистр вводимиых символов
        $Zzz = false;
        $chr = mb_substr ($matches[1], 0, 1, 'utf-8');
        if( mb_strtolower($chr, 'utf-8') != $chr ) {
            $Zzz = true;
            $matchesKey = mb_strtolower($matches[1]);
        }
        else{
            $matchesKey = $matches[1];
        }

        if (isset( $swords[$matchesKey])) {
            $swords[$matchesKey] = array_diff($swords[$matchesKey], array(''));
            $replace = trim($swords[$matchesKey][array_rand ($swords[$matchesKey])]);
            if($Zzz === true){
                return mb_ucfirst($replace);
            }
            else{
                return $replace;
            }
        } else {
            return $matches[1];
        }

    };

    $pattern = array(
        '/([\w]+[\s]+[\w]+[\s]+[\w]+)/iu',
        '/([\w]+[\s]+[\w]+)/iu',
        '/([\w]+)/iu',

    );

    $text = preg_replace_callback($pattern, $callback, $text);

    return $text;
}


/*
Жилой комплекс бизнес-класса Наутилус апартаменты,квартира,резиденция,жилье,помещение расположен в престижном микрорайоне Новый Сочи на улице Виноградная в 500 м от моря. Отделка фасада здания и помещений общего пользования – штукатурка, покраска, плитка. Современный 16-этажный монолитный дом, в котором насчитывается 96 квартир. Типовые квартиры площадью от 52 до 90 кв.м. Черновая отделка, установлены электрические плиты, приборы учета, однокамерные стеклопакеты, железные входные квартиры. Центральные коммуникации, отопление осуществляется от котельной санатория. В доме установлены лифты, в том числе грузовой. Закрытая благоустроенная территория с оборудованными местами для отдыха. Предусмотрена автостоянка. Рядом расположены санатории «Заполярье» и «имени Дзержинского», а также школа, детский сад, поликлиника, магазины. Близость к остановкам общественного транспорта. Расстояние до моря и пляжа – 500 м. До центра Сочи можно добраться за 5 минут.

ЖК "Метрополь" — это идеальное место для тех, кто мечтает о благополучной жизни в современном комфортабельном доме в центре города. Это оптимальный выбор для людей, предпочитающих сочетание комфорта, тишины и удобства расположения. Панорамные окна, высокие потолки квартир, двухуровневый паркинг, закрытый двор — все это доступно в комплексе бизнес-класса «Метрополь». Вид на море и парк Дендрарий создадут уникальный микроклимат проживания в нашем жилом комплексе. Помимо всего прочего, расположение ЖК в центре города дает возможность получить готовую инфраструктуру — магазины и супермаркеты, прогулочная набережная, транспортная доступность до любой точки города.

Просторный особняк 420 м2 на просторном участке зимли в 8 соток  с бассейном дополняемый мансардным этажем и летней кухней.Яркими изюминками интерьера являются разноцветные художественные витражи.В доме 6 спален и 6 санузлов,каминный зал,сауна с бассейном,столовая,2 эффектных прихожих,котельная,прачечная,гардеробная.В отделке широко использованы натуральные, экологически чистые материалы:мрамор,камень,дерево.В саду особняка растут вековые Пицундские сосны,олеандр,большие пальмы Вашингтония нитеносная,магнолии,Саговые пальмы и другие экзотические растения.Водопад выполнен из натурального белого скального камня,добытого в каньонах Сочинских гор.В отделке фасада использован легендарный Дербентский камень.Домик охраны ,на территории особняка,оснащен системой охраны и видеонаблюдения.Отапливаемый гараж имеет санузел и бильярдную комнату.Сочинский климат позволил сделать над гаражем Норвежскую крышу.Звоните - отвечу на все вопросы и все покажу.

Продаются просторные апартаменты в жилом комплексе элит-класса Актер Гэлакси. Апартаменты расположены на 15 этаже. Из окон открывается вид на зеленую территорию комплекса. Общая площадь составляет 89,6 кв.м. Отделка черновая, что позволяет вам сделать ремонт, о котором мечтаете именно вы. Комплекс расположен в микрорайоне Приморье, на первой береговой линии. Рядом находятся такие знаковые объекты город, как Сочинский Морской Порт, парки «Дендрарий» и «Ривьера», отель «Рэдиссон Сас Лазурная». Уникальный комплекс апартаментов «Актер Гэлакси» - это принципиально новый уровень элитного жилья в Сочи, который предоставляет своим жильцам абсолютно все условия для того, чтобы в полной мере насладиться всеми прелестями проживания в курортном городе на берегу моря! Внутреннюю инфраструктуру комплекса составляют SPA-центр, закрытый многоуровневый паркинг, свой собственный ресторан, детские игровые и спортивные площадки, многочисленные сервисные службы. Также в состав комплекса входит собственный пляж. Рядом Курортный проспект, ул.: Несебрская, Орджоникидзе, Гагринская, Пушкина, Депутатская, парки: Ривьера и Дендрарий, жилые комплексы: Актер Гэлакси, Волна, Новая Александрия. Еще больше информации о квартире и комплексе Вы можете получить, позвонив по указанному номеру телефона.

Вашему вниманию предлагается шикарная трехкомнатная квартира в элитном комплексе Королевский парк. Квартира расположена на 6 этаже и обладает чудесными видовыми характеристиками. Сделан дорогой качественный ремонт. Общая площадь квартиры составляет около 150 кв.м. Квартира распланирована на 2 изолированные спальни, гостиную комнату, кухню и 3 сан-узла. 

Королевский парк - это элитный жилой комплекс, расположенный на первой береговой линии Черного моря. Отсюда с легкостью можно добраться в любую точку города, неподалеку расположены все основные достопремичательности Сочи, такие как: парк Дендрарий, Морской Порт, парк Ривьера, Зимний и Летний театры и т.д.

Территория Королевского Парка - это живописнейшая зона с субтропической растительностью, оборудованная всем необходимым для комфортного проживания и даже больше. Внутренняя инфраструктура включает в себя: бассейны, СПА-комлпекс, детские площадки, собственный закрытый пляж, на который можно добраться с помощью трансфера или гольф-кара, рестораны, магазины, подземную охраняемую парковку, круглосуточную службу сервиса и т.д.

Для того чтобы получить больше информации о квартире и жилом комплексе, вы можете обратиться по указанному номеру телефона.
*/


echo '<div style="padding: 20px; background-color: aliceblue">';
echo '<form action="" method="post">
<textarea name="text" style="width: 100%; height: 350px;"></textarea>
<input type="submit" value="Синонимизировать" style="background-color: red; color: white; padding: 20px;">
<input name="mark" value="Y" type="checkbox"> - Подсветить
</form>';
echo '</div>';
if ($_POST['text']) {

    $text = $_POST['text'];

    echo '<div style="padding: 20px; margin: 20px; background-color: #d3dbe2">' . $text . '</div><div style="background-color: #848a91; height: 5px;"></div>';
    echo '<div style="padding: 20px; margin: 20px; background-color: #beffca">' . syn($text) . '</div>';
}
$start = microtime(true);
// тело скрипта
