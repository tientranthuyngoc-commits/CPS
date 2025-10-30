<?php
// Import a subset of MEN SQL dump into bai01 SQLite (products only)
// Usage (CLI): php tools/import_from_men_sql.php "D:\\xam\\htdocs\\men\\men\\sql\\trainingdb (1).sql"

declare(strict_types=1);

require __DIR__ . '/../src/Database.php';

use App\Database;

function readFileUtf8(string $path): string {
    if (!is_readable($path)) {
        throw new RuntimeException("Cannot read SQL file: $path");
    }
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Failed to load SQL file: $path");
    }
    return $content;
}

function extractHangHoaTuples(string $sql): array {
    // Match INSERT INTO `hanghoa` (col list) VALUES (....);
    $pattern = '/INSERT\s+INTO\s+`hanghoa`\s*\(([^\)]*)\)\s*VALUES\s*(.*?);/ims';
    preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER);
    $rows = [];
    foreach ($matches as $m) {
        $cols = array_map(fn($s)=>trim(str_replace('`','',$s)), explode(',', $m[1]));
        $valuesBlob = trim($m[2]);
        // Split top-level tuples: (...) , (...)
        $tuples = [];
        $depth = 0; $buf='';
        for ($i=0; $i<strlen($valuesBlob); $i++) {
            $ch = $valuesBlob[$i];
            if ($ch === '(') { if ($depth++>0) $buf.=$ch; else $buf=''; }
            elseif ($ch === ')') { if (--$depth===0) { $tuples[]=$buf; } else $buf.=$ch; }
            else { if ($depth>0) $buf.=$ch; }
        }
        foreach ($tuples as $tuple) {
            // Split values by commas not inside quotes
            $vals = [];
            $cur=''; $inStr=false; $esc=false;
            $len = strlen($tuple);
            for ($i=0; $i<$len; $i++) {
                $c = $tuple[$i];
                if ($inStr) {
                    if ($esc) { $cur.=$c; $esc=false; continue; }
                    if ($c === "\\") { $esc=true; $cur.=$c; continue; }
                    if ($c === "'") { $inStr=false; $cur.=$c; continue; }
                    $cur.=$c; continue;
                }
                if ($c === "'") { $inStr=true; $cur.=$c; continue; }
                if ($c === ',') { $vals[] = trim($cur); $cur=''; continue; }
                $cur.=$c;
            }
            if ($cur !== '') $vals[] = trim($cur);

            // Map into assoc by columns
            $row = [];
            foreach ($cols as $idx => $col) {
                $row[$col] = $vals[$idx] ?? null;
            }
            $rows[] = $row;
        }
    }
    return $rows;
}

function extractLoaiHangTuples(string $sql): array {
    $pattern = '/INSERT\s+INTO\s+`loaihang`\s*\(([^\)]*)\)\s*VALUES\s*(.*?);/ims';
    preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER);
    $rows = [];
    foreach ($matches as $m) {
        $cols = array_map(fn($s)=>trim(str_replace('`','',$s)), explode(',', $m[1]));
        $valuesBlob = trim($m[2]);
        $tuples = [];
        $depth = 0; $buf='';
        for ($i=0; $i<strlen($valuesBlob); $i++) {
            $ch = $valuesBlob[$i];
            if ($ch === '(') { if ($depth++>0) $buf.=$ch; else $buf=''; }
            elseif ($ch === ')') { if (--$depth===0) { $tuples[]=$buf; } else $buf.=$ch; }
            else { if ($depth>0) $buf.=$ch; }
        }
        foreach ($tuples as $tuple) {
            $vals = [];
            $cur=''; $inStr=false; $esc=false; $len=strlen($tuple);
            for ($i=0; $i<$len; $i++) {
                $c = $tuple[$i];
                if ($inStr) {
                    if ($esc) { $cur.=$c; $esc=false; continue; }
                    if ($c === "\\") { $esc=true; $cur.=$c; continue; }
                    if ($c === "'") { $inStr=false; $cur.=$c; continue; }
                    $cur.=$c; continue;
                }
                if ($c === "'") { $inStr=true; $cur.=$c; continue; }
                if ($c === ',') { $vals[] = trim($cur); $cur=''; continue; }
                $cur.=$c;
            }
            if ($cur !== '') $vals[] = trim($cur);
            $row = [];
            foreach ($cols as $idx => $col) { $row[$col] = $vals[$idx] ?? null; }
            $rows[] = $row;
        }
    }
    return $rows;
}

