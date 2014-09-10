<?php
/**
 * Lang_detect Class
 *
 * Language detection library for CodeIgniter.
 *
 * @author        La2ha
 * @version       1.0
 * @link          http://la2ha.ru/dev/web/php/codeigniter/libraries_helpers/lang_detect
 */
//phpinfo();
//die;
date_default_timezone_set('Europe/Kiev');
class Lang_detect
{
    var $language = null;


    public function __construct()
    {
        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $this->language = array_combine($list[1], $list[2]);
                foreach ($this->language as $n => $v)
                    $this->language[$n] = $v ? $v : 1;
                arsort($this->language, SORT_NUMERIC);
            }
        } else $this->language = array();
    }

    public function getBestMatch($default, $langs)
    {
        $languages=array();
        foreach ($langs as $lang => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lang);
                }
            }else $languages[strtolower($alias)]=strtolower($lang);
        }
        foreach ($this->language as $l => $v) {
            $s = strtok($l, '-'); // убираем то что идет после тире в языках вида "en-us, ru-ru"
            if (isset($languages[$s]))
                return $languages[$s];
        }
        return $default;
    }


}
if($_GET['lang']){
    $_COOKIE['lang'] = $_GET['lang'];
    setcookie('lang',$_GET['lang'],strtotime( '+30 days' ));
}
else{
    if(isset($_COOKIE['lang'])){

    }
    else{


        $lang = new Lang_detect();
        $lang_cur = $lang->getBestMatch('en',array(
                'ru' => "ru",
                'ua' => 'ua',
                'en' => "en"
            )
        );
        $_COOKIE['lang'] = $lang_cur;

        setcookie('lang',$lang_cur,strtotime( '+30 days' ));
    }
}
require_once('./lib/connect.php');
require_once('./config.php');
require_once('./lib/lang.php');
require_once('./lib/tag.php');
require_once('./lib/project.php');
require_once('./lib/user.php');


?>
<!doctype html>
<html lang="<?php echo $_COOKIE['lang']?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=0.6, user-scalable=no">
    <title>Matematica</title>
    <link rel="stylesheet" href="/css/main.css"/>
    <link rel="stylesheet" href="/css/TimeCircles.css" />
    <?php
        if(strpos($_SERVER['REQUEST_URI'],"/project/")!==false){

            $pr = new Project();
            $pr->getProject(end(explode("/",$_SERVER['REQUEST_URI'])));
            $desc = $pr->getDescription();?><meta property="og:title" content="<?=$pr->getName()?>">
    <meta property="og:description" name="description" content="<?=$desc[0]->text?>">
    <meta property="og:image" content="<?=$pr->prev_image?>">
            <?php
        }

    ?>
    <script>
        var datesInMonth = <?=Date('t')?>;
    </script>
<!--    <script type='text/javascript' src='/js/jquery-1.8.3.min.js'></script>-->
        <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/flipclock.js"></script>
    <script src="http://webext.ru/wp-content/uploads/2012/03/jquery.mousewheel.js"></script>
    <script src="/js/jquery.touchwipe.1.1.1.js"></script>
    <script src="/js/jquery.isotope.min.js"></script>
    <script src="/js/waypoints.min.js"></script>
    <script src="/js/jquery.hoverdir.min.js"></script>
    <script src="/js/main.js"></script>
    <script type="text/javascript" src="/js/TimeCircles.js"></script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATnghvpYkTXWlUeboCbuxx7jkvfCi-jMw&sensor=false">
    </script>
    <script type="text/javascript">
        function initialize() {
            var mapOptions = {
                center: new google.maps.LatLng(47.820794,35.168494),
                zoom: 15
            };
            var map = new google.maps.Map(document.getElementById("map"),
                mapOptions);


            var myLatlng = new google.maps.LatLng(47.820794,35.178494);

            var marker = new google.maps.Marker({
                position: myLatlng,
                title:"Hello World!"
            });
            marker.setMap(map);

            // To add the marker to the map, call setMap();

        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>

    <!--fullPageScroll START-->
    <script src="/js/fullPageScroll/vendors/jquery.easings.min.js"></script>
    <script type="text/javascript" src="/js/fullPageScroll/vendors/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="/js/fullPageScroll/jquery.fullPage.js"></script>
    <script type="text/javascript" src="/js/imagesloaded.pkgd.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#fullpage').fullpage({
                menu: '.navigation',
                anchors: ['home', 'ideas', 'experience', 'projects', 'team', 'contacts'],
                scrollOverflow: true,
                touchSensitivity: 20,
                afterLoad: function(anchorLink){

                    if(anchorLink == 'contacts'){
                        if($('.overflow').hasClass('hidden')){
                            $.fn.fullpage.setAllowScrolling(false);
                        }
                    }
                }
            });
        });
    </script>

<!--    <script src="/js/fotorama/fotorama.js"></script>-->
<!--    <link rel="stylesheet" href="/js/fotorama/fotorama.css" />-->

    <!--fullPageScroll END-->

