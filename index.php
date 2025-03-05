<?php
error_reporting(E_ALL);

// ini_set('max_execution_time', 18000);
// ini_set("memory_limit","202M");
// ini_set("time_limit","5555");
// ini_set("post_max_size","2001M");
// ini_set("upload_max_filesize","2000M");

session_start();

include_once(__DIR__ . "/php/config.php");
include_once(__DIR__ . "/includes/dbClass.php");

define("DB_HOST", IP_ADDR);
define("DB_USER", USER_NAME);
define("DB_PASSWORD", USER_PASS);
define("DB_CHARSET", "utf8");

$db = new dbClass();


// $_SESSION['User']['ID'] = 1;

// $info = $db->db_select("objects",array("*"),"WHERE user_id='".$_SESSION['User']['ID']."'");
$info = $db->db_select("objects", array("*"));

$thumb_path = "";
$obj_html   = "";
$my_objs    = array();

if ($info && count($info) > 0) {
    foreach ($info as $eachInfo) {
        $thumb_path = "objs/" . $eachInfo->name . "/";
        $obj_html .= '<li name="' . $eachInfo->name . '" info="' . $eachInfo->id . '" tool="image" twod="' . $eachInfo->two_obj . '" title="' . $eachInfo->descr . '" thrd="' . $eachInfo->three_obj . '" size="' . $eachInfo->size . '">';
        $obj_html .= '<img src="' . $thumb_path . $eachInfo->thumb_img . '" /></li>';

        array_push($my_objs, $eachInfo->name);
    }
}

$all_obj    = $db->db_select("objects", array("*"));
$all_html   = "";

