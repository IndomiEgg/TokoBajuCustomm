<?php
$base = 'http://localhost:8080';
$cookie = sys_get_temp_dir() . '/batom_cookie.txt';
@unlink($cookie);

function http($url, $post = null, $cookie = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    // separate headers and body
    $hdrLen = $info['header_size'];
    $headers = substr($resp, 0, $hdrLen);
    $body = substr($resp, $hdrLen);
    return ['info'=>$info, 'headers'=>$headers, 'body'=>$body];
}

// 1. GET login page to retrieve CSRF
$r = http($base . '/admin/login', null, $cookie);
$loginHtml = $r['body'];
$csrfName = null; $csrfValue = null;
if (preg_match('/<input[^>]*name="([^"]*csrf[^"]*)"[^>]*value="([^"]*)"/i', $loginHtml, $m)) {
    $csrfName = $m[1]; $csrfValue = $m[2];
}

// 2. POST login
$post = [
    'email' => 'admin@batom.studio',
    'password' => 'admin123'
];
if ($csrfName) $post[$csrfName] = $csrfValue;
$respLogin = http($base . '/admin/login', $post, $cookie);
// capture any flash or redirect location

// 3. GET status panel
$panel = http($base . '/admin/dashboard/panel/status', null, $cookie);
$panelHtml = $panel['body'];

// parse counts
$counts = [];
if (preg_match_all('/<div[^>]*class="status-count-card"[\s\S]*?<strong>([0-9]+)<\/strong>/i', $panelHtml, $mc)) {
    // order: paid, unpaid, pending, in_progress, processing, delivering, finished, cancelled
    $keys = ['paid','unpaid','pending','in_progress','processing','delivering','finished','cancelled'];
    foreach ($mc[1] as $i => $v) { $counts[$keys[$i] ?? $i] = (int)$v; }
}

// find first mutable order row
$target = null;
if (preg_match_all('/<tr[^>]*class="order-row"[^>]*data-order-id="(\d+)"[^>]*data-order-status="([^"]*)"[^>]*>/i', $panelHtml, $rows)) {
    for ($i=0;$i<count($rows[1]);$i++) {
        $id = $rows[1][$i]; $status = $rows[2][$i];
        if (!in_array($status, ['finished','cancelled'])) { $target = ['id'=>$id,'status'=>$status]; break; }
    }
}

function postUpdate($base, $cookie, $orderId, $status, $csrfName = null, $csrfValue = null) {
    $url = $base . '/admin/orders/update-status/' . $orderId;
    $post = ['status'=>$status];
    if ($csrfName) $post[$csrfName] = $csrfValue;
    $r = http($url, $post, $cookie, ['X-Requested-With: XMLHttpRequest','Accept: application/json']);
    // try decode json
    $json = json_decode($r['body'], true);
    return ['info'=>$r['info'], 'body'=>$r['body'], 'json'=>$json];
}

$out = ['csrf'=>['name'=>$csrfName,'value'=>$csrfValue], 'login_response_code'=>$respLogin['info']['http_code'], 'initial_counts'=>$counts, 'target'=>$target];

if ($target) {
    $out['to_processing'] = postUpdate($base, $cookie, $target['id'], 'processing', $csrfName, $csrfValue);
    // refresh panel
    $panel2 = http($base . '/admin/dashboard/panel/status', null, $cookie);
    if (preg_match_all('/<div[^>]*class="status-count-card"[\s\S]*?<strong>([0-9]+)<\/strong>/i', $panel2['body'], $mc2)) {
        $keys = ['paid','unpaid','pending','in_progress','processing','delivering','finished','cancelled'];
        $out['after_processing'] = [];
        foreach ($mc2[1] as $i => $v) { $out['after_processing'][$keys[$i] ?? $i] = (int)$v; }
    }

    $out['to_cancelled'] = postUpdate($base, $cookie, $target['id'], 'cancelled', $csrfName, $csrfValue);
    $panel3 = http($base . '/admin/dashboard/panel/status', null, $cookie);
    if (preg_match_all('/<div[^>]*class="status-count-card"[\s\S]*?<strong>([0-9]+)<\/strong>/i', $panel3['body'], $mc3)) {
        $keys = ['paid','unpaid','pending','in_progress','processing','delivering','finished','cancelled'];
        $out['after_cancelled'] = [];
        foreach ($mc3[1] as $i => $v) { $out['after_cancelled'][$keys[$i] ?? $i] = (int)$v; }
    }

    $out['cancelled_to_finished'] = postUpdate($base, $cookie, $target['id'], 'finished', $csrfName, $csrfValue);
    $panel4 = http($base . '/admin/dashboard/panel/status', null, $cookie);
    if (preg_match_all('/<div[^>]*class="status-count-card"[\s\S]*?<strong>([0-9]+)<\/strong>/i', $panel4['body'], $mc4)) {
        $keys = ['paid','unpaid','pending','in_progress','processing','delivering','finished','cancelled'];
        $out['after_final_attempt'] = [];
        foreach ($mc4[1] as $i => $v) { $out['after_final_attempt'][$keys[$i] ?? $i] = (int)$v; }
    }
}

file_put_contents(__DIR__ . '/reproduce_status_output.json', json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo "Wrote tools/reproduce_status_output.json\n";