</head>
<body>
<div class="navigation">
    <nav >
        <a href="#home" class="home active" data-menuanchor="home">
            <div class="nav-text-block">
                <div class="delta"></div>
                <span class="nav-text-label"><?=Lang::text('Математика')?></span>
            </div>
        </a>
    </nav>
    <div class="nav-block">
        <nav >
            <a href="#ideas" class="ideas" data-menuanchor="ideas">
                <div class="nav-text-block">
                    <div class="delta"></div>
                    <span class="nav-text-label">
                        <?=Lang::text('Фокус')?>
                    </span>
                </div>
            </a>
        </nav>
        <nav >
            <a href="#experience" class="experience" data-menuanchor="experience">
                <div class="nav-text-block">
                    <div class="delta"></div>
                    <span class="nav-text-label"><?=Lang::text('Опыт')?></span>
                </div>
            </a>
        </nav>
        <nav >
            <a href="#projects" class="projects" data-menuanchor="projects">
                <div class="nav-text-block" >
                    <div class="delta"></div>
                    <span class="nav-text-label"><?=Lang::text('Проекты')?></span>
                </div>
            </a>
        </nav>
        <nav >
            <a href="#team" class="team" data-menuanchor="team">
                <div class="nav-text-block"><div class="delta"></div>
                    <span class="nav-text-label"><?=Lang::text('Команда')?></span>
                </div>
            </a>
        </nav>
        <nav >
            <a href="#contacts" class="contacts disable-scroll" data-menuanchor="contacts">
                <div class="nav-text-block">
                    <div class="delta"></div>
                    <span class="nav-text-label"><?=Lang::text('Контакты')?></span>
                </div>
            </a>
        </nav>
    </div>
    <nav class="lang"><span class="lang-label"><?=$_COOKIE['lang']?></span>
        <div class="nav-text-block"><div class="delta"></div>
            <div class="lang-ua" onmousedown="window.location ='?lang=ua'"></div>
            <div class="lang-ru" onmousedown="window.location ='?lang=ru'"></div>
            <div class="lang-en" onmousedown="window.location ='?lang=en'"></div>
        </div>
    </nav>
</div>
<div class="content" id="fullpage">
    <div id="home-my" class="content-block section">
        <div class="content-contener">
            <div class="text">
                <?=Lang::text('У самой сложной задачи всегда<br>есть простое решение')?>
            </div>
            <div class="title">
                <?=Lang::text('Математика')?>
            </div>
        </div>
    </div>
    <div id="ideas-my" class="content-block section">
        <div class="content-contener">
            <div class="title">
                <?=Lang::text('Фокус')?>
            </div>
            <div class="text">
                <div>
                    <img src="/images/logo-02.svg" alt=""/>
                    <br>
                    <span><?=Lang::text('Логотип')?></span>
                </div>
                <div>
                    <img src="/images/branding-01.svg" alt=""/>
                    <br>
                    <span><?=Lang::text('Брендинг')?></span>
                </div>
                <div>
                    <img src="/images/app-01.svg" alt=""/>
                    <br>
                    <span><?=Lang::text('Приложения')?></span>
                </div>
                <div>
                    <img src="/images/design-01.svg" alt=""/>
                    <br>
                    <span><?=Lang::text('Дизайн')?></span>
                </div>
                <div>
                    <img src="/images/web-01.svg" alt=""/>
                    <br>
                    <span>Web</span>
                </div>
            </div>
        </div>
    </div>
    <div id="experience-my" class="content-block section">
        <div class="content-contener">
            <div class="title">
                <?=Lang::text('Опыт')?>
            </div>
            <div id="DateCountdown" data-date="2010-11-01 00:00:00">
                <nobr>

                    <div id="clock"></div>

                    <script type="text/javascript">
                        //var clock = jQuery('#clock').FlipClock(<?=strtotime(date('Y-m-d H:i:s'))-strtotime('2010-11-01 00:00:00')?>, {
                        //    clockFace: 'DailyCounter'});
                    </script>
                    <?php
                    $years = date("Y")-2010;
                    $days = date('d')-1;
                    $months = date("m")-11;
                    if($months>0){

                    }
                    else{
                        $months += 12;
                        $years--;
                    }
                    ?>
                <div class="years">
                    <div class="time-text" id="years"><?=$years?></div>
                    <span><?=Lang::text('год')?></span>
                </div>
                <div class="months">
                    <div class="time-text" id="months"><?=$months?></div>
                    <span><?=Lang::text('месяц')?></span>
                </div>
                <div class="days">
                    <div class="time-text" id="days"><?=$days?></div>
                    <span><?=Lang::text('день')?></span>
                </div>
                <div class="hours">
                    <div class="time-text" id="hours"><?=date('H')?></div>
                    <span><?=Lang::text('час')?></span></div>
                <div class="minutes">
                    <div class="time-text" id="minutes"><?=date('i')?></div>
                    <span><?=Lang::text('минута')?></span>
                </div>
                <div class="seconds">
                    <div class="time-text" id="seconds"><?=date('s')?></div>
                    <span><?=Lang::text('секунда')?></span>
                </div>

                </nobr>
            </div>
        </div>
    </div>

    <!--div id="project-my" style="display: none;background-color: #fff" class="content-block">
        <div class="proj-content"></div>
    </div-->

    <div id="projects-my" class="content-block section">


    <div class="progect-page">
    <div style="clear: both"></div>


    <!-- Portfolio Filter -->
    <div class="filters"><span><?=Lang::text('Ф И Л Ь Т Р Ы')?></span></div>
    <ul id="portfolio-filter" class="list-inline">
        <li class="active"><a href="#" data-filter="*"><?=Lang::text('Все')?></a></li>
        <?
        $tags = Tag::getTagList('objects');
        foreach($tags as $tag):
        ?>
        <li><a href="#" data-filter="<?=$tag->code?>"><?=$tag->getName()?></a></li>
        <?
        endforeach;
        ?>
    </ul>

    <!-- Project List -->
    <div style="clear: both"></div>

    <ul id="portfolio-list" class="project-list">
        <?
