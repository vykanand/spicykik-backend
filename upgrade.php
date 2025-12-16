<?php
session_start();
include(__DIR__ . '/config.php');

// Helper functions (from module.php)
function hashDirectory($directory)
{
  if (!is_dir($directory)) {
    return false;
  }

  $files = array();
  $dir = dir($directory);

  while (false !== ($file = $dir->read())) {
    if ($file != '.' and $file != '..') {
      if (is_dir($directory . '/' . $file)) {
        $files[] = hashDirectory($directory . '/' . $file);
      } else {
        $files[] = md5_file($directory . '/' . $file);
      }
    }
  }

  $dir->close();

  return md5(implode('', $files));
}

function xcopy($source, $dest, $permissions = 0755)
{
  // Prevent copying into itself
  $source = rtrim($source, "/\\");
  $dest = rtrim($dest, "/\\");
  if ($source === $dest || strpos($dest, $source . DIRECTORY_SEPARATOR) === 0) {
    return false;
  }

  $sourceHash = is_dir($source) ? hashDirectory($source) : false;
  if (is_link($source)) {
    return symlink(readlink($source), $dest);
  }

  if (is_file($source)) {
    if (!is_dir(dirname($dest)))
      mkdir(dirname($dest), $permissions, true);
    return copy($source, $dest);
  }

  if (!is_dir($dest)) {
    if (!mkdir($dest, $permissions, true))
      return false;
  }

  $dir = dir($source);
  while (false !== $entry = $dir->read()) {
    if ($entry == '.' || $entry == '..')
      continue;

    $srcPath = $source . DIRECTORY_SEPARATOR . $entry;
    $dstPath = $dest . DIRECTORY_SEPARATOR . $entry;

    if ($sourceHash !== false && is_dir($srcPath) && $sourceHash == hashDirectory($srcPath)) {
      // skip if hashing indicates same directory (protect against recursion)
      continue;
    }

    if (is_dir($srcPath)) {
      if (!xcopy($srcPath, $dstPath, $permissions)) {
        $dir->close();
        return false;
      }
    } else {
      if (!copy($srcPath, $dstPath)) {
        $dir->close();
        return false;
      }
    }
  }

  $dir->close();
  return true;
}

function rrmdir($dir)
{
  if (!is_dir($dir))
    return;
  $objects = scandir($dir);
  foreach ($objects as $object) {
    if ($object != "." && $object != "..") {
      $path = $dir . DIRECTORY_SEPARATOR . $object;
      if (is_dir($path) && !is_link($path))
        rrmdir($path);
      else
        @unlink($path);
    }
  }
  @rmdir($dir);
}

// API endpoints
if (isset($_GET['action']) && $_GET['action'] === 'list') {
  header('Content-Type: application/json');
  $modules = array();

  $sql = "SELECT id, nav FROM navigation";
  $res = mysqli_query($db, $sql);
  if (!$res) {
    echo json_encode(array('status' => 'error', 'error' => mysqli_error($db)));
    exit;
  }

  while ($row = mysqli_fetch_assoc($res)) {
    $nav = $row['nav'];

    // derive folder from nav only
    $folder = '';
    if (!empty($nav)) {
      $folder = strtolower(preg_replace('/\s+/', '_', $nav));
    }

    // sanitize folder name
    $folder = preg_replace('/[^a-zA-Z0-9_\-]/', '', $folder);

    $path = realpath(__DIR__ . DIRECTORY_SEPARATOR . $folder);
    $exists = ($path !== false && is_dir($path));

    $modules[] = array(
      'id' => $row['id'],
      'nav' => $nav,
      'folder' => $folder,
      'path' => $path ?: __DIR__ . DIRECTORY_SEPARATOR . $folder,
      'exists' => $exists
    );
  }

  echo json_encode(array('status' => 'ok', 'modules' => $modules));
  exit;
}

// List available backup files created by downloader (downloads/backups)
if (isset($_GET['action']) && $_GET['action'] === 'backups') {
  header('Content-Type: application/json');
  $backupDir = __DIR__ . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR . 'backups';
  $list = [];
  if (is_dir($backupDir)) {
    $patterns = ['*.tar', '*.tar.gz', '*.phar'];
    $files = [];
    foreach ($patterns as $p) {
      $found = glob($backupDir . DIRECTORY_SEPARATOR . $p) ?: [];
      foreach ($found as $f)
        $files[] = $f;
    }
    rsort($files);
    foreach ($files as $f) {
      $list[] = ['file' => basename($f), 'path' => $f, 'mtime' => filemtime($f)];
    }
  }
  echo json_encode(['status' => 'ok', 'backups' => $list]);
  exit;
}

