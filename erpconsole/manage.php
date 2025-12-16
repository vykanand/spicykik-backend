<?php
session_start();

include('../config.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>dyn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" type="text/css" href="../assets/css/notie.min.css">
  <style>
    /* override styles here */
    .notie-container {
      box-shadow: none;
    }
  </style>

  <link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,600'>
  <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="../loader.css">


</head>

<body>
  <!-- Global loader overlay (shown when navigating to another page) -->
  <div id="global-loader" class="loadmodal" style="display:none;">
    <div class="loading" style="z-index: 999; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
    </div>
  </div>
  <!-- partial:index.partial.html -->
  <div class="cont_principal">
    <div class="cont_centrar">

      <div class="cont_todo_list_top">
        <div class="cont_titulo_cont">
          <h3>Manage Apps</h3>

        </div>
        <div class="cont_add_titulo_cont"><a onclick="gburl()" style="cursor: pointer;"><i
              class="material-icons">&#xE145;</i></a>
        </div>

      </div>


      <div class="cont_princ_lists">
        <ul id="sortable-1">



          <?php

          $sql = "SELECT * FROM navigation";
          $result = mysqli_query($db, $sql) or die(mysqli_error($db));


          while ($row = mysqli_fetch_array($result)) {

            ?>



            <li class="list_dsp_true list_shopping list_dsp_none li_num_0" draggable="true" style="display: block;"
              data-module="<?= htmlspecialchars($row['nav']) ?>">
              <div class="col_md_1_list">
                <p>APP</p>
              </div>
              <div class="col_md_2_list">
                <h4><?= $row['nav']; ?></h4>
                <p style="color: green">Running</p>
              </div>
              <div class="col_md_3_list">
                <div class="cont_text_date">
                  <p>ACTIONS</p>
                </div>
                <div class="cont_btns_options">
                  <ul>
                    <li><a href="../pluginmap.php?module=<?= urlencode($row['nav']); ?>" title="Plugin Mapping"><i
                          class="material-icons">extension</i></a>
                    </li>
                    <li><a href="#finish" onclick="finish_action(0,0,'<?= $row['nav']; ?>');"><i
                          class="material-icons">delete</i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>

            <?php

          }

          ?>

        </ul>
      </div>


      <!--   End cont_central  -->
    </div>

  </div>


  <!-- partial -->
  <script src="scriptmanage.js"></script>
  <script type="text/javascript">
    function gburl() {
      console.log();
      var URL = window.location.href
      window.location.href = URL.substring(0, URL.lastIndexOf("/") + 1) + 'create';
    }

    $(document).ready(function () {
      var body = document.body,
        html = document.documentElement;

      var height = Math.max(body.scrollHeight, body.offsetHeight,
        html.clientHeight, html.scrollHeight, html.offsetHeight);
      window.top.postMessage(height + 500, '*');

    })

    // show loader when clicking plugin mapping links and persist flag until target page fully loads
    $(document).on('click', 'a[href*="pluginmap.php"]', function (e) {
      var href = $(this).attr('href');
      if (!href) return;
      // show overlay
      $('#global-loader').show();
      try {
        localStorage.setItem('showLoaderUntilPageLoad', '1');
      } catch (err) {
        // ignore storage errors
      }
      // navigate (prevent default to ensure overlay shows first)
      e.preventDefault();
      // small timeout lets overlay render before navigation
      setTimeout(function () { window.location.href = href; }, 30);
    });

    // Clicking the module list item should open its plugin mapping page
    // Ignore clicks on action buttons/links inside the list item
    $(document).on('click', '.cont_princ_lists li', function (e) {
      // if click originated from an actionable element, skip
      if ($(e.target).closest('.cont_btns_options, a, button, input, .material-icons').length) return;
      var module = $(this).data('module');
      if (!module) return;
      var href = '../pluginmap.php?module=' + encodeURIComponent(module);
      // show overlay and set flag like other navigation
      $('#global-loader').show();
      try { localStorage.setItem('showLoaderUntilPageLoad', '1'); } catch (err) { }
      e.preventDefault();
      setTimeout(function () { window.location.href = href; }, 30);
    });
  </script>

  <script type="text/javascript" src="../assets/js/notie.min.js"></script>


  <style type="text/css">
    /* Ensure action icons align horizontally and don't stack on hover */
    .cont_btns_options ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 4px;
      /* tighter spacing */
      align-items: center;
    }

    .cont_btns_options ul li {
      display: inline-block;
      margin: 0;
      padding: 0;
    }

    .cont_btns_options ul li a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      /* smaller square */
      height: 30px;
      line-height: 30px;
      font-size: 20px;
      /* icon size */
      color: #333;
      text-decoration: none;
      border-radius: 4px;
      padding: 0;
      /* ensure no extra space */
    }

    .cont_btns_options ul li a i,
    .cont_btns_options ul li a .material-icons {
      font-size: 20px;
      /* ensure icon fits */
      line-height: 20px;
    }

    .cont_btns_options ul li a:hover {
      background: rgba(0, 0, 0, 0.05);
    }

    /* Make module rows show pointer and subtle hover feedback */
    .cont_princ_lists li {
      cursor: pointer;
      transition: background-color 0.12s ease;
    }

    .cont_princ_lists li:hover {
      background-color: rgba(0, 0, 0, 0.03);
    }

    @media only screen and (min-width: 600px) {
      .floatUpgrade {
        display: flex;
        position: fixed;
        width: auto;
        min-width: 140px;
        max-width: 240px;
        height: 46px;
        bottom: 10px;
        right: 24px;
        background-color: #456B95;
        color: #FFF;
        border-radius: 5px;
        align-items: center;
        padding: 6px 10px;
        gap: 8px;
        box-shadow: 2px 2px 3px #999;
        text-decoration: none !important;
      }
    }

    @media only screen and (max-width: 600px) {
      .floatUpgrade {
        display: flex;
        position: fixed;
        width: auto;
        min-width: 120px;
        height: 42px;
        bottom: 8px;
        right: 8px;
        background-color: #456B95;
        color: #FFF;
        border-radius: 5px;
        align-items: center;
        padding: 6px 10px;
        gap: 6px;
        box-shadow: 2px 2px 3px #999;
        text-decoration: none !important;
      }
    }

    /* Float button inner layout */
    .floatUpgrade .label {
      color: #fff;
      font-size: 13px;
      margin: 0;
      line-height: 1;
      font-weight: 600;
    }

    .floatUpgrade img {
      height: 24px;
      width: 24px;
      margin: 0;
      display: block;
    }

    .my-float {
      margin-top: 0
    }
  </style>

  <a href="/upgrade.php" class="floatUpgrade">
    <img src="sync.png" alt="Sync">
    <span class="label">Upgrade</span>
  </a>

</body>

</html>