<?php
/**
 * easebuzz_webhook.php
 * - Saves raw webhook -> tbl_easebuzz_webhooks
 * - Parses fields -> updates tbl_orders (by order_id = txnid)
 * - Also saves a concise structured snapshot -> tbl_easebuzz_webhook_events
 */

declare(strict_types=1);
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '0');

require_once __DIR__ . '/../config.php'; // must define $con (mysqli)

/* ---------- Safety checks ---------- */
if (!($con instanceof mysqli) || $con->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}
if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

/* ---------- Ensure tables exist (idempotent) ---------- */
$con->query("
CREATE TABLE IF NOT EXISTS `tbl_easebuzz_webhooks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `payload` LONGTEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$con->query("
CREATE TABLE IF NOT EXISTS `tbl_easebuzz_webhook_events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` VARCHAR(64) DEFAULT NULL,
  `txnid` VARCHAR(64) DEFAULT NULL,
  `status` VARCHAR(32) DEFAULT NULL,
  `amount` DECIMAL(12,2) DEFAULT NULL,
  `payment_source` VARCHAR(64) DEFAULT NULL,
  `mode` VARCHAR(32) DEFAULT NULL,
  `bank_ref_num` VARCHAR(100) DEFAULT NULL,
  `easepayid` VARCHAR(64) DEFAULT NULL,
  `net_amount_debit` DECIMAL(12,2) DEFAULT NULL,
  `payload` LONGTEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY (`order_id`),
  KEY (`txnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* ---------- Read raw body & store raw ---------- */
$raw = file_get_contents('php://input');
if ($raw === false) $raw = '';
if ($raw === '' && !empty($_POST)) {
    // Some gateways post form-encoded without raw stream
    $raw = http_build_query($_POST);
}

$insRaw = $con->prepare("INSERT INTO `tbl_easebuzz_webhooks` (`payload`, `created_at`) VALUES (?, NOW())");
$payloadToSave = $raw;
$insRaw->bind_param('s', $payloadToSave);
$insRaw->execute();
$insRaw->close();

/* ---------- Parse key=value&... into array ---------- */
$kv = [];
if ($raw !== '') {
    // Typical Easebuzz webhook/return is x-www-form-urlencoded
    parse_str($raw, $kv);
}
// If Content-Type was JSON (rare for this), try to decode
if (!$kv && isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'json') !== false) {
    $json = json_decode($raw, true);
    if (is_array($json)) $kv = $json;
}

/* ---------- Basic fields we need ---------- */
$txnid           = trim((string)($kv['txnid'] ?? ''));
$status          = trim((string)($kv['status'] ?? ''));
$amount          = (float)($kv['amount'] ?? 0);
$payment_source  = trim((string)($kv['payment_source'] ?? ''));
$mode            = trim((string)($kv['mode'] ?? ''));
$bank_ref_num    = trim((string)($kv['bank_ref_num'] ?? ''));
$easepayid       = trim((string)($kv['easepayid'] ?? ''));
$net_amount_debit= (float)($kv['net_amount_debit'] ?? 0);