//        $tags_list_codes = Tag::getCodesArray();
        $projects = Project::getProjectsList('objects');
        foreach($projects as $project):
            $project_tags = explode(",",$project->tags);
            foreach($project_tags as $tag_id){
                foreach($tags as $tag){
                    if($tag_id == $tag->id){
                        $project->taglist[$tag->code] = $tag->getName();
                        break;
                    }
                }
            }
            ?>
            <li class="<?=join(" ",array_keys($project->taglist))?>">
                <a href="/index2.php?type=project&task=view&id=<?=$project->id?>" onclick="return false;">
                    <img src="/images/projects/<?=$project->id.DIRECTORY_SEPARATOR.$project->prev_image?>" alt="" />
                    <div class="portfolio-item-content">
                        <div class="wrapper">
                            <div class="inner">
                                <div class="header"><?=$project->getName()?></div>
                                <div class="seperator"></div>
                                <p class="body"><?=join(", ",array_values($project->taglist))?></p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
            <?
        endforeach;
        ?>
    </ul>
        <div id="show_projects" onclick="showProjects();"><span><?=Lang::text('Е Щ Е&nbsp;&nbsp;&nbsp;М И Н Е Р А Л О В')?></span></div>
<!-- #projects -->
    </div>
    </div>
    <div id="team-my" class="content-block section">
                <div class="title" style="text-transform: uppercase">
                    <?=Lang::text('Это мы')?>
                </div>
                <div class="weare">
                    <div class="block active" id="block_1">
                        <?
                        $i=0;
                        $users = User::getUsersList("objects");
                        foreach($users as $user):
                        if($i%6==0 && $i!=0){?></div><div class="block" id="block_<?=$i/6+1;?>"><?}
                        if(!$user->in_office) continue;
                        ?>
                            <div class="user">
                                <div class="flip-container" ontouchstart="this.classList.toggle('hover');">
                                    <div class="flipper">
                                        <div class="front">
                                            <img src="/images/users/<?=$user->photo?$user->photo:'20140502Bogdan.png'?>" alt=""/>
                                        </div>
                                        <div class="back">
                                            <img src="/images/users/<?=$user->animatePhoto?$user->animatePhoto:$user->photo?$user->photo:'20140502Bogdan.png'?>" alt=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="user-name"><?=$user->getName()?></div>
                            </div>
                        <?
                        $i++;
                        endforeach;
                        ?>
                    </div>
                    <div id="show_faces">
                        <span onclick="$('#team-my #block_2').addClass('active');$(this).parent().hide();$.fn.fullpage.reBuild();"><?=Lang::text('Б О Л Ь Ш Е&nbsp;&nbsp;&nbsp;З О Л О Т А');?></span>
                    </div>
        </div>
    </div>
    <div id="contacts-my" class="content-block section">
        <div id="map"></div>
        <div class="overflow">
            <div class="content-contener">
                <div class="title" style="color: #fff;text-align: left;"><?=Lang::text('Математика')?></div>
                <div class="text" style="color: #fff;text-align: left;">
                    <?=Lang::text('Украина<br>г. Запорожье, 69002<br>ул. Красногвардейская, 40 оф 507<br>info@matematica.com.ua<br><br><a href="tel:+380933443326">+38 093 344 33 26</a>')?>
                </div>
            </div>
        </div>
        <a href="#" onclick="$(this).toggleClass('rotate');$('.overflow').toggleClass('hidden');if($('.overflow').hasClass('hidden')){$.fn.fullpage.setAllowScrolling(false);}else{$.fn.fullpage.setAllowScrolling(true);};return false;" style="webkit-transition: all 0.5s ease;-moz-transition: all 0.5s ease;-ms-transition: all 0.5s ease;-o-transition: all 0.5s ease;transition: all 0.5s ease;position: absolute;right: 0px;z-index: 999;top: 50%;margin-top: -45px"><img src="/images/strelka.svg"></a>
    </div>
</div>


</body>
</html>
