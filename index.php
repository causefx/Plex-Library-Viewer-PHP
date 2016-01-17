<?php
#CHANGE THESE HERE
$name = "Cause FX"; //your name here :)
$useSSL = true; //Use SSL?
$host = "hostname:port"; //Dont use localhost or etc, it will prevent images from loading,also dont inlcude http(s)
$token = "xxxxxxxxxxxxx"; //Plex Token
$movies = "1"; //Library Section for Movies
$tv = "2"; //Library Section for TV Shows

//DONT CHANGE THESE PARAMETERS
ini_set('display_errors',1);  error_reporting(E_ALL);
if($useSSL == true){ $http = "https"; }else{ $http = "http"; }
$act = isset($_GET['act']) ? $_GET['act'] : 'recentlyAdded';
$type = isset($_GET['type']) ? $_GET['type'] : 'movie';
$section = ($type == "movie") ? $movies : $tv;
$typeselect = ($type == "movie") ? "Movies" : "TV Shows";
$parent = ($act == "all" && $type == "tv") ? "Directory" : "Video";
$url = "$http://$host/library/sections/$section/$act?X-Plex-Token=$token";
$imgurl = "$http://$host/photo/:/transcode?url=";
$imgurlend = "&width=100&height=100&X-Plex-Token=$token";
$imgurlendhq = "&width=300&height=300&X-Plex-Token=$token";
$achxml = simplexml_load_file($url);

$actarray = array
(
    array("newest","all","recentlyAdded", "recentlyViewed"),
    array("Newest Released $typeselect","All $typeselect","Recently Added $typeselect", "Recently Viewed $typeselect")
);

$title = $actarray[1][array_search($act, $actarray[0])];

if (in_array($act, $actarray[0])) {unset($actarray[0] [array_search($act,$actarray[0] )]);}

?>

<!doctype html>
<html><head>
    <meta charset="utf-8">
    <title><?=$name;?>'s Plex Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <!-- DATA TABLE CSS -->
    <link href="assets/css/table.css" rel="stylesheet">



    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

    <style type="text/css">
        body {
            padding-top: 60px;
        }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

    <!-- Google Fonts call. Font Used Open Sans -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

    <!-- DataTables Initialization -->
    <script type="text/javascript" src="assets/js/datatables/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
            $('#dt1').dataTable();
        } );
    </script>


</head>
<body>

<!-- NAVIGATION MENU -->