function unquote(?string $val): ?string {
    if ($val === null) return null;
    $val = trim($val);
    if ($val === 'NULL' || $val === '') return null;
    if ($val[0] === "'" && substr($val,-1) === "'") {
        $inner = substr($val,1,-1);
        // Unescape common sequences
        $inner = str_replace(["\\'","\\n","\\r","\\t","\\\\"],["'","\n","\r","\t","\\"], $inner);
        return $inner;
    }
    return $val;
}

function toIntPrice(?string $val): int {
    $v = unquote($val);
    if ($v === null || $v === '') return 0;
    return (int)round((float)$v);
}

// Entry
$sqlPath = $argv[1] ?? 'D:\\xam\\htdocs\\men\\men\\sql\\trainingdb (1).sql';
$sql = readFileUtf8($sqlPath);
$tuples = extractHangHoaTuples($sql);
$loai = extractLoaiHangTuples($sql);

$pdo = Database::getInstance()->pdo();
$pdo->beginTransaction();

// Optional: clear products safely (respect foreign keys)
try {
    // Remove dependent rows first
    $pdo->exec('DELETE FROM order_items');
    $pdo->exec('DELETE FROM products');
} catch (Throwable $e) {
    // As a fallback, temporarily disable FK checks in SQLite
    $pdo->exec('PRAGMA foreign_keys=OFF');
    $pdo->exec('DELETE FROM order_items');
    $pdo->exec('DELETE FROM products');
    $pdo->exec('PRAGMA foreign_keys=ON');
}

$imagesDir = __DIR__ . '/../public/assets/images';
if (!is_dir($imagesDir)) @mkdir($imagesDir, 0775, true);

$stmt = $pdo->prepare('INSERT INTO products (name, description, price, image) VALUES (:name, :description, :price, :image)');
$stmtCat = $pdo->prepare('INSERT OR IGNORE INTO categories (name) VALUES (:name)');
$stmtLink = $pdo->prepare('INSERT OR IGNORE INTO product_categories (product_id, category_id) VALUES (:pid, :cid)');
$count = 0;
$catMap = [];
foreach ($loai as $lh) {
    $id = (int)(unquote($lh['idloaihang'] ?? '0') ?? 0);
    $name = unquote($lh['tenloaihang'] ?? '') ?? '';
    if ($name === '' || $id === 0) continue;
    $stmtCat->execute([':name'=>$name]);
    $cid = (int)Database::getInstance()->pdo()->lastInsertId();
    if ($cid === 0) { // already exists, fetch id
        $cidStmt = $pdo->prepare('SELECT id FROM categories WHERE name = :n');
        $cidStmt->execute([':n'=>$name]);
        $cid = (int)$cidStmt->fetchColumn();
    }
    $catMap[$id] = $cid;
}

$idMap = [];
foreach ($tuples as $t) {
    $name = unquote($t['tenhanghoa'] ?? null) ?? '';
    if ($name === '') continue;
    $desc = unquote($t['mota'] ?? null) ?? '';
    $price = toIntPrice($t['giathamkhao'] ?? null);
    // Try to decode base64 image if present in MEN dump
    $imgRel = 'assets/images/placeholder.svg';
    $b64 = $t['hinhanh'] ?? null;
    $b64 = unquote($b64);
    if ($b64 && strlen($b64) > 100) { // rudimentary check
        $bin = base64_decode($b64, true);
        if ($bin !== false) {
            $fileName = 'men_' . substr(md5($name . $price . strlen($b64)), 0, 12) . '.jpg';
            $out = $imagesDir . '/' . $fileName;
            file_put_contents($out, $bin);
            $imgRel = 'assets/images/' . $fileName;
        }
    }
    $stmt->execute([':name'=>$name, ':description'=>$desc, ':price'=>$price, ':image'=>$imgRel]);
    $newPid = (int)$pdo->lastInsertId();
    $srcId = (int)(unquote($t['idhanghoa'] ?? '0') ?? 0);
    if ($srcId > 0 && $newPid > 0) $idMap[$srcId] = $newPid;

    // link to category if exists
    $srcCatId = (int)(unquote($t['idloaihang'] ?? '0') ?? 0);
    if ($srcCatId > 0 && isset($catMap[$srcCatId]) && $newPid > 0) {
        $stmtLink->execute([':pid'=>$newPid, ':cid'=>$catMap[$srcCatId]]);
    }
    $count++;
}

$pdo->commit();

echo "Imported $count products from MEN SQL into bai01 SQLite.\n";