/* ---------- Save concise structured snapshot ---------- */
$insEvt = $con->prepare("
  INSERT INTO `tbl_easebuzz_webhook_events`
  (`order_id`,`txnid`,`status`,`amount`,`payment_source`,`mode`,`bank_ref_num`,`easepayid`,`net_amount_debit`,`payload`,`created_at`)
  VALUES (?,?,?,?,?,?,?,?,?, ?, NOW())
");
$order_id_for_evt = $txnid; // order_id equals txnid in your flow
$payloadJson = json_encode($kv, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
$insEvt->bind_param(
    'sssdsdssss',
    $order_id_for_evt,
    $txnid,
    $status,
    $amount,
    $payment_source,
    $mode,
    $bank_ref_num,
    $easepayid,
    $net_amount_debit,
    $payloadJson
);
$insEvt->execute();
$insEvt->close();

/* ---------- Build UPDATE for tbl_orders ---------- */
/*
Mapping based on your schema:
- payment_status         <- status
- bank_ref_no            <- bank_ref_num
- pg_txnid               <- txnid
- pg_amount              <- amount
- pg_status              <- status
- pg_error_message       <- error_Message
- pg_payment_source      <- payment_source
- pg_mode                <- mode
- pg_addedon             <- addedon
- pg_easepayid           <- easepayid
- pg_net_amount_debit    <- net_amount_debit
- pg_cash_back_percentage<- cash_back_percentage
- pg_deduction_percentage<- deduction_percentage
- pg_card_category       <- cardCategory
- pg_unmappedstatus      <- unmappedstatus
- pg_cardnum             <- cardnum
- pg_upi_va              <- upi_va
- pg_card_type           <- card_type
- pg_bankcode            <- bankcode
- pg_name_on_card        <- name_on_card
- pg_bank_name           <- bank_name
- pg_issuing_bank        <- issuing_bank
- pg_pg_type             <- PG_TYPE
- pg_auth_code           <- auth_code
- pg_auth_ref_num        <- auth_ref_num
- pg_email               <- email
- pg_firstname           <- firstname
- pg_productinfo         <- productinfo
- pg_key                 <- `key`
- pg_merchant_logo       <- merchant_logo
- pg_surl                <- surl
- pg_furl                <- furl
- pg_udf1..pg_udf10      <- udf1..udf10
- pg_hash                <- hash
*/
$map = [
  'payment_status'           => $kv['status']            ?? null,
  'bank_ref_no'              => $kv['bank_ref_num']      ?? null,
  'pg_txnid'                 => $kv['txnid']             ?? null,
  'pg_amount'                => $kv['amount']            ?? null,
  'pg_status'                => $kv['status']            ?? null,
  'pg_error_message'         => $kv['error_Message']     ?? ($kv['error'] ?? null),
  'pg_payment_source'        => $kv['payment_source']    ?? null,
  'pg_mode'                  => $kv['mode']              ?? null,
  'pg_addedon'               => $kv['addedon']           ?? null,
  'pg_easepayid'             => $kv['easepayid']         ?? null,
  'pg_net_amount_debit'      => $kv['net_amount_debit']  ?? null,
  'pg_cash_back_percentage'  => $kv['cash_back_percentage'] ?? null,
  'pg_deduction_percentage'  => $kv['deduction_percentage'] ?? null,
  'pg_card_category'         => $kv['cardCategory']      ?? null,
  'pg_unmappedstatus'        => $kv['unmappedstatus']    ?? null,
  'pg_cardnum'               => $kv['cardnum']           ?? null,
  'pg_upi_va'                => $kv['upi_va']            ?? null,
  'pg_card_type'             => $kv['card_type']         ?? null,
  'pg_bankcode'              => $kv['bankcode']          ?? null,
  'pg_name_on_card'          => $kv['name_on_card']      ?? null,
  'pg_bank_name'             => $kv['bank_name']         ?? null,
  'pg_issuing_bank'          => $kv['issuing_bank']      ?? null,
  'pg_pg_type'               => $kv['PG_TYPE']           ?? null,
  'pg_auth_code'             => $kv['auth_code']         ?? null,
  'pg_auth_ref_num'          => $kv['auth_ref_num']      ?? null,
  'pg_email'                 => $kv['email']             ?? null,
  'pg_firstname'             => $kv['firstname']         ?? null,
  'pg_productinfo'           => $kv['productinfo']       ?? null,
  'pg_key'                   => $kv['key']               ?? null,
  'pg_merchant_logo'         => $kv['merchant_logo']     ?? null,
  'pg_surl'                  => $kv['surl']              ?? null,
  'pg_furl'                  => $kv['furl']              ?? null,
  'pg_udf1'                  => $kv['udf1']              ?? null,
  'pg_udf2'                  => $kv['udf2']              ?? null,
  'pg_udf3'                  => $kv['udf3']              ?? null,
  'pg_udf4'                  => $kv['udf4']              ?? null,
  'pg_udf5'                  => $kv['udf5']              ?? null,
  'pg_udf6'                  => $kv['udf6']              ?? null,
  'pg_udf7'                  => $kv['udf7']              ?? null,
  'pg_udf8'                  => $kv['udf8']              ?? null,
  'pg_udf9'                  => $kv['udf9']              ?? null,
  'pg_udf10'                 => $kv['udf10']             ?? null,
  'pg_hash'                  => $kv['hash']              ?? null,
];

if ($txnid !== '') {
    // Build dynamic UPDATE with escaping
    $sets = [];
    foreach ($map as $col => $val) {
        if ($val === null) continue;
        $sets[] = "`$col` = '" . $con->real_escape_string((string)$val) . "'";
    }
    $sets[] = "`updated_at` = NOW()";
    if ($sets) {
        $sql = "UPDATE `tbl_orders` SET " . implode(', ', $sets) .
               " WHERE `order_id` = '" . $con->real_escape_string($txnid) . "' LIMIT 1";
        $con->query($sql);
    }
}

/* ---------- Respond OK to provider ---------- */
header('Content-Type: application/json');
echo json_encode(['success' => true, 'order_id' => $txnid, 'status' => $status]);