<div class="navbar-nav navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar">1</span>
                <span class="icon-bar">2</span>
                <span class="icon-bar">3</span>
            </button>
            <a class="navbar-brand" href="<?=basename(__FILE__);?>"><img src="assets/img/logo30.png" alt=""><?=$name;?>'s Plex Library</a>
        </div>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <h4><?php if($type == "movie"){ echo "Movies | <a href='?act=$act&type=tv' style='color: rgb(0,255,0)'><font color='FF00CC'>TV Shows</font></a>"; }else{ echo "<a href='?act=$act&type=movie'><font color='FF00CC'>Movies</font></a> | TV Shows"; } ?> </h4>
            <h4><strong><?=$title;?> </strong></h4>
            <?php foreach ($actarray[0] as $action) {
                $linktitle = $actarray[1][array_search($action, $actarray[0])];
                echo '<h4><strong><a href="?act='.$action.'&type='.$type.'" style="color: rgb(0,255,0)"><font color="FF00CC">Switch to '.ucfirst($linktitle).'</font></a></strong></h4>';
            }?>


            <table class="display" id="dt1">
                <thead>
                <tr>
                    <th>Poster</th>
                    <th><?=substr($typeselect, 0, -1);?> Name</th>
                    <?php if($type == "tv"){ echo "<th>Episode Name</th>"; }?>
                    <th>Quality</th>
                    <th>Release Date</th>
                    <th>Rating</th>
                    <th>Content Rating</th>
                </tr>
                </thead>
                <tbody>

                <!-- CONTENT -->
                <?php foreach($achxml->$parent AS $child) {
                    $trueimage = ($type == "movie") ? $child['thumb'] : $child['grandparentThumb'];
                    echo '<tr class="gradeA">';
                    echo '<td><center><a href="#myModal'.$child->Media['id'].'" data-toggle="modal"><img src="'.$imgurl.$trueimage.$imgurlend.'"></a></center></td>';
                    if($type == "tv"){ echo '<td>'.$child['grandparentTitle'].'</td>'; }else{ echo '<td>'.$child['title'].'</td>'; }
                    if($type == "tv"){ echo '<td>'.$child['title'].'</td>'; }
                    echo '<td>'.strtoupper($child->Media['videoResolution']).'</td>';
                    echo '<td>'.$child['originallyAvailableAt'].'</td>';
                    echo '<td>'.$child['rating'].'</td>';
                    echo '<td>'.$child['contentRating'].'</td>';
                    echo '</tr>';
                }?>

                </tbody>
            </table><!--/END SECOND TABLE -->
            <?php foreach($achxml->$parent AS $child) {?>
                <div class="modal fade" id="myModal<?=$child->Media['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                                <h4 class="modal-title"><?=$child['title'];?></h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="cont3">
                                            <img src="<?=$imgurl.$child['thumb'].$imgurlendhq;?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="cont3">
                                            <?php if(!$child['tagline']){ echo "<h6>".$child['summary']."</h6>";
                                            }else{ echo "<h4><strong>".$child['tagline']."</strong></h4><h6>".$child['summary']."</h6>";}?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="cont3">
                                            <?php if($child->Media['container']){ echo "<p><ok>Containter:</ok><pre><center>".$child->Media['container']."</center></pre></p>";}?>
                                            <?php if($child->Media['duration']){ echo "<p><ok>Duration:</ok><pre><center>".$child->Media['duration']."</center></pre></p>";}?>
                                            <?php if($child->Media['videoFrameRate']){ echo "<p><ok>Framerate:</ok><pre><center>".$child->Media['videoFrameRate']."</center></pre></p>";}?>
                                            <?php //if($child['addedAt']){ echo "<p><ok>Date Added:</ok><pre><center>".date('M/d/Y', $child['addedAt']/1000)."</center></pre></p>";}?>
                                            <?php if($child['viewCount']){ echo "<p><ok>Times Played:</ok><pre><center>".$child['viewCount']."</center></pre></p>";}?>


                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="cont3">
                                            <?php if($child->Media['width']){ echo "<p><ok>Width:</ok><pre><center>".$child->Media['width']."</center></pre></p>";}?>
                                            <?php if($child->Media['height']){ echo "<p><ok>Height:</ok><pre><center>".$child->Media['height']."</center></pre></p>";}?>
                                            <?php if($child->Media['aspectRatio']){ echo "<p><ok>Aspect Ratio:</ok><pre><center>".$child->Media['aspectRatio']."</center></pre></p>";}?>
                                            <?php if($child->Media['videoCodec']){ echo "<p><ok>Video Codec:</ok><pre><center>".$child->Media['videoCodec']."</center></pre></p>";}?>

                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="cont3">
                                            <?php if($child->Media['audioChannels']){ echo "<p><ok>Audio Channels:</ok><pre><center>".$child->Media['audioChannels']."</center></pre></p>";}?>
                                            <?php if($child->Media['audioCodec']){ echo "<p><ok>Audio Codec:</ok><pre><center>".$child->Media['audioCodec']."</center></pre></p>";}?>
                                            <?php if($child->Media['audioProfile']){ echo "<p><ok>Audio Profile:</ok><pre><center>".$child->Media['audioProfile']."</center></pre></p>";}?>

                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="cont3">
                                            <p><ok>Genre(s):</ok><?php foreach ($child->Genre AS $genre){ echo "<pre word-break='break-word'><center>".$genre['tag']."</center></pre>";}?></p>




                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
            <?}?>

        </div><!--/span12 -->
    </div><!-- /row -->

</div> <!-- /container -->
<br>

<br>
<!-- FOOTER -->
<div id="footerwrap">
    <footer class="clearfix"></footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <p><img src="assets/img/logo.png" alt=""></p>
                <p>Blocks Dashboard Theme - Crafted With Love - Copyright 2013</p>
            </div>

        </div><!-- /row -->
    </div><!-- /container -->
</div><!-- /footerwrap -->


<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript" src="assets/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/js/admin.js"></script>


</body></html>