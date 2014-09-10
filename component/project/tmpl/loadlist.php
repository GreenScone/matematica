<?php
/**
 * Created by PhpStorm.
 * User: flash1k
 * Date: 05.08.14
 * Time: 16:10
 */

$project = new Project();
$project->getProjectsList('object',$_GET['start'],12);
$project->getObjects();
$users = User::getUsersList();
$tags = Tag::getTagList("array");
?>
<script>
    currentProjectName = firstTitle + ' - <?=$project->getName()?>';
    currentProjectUrl = "/project/<?=$project->id?>"+window.location.hash;
    history.pushState("","",currentProjectUrl);
    document.title = currentProjectName;
</script>
<div class="project-name"><?=$project->getName()?></div>
<?
switch($project->view_type){
    case 1:
        ?>
        <img src="/images/projects/<?=$project->id.DIRECTORY_SEPARATOR.$project->objects[0];?>" style="width: 100%" />
        <?
        break;
    case 2:
        ?>
        <div style="background-image: url('/images/slider.jpg');background-position: 50% 50%;background-size: auto 100%;position: relative;overflow: hidden;">
            <div id="slider">
                <div id="next"></div>
                <div id="prev"></div>

                <?
                foreach($project->objects as $object){
                    ?>
                    <img src="/images/projects/<?=$project->id.DIRECTORY_SEPARATOR.$object;?>" />
                <?
                }
                ?>
            </div>
        </div>
        <script>
            var slides_count = $("#slider img").length;
            var slides_count_loaded = 0;
            $("#slider img").load(function(){
                slides_count_loaded++;
                if(slides_count == slides_count_loaded){
                    var slider = $("#slider").slider();
                }
            });
        </script>
        <?
        break;
    case 3:
        ?>
        <?=$project->objects[0];?>
        <?
        break;
    case 4:
    if($project->id==20 || $project->id==21 || $project->id==23 || $project->id==24 || $project->id==25 || $project->id==27 || $project->id==28 || $project->id==29 || $project->id==30){
        ?>
        <div class="project-patern" style="width: 100%;background-image: url('/images/projects/<?=$project->id?>/pattern2.png');background-attachment:fixed;background-position: 0 -230px" >
            <div style="width: 100%;max-width: 1262px;margin: 0 auto;padding-top: 144px;padding-bottom: 144px">
                <?
                }
                foreach($project->objects as $object){ ?>
                    <img src="/images/projects/<?=$project->id.DIRECTORY_SEPARATOR.$object;?>" style="width: 100%;" />
                <?
                }
                if($project->id==20 || $project->id==21 || $project->id==23 || $project->id==24 || $project->id==25 || $project->id==27 || $project->id==28 || $project->id==29 || $project->id==30){
                ?>
            </div></div>
        <style>
            .project-patern{
                -webkit-transition: none;
                -moz-transition: none;
                -ms-transition: none;
                -o-transition: none;
                transition: none;
            }
        </style>
        <script>
            $('#project-my').mousemove(function(e){
                var x = -(e.pageX + this.offsetLeft) / 10;
                var y = -(e.pageY + this.offsetTop) / 10;
                $(".project-patern").css('background-position', x + 'px ' + y + 'px');
            });
        </script>
    <?
    }
        break;
}

?>

<!--<img src="/images/obra_matematica-01.svg" alt="" style="width: 100%">-->
<div style="clear: both"></div>
<?
if($project->url){
    ?>
    <a href="http://<?=$project->url?>" class="project-link" target="_blank"><div><?=$project->url?></div></a>
<?
}
?>
<div class="description">
    <?
    foreach($project->getDescription() as $desc){
        ?>
        <p class="desc-title">
            <?=$desc->title?>
        </p>
        <p class="desc-text">
            <?=$desc->text?>
        </p>
    <?
    }
    ?>


</div>
<div class="worked-team">

    <div class="title-team">
        <?=Lang::text('Над проектом работали')?>
    </div>
    <?
    foreach($users as $user){
        if(in_array($user->id,$project->users)){
            ?>
            <div class="user">
                <img src="/images/users/<?=$user->photo?>" alt="">
                <div class="user-name"><?=$user->getName()?></div>
            </div>
        <?
        }
    }
    ?>
</div>
<div style="clear: both"></div>
<div class="share-block" style="background-position-y: 26.73758865248227%;">
    <div style="position: absolute;width: 100%;height: 100%;background-color:rgba(0, 5, 23, 0.80) ">
        <div class="share-title">
            <?=Lang::text('Нравится. Расскажи друзьям')?>
        </div>
        <div class="share-buttons">
            <div class="google-share"></div>
            <div class="fb-share"></div>
            <div class="vk-share"></div>
            <div class="tw-share"></div>
        </div>
    </div>
</div>