if ($all_obj && count($all_obj) > 0) {
    foreach ($all_obj as $eachInfo) {
        $thumb_path = "objs/" . $eachInfo->name . "/";

        if (in_array($eachInfo->name, $my_objs)) continue;

        $all_html .= '<tr>';
        // $all_html .= '<td><input type="checkbox" info="'.$eachInfo->id.'"></td>';
        // $all_html .= '<td>'.$eachInfo->name.'</td>';
        // $all_html .= '<td><img src="'.$thumb_path.$eachInfo->thumb_img.'" /></td>';
        $all_html .= '</tr>';
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>3D Floor Plan Editing Tool</title>

    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <link rel="stylesheet" type="text/css" href="style/overlay.css" />
    <link rel="stylesheet" type="text/css" href="style/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="style/colorpicker.css" />
</head>

<script type="text/javascript" src="js/library/jquery.min.js"></script>
<script type="text/javascript" src="js/library/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/library/fabric.min.js"></script>
<script type="text/javascript" src="js/library/colorpicker.js"></script>

<script type="text/javascript" src="js/library/Three.js"></script>
<script type="text/javascript" src="js/library/Detector.js"></script>
<script type="text/javascript" src="js/library/Stats.js"></script>

<script type="text/javascript" src="js/library/MTLLoader.js"></script>
<script type="text/javascript" src="js/library/OBJMTLLoader.js"></script>
<script type="text/javascript" src="js/library/OrbitControls.js"></script>

<script src="js/library/file_upload/jquery.iframe-transport.js"></script>
<script src="js/library/file_upload/jquery.ui.widget.js"></script>
<script src="js/library/file_upload/jquery.fileupload.js"></script>

<script type="text/javascript" src="js/mine/common/addon.js"></script>
<script type="text/javascript" src="js/mine/common/main.js"></script>
<script type="text/javascript" src="js/mine/common/event.js"></script>
<script type="text/javascript" src="js/mine/common/popup.js"></script>
<script type="text/javascript" src="js/mine/drawing/draw.js"></script>
<script type="text/javascript" src="js/mine/3dmode/3ds.js"></script>

<body>

    <div class="nabvar">
        <header class="header">
            <a href="<?= 'http://' . $_SERVER['HTTP_HOST'] ?>">
                <img src="img/logo.png">
            </a>
        </header>

        <div id="top_area">
            <div id="text_area">
                <img src="./img2/new/arrow.png" alt="">
                <div></div>
                <p>File</p>
                <p>Edit</p>
                <p>Properties</p>
                <p>Windows</p>
                <p>Help</p>
            </div>
            <div id="ctrl_area">
                <div></div>
                <ul>
                    <li><img src="./img2/new/icon1.png" alt=""></li>
                    <li>
                        <img src="./img2/new/icon2.png" alt="">
                        <img src="./img2/new/icon3.png" alt="">
                    </li>
                    <li><img src="./img2/new/icon4.png" alt=""></li>
                    <li id="parent">
                        <img id="folder1" src="./img2/new/icon5.png" alt="">
                        <img id="folder2" src="./img2/new/icon6.png" alt="">
                    </li>
                    <li><img src="./img2/new/icon7.png" alt=""></li>
                    <li><img src="./img2/new/icon8.png" alt=""></li>
                    <li><img src="./img2/new/icon9.png" alt=""></li>
                    <li id="parent">
                        <img src="./img2/new/icon10.png" alt="">
                        <img id="paste" src="./img2/new/icon10.png" alt="">
                    </li>
                    <li><img src="./img2/new/icon11.png" alt=""></li>
                    <li><img src="./img2/new/icon12.png" alt=""></li>
                    <li><img src="./img2/new/icon13.png" alt=""></li>
                </ul>
            </div>
            <div id="slider_area">
                <div class="slider-wrapper">
                    <img class="img_minus" src="./img2/new/icon14.png" alt="" style="cursor: pointer;">
                    <input type="range" min="10" max="100" value="16" class="slider" id="scaleSlider" aria-labelledby="scaleValue">
                    <img class="img_plus" src="./img2/new/icon15.png" alt="" style="cursor: pointer;">
                    <div></div>
                </div>
                <div id="percent1">
                    <span id="scaleValue">16%</span>
                    <div></div>
                </div>
            </div>
            <div id="last_item">
                <img src="./img2/new/icon9.png" alt="">
            </div>
        </div>
    </div>

    <div id="overlay"></div>
    <div id="over_overlay">
        <?php require_once(__DIR__ . "/theme/overlay.html"); ?>
    </div>

    <div id="area_2d">

        <div id="container">
            <div id="left_container">
                <div id="work_container">
                    <div id="tool_area">
                        <div class="control_item" class="ctrlitem" style="margin-top: 16px;"><img src="./img2/new/icon16.svg" alt=""></div>
                        <div class="control_item"><img src="./img2/new/icon17.svg" alt=""></div>
                        <div class="control_item active"><img src="./img2/new/icon18.png" alt=""></div>
                        <div class="control_item"><img src="./img2/new/icon19.svg" alt=""></div>
                        <div class="control_item"><img src="./img2/new/icon20.svg" alt=""></div>
                        <div class="control_item" tool="text"><img src="./img2/new/icon21.svg" alt=""></div>
                        <div class="control_item" tool="line"><img src="./img2/new/icon22.svg" alt=""></div>
                        <div class="control_item" id="shape">

                            <img class="retangular" src="./img2/new/icon23.svg" alt="">

                            <img class="circle" src="./img2/new/icon24.svg" alt="">

                            <dd id="all_shapes">
                                <dl tool="rect"><img src="img/icon_rectangle.png"></dl>

                                <dl tool="triangle"><img src="img/icon_triangle.png"></dl>

                                <dl tool="circle"><img src="img/icon_circle.png"></dl>

                                <dl tool="star"><img src="img/icon_star.png"></dl>

                                <dl tool="ellipse"><img src="img/icon_ellipse.png"></dl>
                            </dd>
                        </div>
                        <div class="control_item"><img src="./img2/new/icon25.svg" alt=""></div>
                    </div>
                    <div id="canvas_area">
                        <font color="red" id="font_protitle"></font>
                        <center>Move here</center>
                        <canvas id="canvas"></canvas>
                        <div id="grid_bg"></div>
                    </div>
                </div>
            </div>
        </div>

        <footer id="footer">
            <div class="object">
                <div class="add-new-project">
                    <div class="add-new-project-header">
                        <img src="./img2/new/icon15.png" alt="">
                        <a id="btn_add_obj">Add new Object</a>
                        <div></div>
                    </div>
                    <div class="add-new-project-content">
                        <div class="object-item">
                            <img src="./img2/new/icon14.png" alt="">
                            <a id="btn_add_obj">Remove Object</a>
                        </div>
                    </div>
                </div>
                <div class="select-object">
                    <div class="select-object-header">
                        <span>Please select object</span>
                    </div>
                    <div id="left_area">
                        <div class="category" id="floorArea">
                            <ul>
                                <?php echo $obj_html; ?>
                                </li>
                            </ul>
                            <div class="clear_both"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-content">
                <label for="">© 2025 Getty Images. The Getty Images design is a trademark of Getty Images.</label>
            </div>
        </footer>

        <div id="right_area">
            <span>Properties</span>
            <?php require_once("theme/property.html"); ?>
        </div>
    </div>

    <div id="area_3d">
        <img src="./img2/new/icon9.png" id="img_close3d">
        <img src="img/btn_3d_export.png" id="export_3d">
    </div>
</body>

<script>
    const items = document.querySelectorAll(".control_item");

    items.forEach(item => {
        item.addEventListener("click", () => {

            if (item.id == "shape") {
                document.getElementById("all_shapes").style.display = "flex";
            } else document.getElementById("all_shapes").style.display = "none";

            items.forEach(i => i.classList.remove("active"));

            item.classList.add("active");
        });
    });

    const scaleSlider = document.getElementById("scaleSlider");
    const scaleValue = document.getElementById("scaleValue");
    const gridSlider = document.getElementById("gridSlider");
    const gridValue = document.getElementById("gridValue");
    const depthSlider = document.getElementById("depthSlider");
    const depthValue = document.getElementById("depthValue");
    const presetButtons = document.querySelectorAll(".preset-btn");

    // Function to update the slider background color dynamically
    function updateSliderBackground(slider) {
        const value = (slider.value - slider.min) / (slider.max - slider.min) * 100;
        const thumbColor = `#007bff`; // Color from red to green
    }

    // Update scale value and slider background color
    scaleSlider.addEventListener("input", function() {
        scaleValue.textContent = this.value + "%";
        updateSliderBackground(scaleSlider);
    });

    // Update grid value
    gridSlider.addEventListener("input", function() {
        gridValue.textContent = this.value;
        document.getElementById("txt_fwidth").value = this.value;
        presetButtons.forEach(btn => btn.classList.remove("active"));
    });

    depthSlider.addEventListener("input", function() {
        depthValue.textContent = this.value;
        document.getElementById("txt_fdepth").value = this.value;
        presetButtons.forEach(btn => btn.classList.remove("active"));
    })

    // Preset buttons functionality
    presetButtons.forEach(button => {
        button.addEventListener("click", function() {
            let value = this.getAttribute("data-value");
            gridSlider.value = value;
            gridValue.textContent = value;

            presetButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // Initialize the background color of the slider based on the initial value
    updateSliderBackground(scaleSlider);
</script>