// Restore a backup (POST) - extracts the archive into repo root, overwriting files
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'restore') {
  header('Content-Type: application/json');
  $file = isset($_POST['file']) ? basename($_POST['file']) : '';
  if (empty($file)) {
    echo json_encode(['status' => 'error', 'error' => 'Missing file parameter']);
    exit;
  }
  $backupPath = __DIR__ . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $file;
  if (!file_exists($backupPath)) {
    echo json_encode(['status' => 'error', 'error' => 'Backup not found']);
    exit;
  }

  if (!class_exists('PharData')) {
    echo json_encode(['status' => 'error', 'error' => 'PharData not available on server']);
    exit;
  }
  try {
    $phar = new PharData($backupPath);
    $phar->extractTo(__DIR__);
    echo json_encode(['status' => 'ok', 'message' => 'Restored', 'file' => $file]);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => 'Extraction failed: ' . $e->getMessage()]);
  }
  exit;
}

// Delete a backup (POST) - removes the archive file from downloads/backups
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete_backup') {
  header('Content-Type: application/json');
  $file = isset($_POST['file']) ? basename($_POST['file']) : '';
  if (empty($file)) {
    echo json_encode(['status' => 'error', 'error' => 'Missing file parameter']);
    exit;
  }
  $backupPath = __DIR__ . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $file;
  if (!file_exists($backupPath)) {
    echo json_encode(['status' => 'error', 'error' => 'Backup not found']);
    exit;
  }
  try {
    if (!@unlink($backupPath)) {
      echo json_encode(['status' => 'error', 'error' => 'Could not delete backup (permission denied?)']);
      exit;
    }
    echo json_encode(['status' => 'ok', 'message' => 'Deleted', 'file' => $file]);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => 'Delete failed: ' . $e->getMessage()]);
  }
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'copy') {
  header('Content-Type: application/json');
  $folder = isset($_POST['folder']) ? $_POST['folder'] : '';
  $folder = preg_replace('/[^a-zA-Z0-9_\-]/', '', $folder);
  if (empty($folder)) {
    echo json_encode(array('status' => 'error', 'error' => 'Invalid folder name'));
    exit;
  }

  $modulePath = __DIR__ . DIRECTORY_SEPARATOR . $folder;
  if (!is_dir($modulePath)) {
    echo json_encode(array('status' => 'error', 'error' => 'Module folder not found', 'folder' => $folder, 'path' => $modulePath));
    exit;
  }

  $src = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'custom');
  if ($src === false || !is_dir($src)) {
    echo json_encode(array('status' => 'error', 'error' => 'Source custom folder not found', 'path' => $src));
    exit;
  }

  // Copy contents of ./custom INTO the module folder (overwrite existing files)
  $dest = $modulePath;

  // Do NOT remove the module folder; we only overwrite/add files from the custom folder
  $ok = xcopy($src, $dest);
  if ($ok) {
    echo json_encode(array('status' => 'ok', 'message' => 'Copied', 'folder' => $folder, 'path' => $dest));
  } else {
    echo json_encode(array('status' => 'error', 'error' => 'Copy failed', 'folder' => $folder, 'path' => $dest));
  }
  exit;
}

// If not API, render the GUI page
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Upgrade / Sync Modules</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      margin: 20px
    }

    table {
      border-collapse: collapse;
      width: 100%
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px
    }

    th {
      background: #f5f5f5
    }

    .pending {
      color: #888
    }

    .ok {
      color: green
    }

    .err {
      color: red
    }

    .spinner {
      animation: spin 1s linear infinite;
      display: inline-block;
      border: 2px solid #ccc;
      border-top: 2px solid #333;
      border-radius: 50%;
      width: 14px;
      height: 14px
    }

    @keyframes spin {
      to {
        transform: rotate(360deg)
      }
    }

    .progress {
      height: 18px;
      background: #eee;
      border-radius: 9px;
      overflow: hidden
    }

    .progress>.bar {
      height: 100%;
      background: #4caf50;
      width: 0%
    }

    .log {
      background: #111;
      color: #fff;
      padding: 8px;
      height: 120px;
      overflow: auto;
      white-space: pre-wrap;
      font-family: monospace;
      font-size: 12px
    }

    .btn {
      padding: 8px 12px;
      border-radius: 4px;
      background: #456B95;
      color: #fff;
      border: none;
      cursor: pointer
    }
  </style>
</head>

<body>
  <h2>Upgrade</h2>
  <p>This tool upgrades and syncs your software to the latest version.</p>

  <div style="margin-bottom:12px">
    <button id="btnRefresh" class="btn">Refresh list</button>
    <button id="btnStart" class="btn" disabled style="opacity:0.6;cursor:not-allowed;margin-left:8px">Start Sync
      (disabled)</button>
  </div>

  <div class="progress" style="margin-bottom:8px">
    <div class="bar" id="overallBar"></div>
  </div>

  <table id="tblModules">
    <thead>
      <tr>
        <th><input type="checkbox" id="hdrCheck" disabled></th>
        <th>#</th>
        <th>Nav</th>
        <th>Folder</th>
        <th>Path</th>
        <th>Exists</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <h3>Log</h3>
  <div class="log" id="log"></div>
  <h4>Downloader Diagnostics</h4>
  <div style="background:#222;color:#fff;padding:8px;border-radius:4px;margin-top:8px;font-family:monospace">
    <div id="downloaderDiag" style="white-space:pre-wrap;max-height:200px;overflow:auto;color:#9ae;border:inherit">
    </div>
  </div>

  <h4>Backups</h4>
  <div style="margin-top:8px;margin-bottom:12px">
    <select id="backupList" style="width:420px;padding:6px"></select>
    <button id="btnRefreshBackups" class="btn" style="margin-left:8px">Refresh</button>
    <button id="btnRestore" class="btn" style="margin-left:8px">Restore Selected</button>
    <button id="btnDeleteBackup" class="btn" style="margin-left:8px;background:#d9534f">Delete Selected</button>
  </div>

  <script>
    // Client-side downloader pre-check (tries ping/info and extensionless '/downloader' first)

    function showDiag(msg) {
      $('#downloaderDiag').text(msg);
    }

    // Load backups list - exposed globally so other functions can refresh it
    function loadBackups() {
      $('#backupList').html('<option>Loading...</option>');
      $.getJSON('upgrade.php?action=backups', function (resp) {
        if (!resp || resp.status !== 'ok') { $('#backupList').html('<option>No backups available</option>'); return; }
        if (!resp.backups || resp.backups.length === 0) { $('#backupList').html('<option>No backups available</option>'); return; }
        var opts = '';
        resp.backups.forEach(function (b) { opts += '<option value="' + b.file + '">' + b.file + ' (' + new Date(b.mtime * 1000).toLocaleString() + ')</option>'; });
        $('#backupList').html(opts);
      }).fail(function () { $('#backupList').html('<option>Error loading backups</option>'); });
    }

    function downloaderPrecheck() {
      // downloader runs on the same instance; always use current origin
      var bases = [window.location.origin];

      // For each base, try '/downloader?action=ping', '/downloader?action=info', '/downloader', '/downloader.php'
      var candidates = [];
      bases.forEach(function (b) { b = b.replace(/\/$/, ''); candidates.push(b + '/downloader?action=ping'); candidates.push(b + '/downloader?action=info'); candidates.push(b + '/downloader'); candidates.push(b + '/downloader.php'); });

      $('#btnStart').prop('disabled', true).text('Start Sync (checking...)');
      showDiag('');

      var i = 0;
      function tryOne() {
        if (i >= candidates.length) {
          log('Downloader pre-check failed: no valid endpoint returned JSON OK');
          $('#btnStart').prop('disabled', true).text('Start Sync (disabled)');
          return;
        }
        var url = candidates[i++];
        log('Checking downloader...');
        $.ajax({ url: url, method: 'GET', dataType: 'json', timeout: 10000 })
          .done(function (resp) {
            showDiag(JSON.stringify(resp, null, 2));
            if (resp && resp.status === 'ok') {
              log('Downloader pre-check OK');
              // If this was a ping/info URL, trigger the full downloader start automatically
              if (url.indexOf('action=ping') !== -1 || url.indexOf('action=info') !== -1) {
                // derive start URL and start downloader
                var startUrl = url.replace(/action=(ping|info)/, 'action=start');
                callDownloaderStart(startUrl);
              } else {
                // endpoint itself returned OK (maybe already start) – enable syncing
                enableStartSync();
              }
            } else {
              var err = resp && resp.error ? resp.error : 'Invalid/empty JSON response';
              log('Downloader pre-check failed: ' + err);
              if (resp && resp.details) log('Details: ' + JSON.stringify(resp.details));
              tryOne();
            }
          })
          .fail(function (xhr, status, err) {
            var snippet = xhr.responseText ? xhr.responseText.substring(0, 2000) : '';
            log('Downloader pre-check HTTP error: ' + (xhr.status || status) + ' - ' + err);
            if (snippet) log('Response snippet: ' + snippet);
            showDiag(snippet || ('HTTP error: ' + (xhr.status || status)));
            if (/\bcannot open \?php|Syntax error|Permission denied|not found|\r\n/.test(snippet)) {
              log('Suggestion: the downloader endpoint returned non-JSON output. Ensure PHP is served by your webserver (check handler, permissions and EOLs).');
            }
            tryOne();
          });
      }
      tryOne();
    }

    function callDownloaderStart(startUrl) {
      if (!startUrl) return;
      log('Starting downloader...');
      showDiag('Starting downloader...');
      // disable the Start button while downloader runs and show running state
      $('#btnStart').prop('disabled', true).text('Running...').css({ 'background': '#999' });
      $.ajax({ url: startUrl, method: 'GET', dataType: 'json', timeout: 120000 })
        .done(function (resp) {
          showDiag(JSON.stringify(resp, null, 2));
          if (resp && resp.status === 'ok') {
            log('Downloader completed: upgrade downloaded and extracted.');
            if (resp.zip) log('Downloaded: ' + resp.zip);
            if (resp.extracted) log('Extracted to: ' + resp.extracted);
            if (resp.copied) {
              if (resp.copied.custom && resp.copied.custom.length) log('Files copied to custom: ' + resp.copied.custom.length);
              if (resp.copied.appmarket && resp.copied.appmarket.length) log('Files copied to appmarket: ' + resp.copied.appmarket.length);
            }
            // enable Start Sync (green)
            enableStartSync();
            try { loadBackups(); } catch (e) { /* ignore */ }
          } else {
            var err = resp && resp.error ? resp.error : 'Downloader returned error';
            log('Downloader error: ' + err);
            if (resp && resp.details) log('Details: ' + JSON.stringify(resp.details));
            // re-enable Start button so user can retry
            $('#btnStart').prop('disabled', false).text('Start Sync (retry)');
          }
        }).fail(function (xhr, status, err) {
          var snippet = xhr.responseText ? xhr.responseText.substring(0, 2000) : '';
          log('Downloader request failed: ' + (xhr.status || status) + ' - ' + err);
          if (snippet) log('Response: ' + snippet);
          showDiag('Downloader request failed:\n' + (snippet || (xhr.status || status)));
          // re-enable Start button on failure
          $('#btnStart').prop('disabled', false).text('Start Sync (retry)');
        });
    }

    function enableStartSync() {
      var $btn = $('#btnStart');
      $btn.prop('disabled', false);
      // remove visual disabled cues set inline earlier
      $btn.css({ 'background': '#28a745', 'opacity': '1', 'cursor': 'pointer' });
      $btn.text('Start Sync');
      $btn.show();
      log('Ready to start module sync.');
    }
    function log(msg) {
      // Support multi-line messages: prefix each line with a timestamp
      try {
        var s = String(msg || '');
        var lines = s.split(/\r?\n/);
        var out = '';
        for (var i = 0; i < lines.length; i++) {
          var line = lines[i];
          if (line === '') continue;
          var d = new Date().toLocaleString();
          out += '[' + d + '] ' + line + '\n';
        }
        if (out) {
          $('#log').append(out);
          $('#log').scrollTop($('#log')[0].scrollHeight);
        }
      } catch (e) {
        // fallback
        var d2 = new Date().toLocaleString();
        $('#log').append('[' + d2 + '] ' + msg + '\n');
        $('#log').scrollTop($('#log')[0].scrollHeight);
      }
    }

    function showCacheNotice() {
      var msg = 'Upgrade complete — please clear your browser cache to ensure the latest files are loaded.';
      // Try parent notie if present
      try {
        if (window.parent && window.parent.notie) {
          window.parent.notie.alert({ type: 'info', text: msg, time: 8 });
          return;
        }
      } catch (e) { /* ignore */ }

      // Fallback: show an inline dismissible banner
      if ($('#cacheNotice').length === 0) {
        var banner = '<div id="cacheNotice" style="position:fixed;left:20px;right:20px;bottom:90px;background:#fffae6;border:1px solid #ffd24d;padding:12px;border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,0.15);z-index:9999;display:flex;align-items:center;justify-content:space-between">'
          + '<div style="color:#333;font-size:14px">' + msg + '</div>'
          + '<div><button id="dismissCacheNotice" style="margin-left:12px;padding:6px 10px;border-radius:4px;border:none;background:#456B95;color:#fff;cursor:pointer">Dismiss</button></div>'
          + '</div>';
        $('body').append(banner);
        $('#dismissCacheNotice').on('click', function () { $('#cacheNotice').remove(); });
      }
    }

    function refreshList() {
      $('#tblModules tbody').html('<tr><td colspan="7">Loading...</td></tr>');
      $.getJSON('upgrade.php?action=list', function (resp) {
        if (!resp || resp.status !== 'ok') {
          $('#tblModules tbody').html('<tr><td colspan="6">Error: ' + (resp && resp.error ? resp.error : 'Unknown') + '</td></tr>');
          log('List error: ' + (resp && resp.error ? resp.error : 'Unknown'));
          return;
        }
        var rows = '';
        resp.modules.forEach(function (m, i) {
          rows += '<tr data-folder="' + m.folder + '">';
          rows += '<td><input type="checkbox" class="moduleCheck" checked></td>';
          rows += '<td>' + (i + 1) + '</td>';
          rows += '<td>' + escapeHtml(m.nav) + '</td>';
          rows += '<td>' + escapeHtml(m.folder) + '</td>';
          rows += '<td>' + escapeHtml(m.path) + '</td>';
          rows += '<td>' + (m.exists ? '<span class="ok">Yes</span>' : '<span class="err">No</span>') + '</td>';
          rows += '<td class="status pending">Pending</td>';
          rows += '</tr>';
        });
        $('#tblModules tbody').html(rows);
        // default select all: check header and module boxes
        $('#hdrCheck').prop('disabled', false).prop('checked', true);
        $('.moduleCheck').prop('checked', true);
        log('Detected ' + resp.modules.length + ' modules.');
      }).fail(function (xhr) {
        $('#tblModules tbody').html('<tr><td colspan="7">Request failed: ' + xhr.status + '</td></tr>');
        log('List request failed: ' + xhr.status + ' - ' + xhr.responseText.substring(0, 200));
      });
    }

    function escapeHtml(s) { if (!s) return ''; return $('<div/>').text(s).html(); }

    function startSync() {
      var rows = $('#tblModules tbody tr').filter(function () { return $(this).find('.moduleCheck').prop('checked'); });
      if (rows.length === 0) { log('No modules selected. Refresh list and select modules.'); return; }
      var total = rows.length; var done = 0;
      $('#btnStart').prop('disabled', true);

      function next(i) {
        if (i >= total) {
          log('Sync completed.');
          $('#btnStart').prop('disabled', false);
          // notify user to clear browser cache
          try { showCacheNotice(); } catch (e) { }
          return;
        }
        var tr = $(rows[i]);
        var folder = tr.data('folder');
        tr.find('.status').html('<span class="spinner"></span> Copying...');
        log('Copying to ' + folder + '...');

        $.ajax({ url: 'upgrade.php?action=copy', method: 'POST', data: { folder: folder }, dataType: 'json' })
          .done(function (resp) {
            if (resp && resp.status === 'ok') {
              tr.find('.status').html('<span class="ok">✔</span> Copied');
              log('Copied to ' + folder + ' -> ' + resp.path);
            } else {
              tr.find('.status').html('<span class="err">✖</span> Error: ' + (resp && resp.error ? resp.error : 'Unknown'));
              log('Error copying to ' + folder + ': ' + (resp && resp.error ? resp.error : 'Unknown'));
            }
          }).fail(function (xhr) {
            tr.find('.status').html('<span class="err">✖</span> HTTP ' + xhr.status);
            var snippet = xhr.responseText ? xhr.responseText.substring(0, 500) : '';
            log('HTTP ' + xhr.status + ' copying to ' + folder + ': ' + snippet);
          }).always(function () {
            done++; var pct = Math.round((done / total) * 100);
            $('#overallBar').css('width', pct + '%');
            next(i + 1);
          });
      }

      next(0);
    }

    $(function () {
      // Scroll to the top on page load for better UX
      try { window.scrollTo(0, 0); } catch (e) { }
      $('#btnRefresh').click(refreshList);
      // simple confirmation for upgrade
      $('#btnStart').click(function () { if (confirm('Upgrade now?')) startSync(); });
      // Run precheck on load (use the same instance's downloader)
      downloaderPrecheck();
      // load backups list
      function loadBackups() {
        $('#backupList').html('<option>Loading...</option>');
        $.getJSON('upgrade.php?action=backups', function (resp) {
          if (!resp || resp.status !== 'ok') { $('#backupList').html('<option>No backups available</option>'); return; }
          if (!resp.backups || resp.backups.length === 0) {
            $('#backupList').html('<option>No backups available</option>');
            return;
          }
          var opts = '';
          resp.backups.forEach(function (b) { opts += '<option value="' + b.file + '">' + b.file + ' (' + new Date(b.mtime * 1000).toLocaleString() + ')</option>'; });
          $('#backupList').html(opts);
        }).fail(function () { $('#backupList').html('<option>Error loading backups</option>'); });
      }
      loadBackups();
      $('#btnRefreshBackups').click(loadBackups);
      $('#btnRestore').click(function () {
        var f = $('#backupList').val();
        if (!f) return alert('Select a backup');
        if (!confirm('Restore backup ' + f + '? This will overwrite current files.')) return;
        $('#btnRestore').prop('disabled', true).text('Restoring...');
        $.post('upgrade.php?action=restore', { file: f }, function (resp) {
          if (resp && resp.status === 'ok') { log('Restore complete: ' + resp.file); alert('Restore complete'); location.reload(); }
          else { log('Restore error: ' + (resp && resp.error ? resp.error : 'Unknown')); alert('Restore failed: ' + (resp && resp.error ? resp.error : 'Unknown')); }
        }, 'json').fail(function (xhr) { log('Restore request failed: ' + xhr.status); alert('Restore failed'); }).always(function () { $('#btnRestore').prop('disabled', false).text('Restore Selected'); });
      });
      $('#btnDeleteBackup').click(function () {
        var f = $('#backupList').val();
        if (!f) return alert('Select a backup to delete');
        if (!confirm('Delete backup ' + f + '? This cannot be undone.')) return;
        $('#btnDeleteBackup').prop('disabled', true).text('Deleting...');
        $.post('upgrade.php?action=delete_backup', { file: f }, function (resp) {
          if (resp && resp.status === 'ok') {
            log('Deleted backup: ' + resp.file);
            loadBackups();
            alert('Backup deleted: ' + resp.file);
          } else {
            log('Delete error: ' + (resp && resp.error ? resp.error : 'Unknown'));
            alert('Delete failed: ' + (resp && resp.error ? resp.error : 'Unknown'));
          }
        }, 'json').fail(function (xhr) { log('Delete request failed: ' + xhr.status); alert('Delete failed'); }).always(function () { $('#btnDeleteBackup').prop('disabled', false).text('Delete Selected'); });
      });
      // header checkbox toggles selection
      $('#hdrCheck').change(function () { var v = $(this).prop('checked'); $('.moduleCheck').prop('checked', v); });
      // update header checkbox when individual checks change
      $(document).on('change', '.moduleCheck', function () {
        var all = $('.moduleCheck').length > 0 && $('.moduleCheck').length === $('.moduleCheck:checked').length;
        $('#hdrCheck').prop('checked', all);
      });
      refreshList();
    });
  </script>
</body>

</html>