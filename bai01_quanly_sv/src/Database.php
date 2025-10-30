<?php
namespace App;

use PDO;

class Database
{
    private static ?Database $instance = null;
    private PDO $conn;

    private function __construct()
    {
        $dataDir = __DIR__ . '/../data';
        if (!is_dir($dataDir)) @mkdir($dataDir, 0775, true);

        $dsn = 'sqlite:' . $dataDir . '/database.sqlite';
        $this->conn = new PDO($dsn);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $schema = __DIR__ . '/../data/schema.sql';
        if (is_readable($schema)) {
            $sql = file_get_contents($schema);
            if ($sql !== false && trim($sql) !== '') {
                $this->conn->exec($sql);
            }
        }
        $seed = __DIR__ . '/../data/seed.sql';
        if (is_readable($seed)) {
            // Seed once: naive check for existing products
            $count = (int)$this->conn->query('SELECT COUNT(*) FROM products')->fetchColumn();
            if ($count === 0) {
                $this->conn->exec(file_get_contents($seed));
            }
        }

        // Seed default admin user if none exists
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE NOT NULL, password_hash TEXT NOT NULL, role TEXT DEFAULT 'admin', email TEXT, phone TEXT, avatar TEXT, is_active INTEGER DEFAULT 1, email_verified_at TEXT, reset_token TEXT, reset_expires TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
            // Ensure orders columns for shipping/payment tracking
            $colsO = $this->conn->query('PRAGMA table_info(orders)')->fetchAll(PDO::FETCH_ASSOC);
            $onames = array_column($colsO, 'name');
            $ensureO = function(string $sql){ try{ $this->conn->exec($sql);} catch(\Throwable $e){} };
            if (!in_array('payment_status',$onames)) $ensureO("ALTER TABLE orders ADD COLUMN payment_status TEXT DEFAULT 'unpaid'");
            if (!in_array('payment_method',$onames)) $ensureO("ALTER TABLE orders ADD COLUMN payment_method TEXT");
            if (!in_array('shipping_method',$onames)) $ensureO("ALTER TABLE orders ADD COLUMN shipping_method TEXT");
            if (!in_array('shipping_fee',$onames)) $ensureO("ALTER TABLE orders ADD COLUMN shipping_fee INTEGER DEFAULT 0");
            if (!in_array('tax',$onames)) $ensureO("ALTER TABLE orders ADD COLUMN tax INTEGER DEFAULT 0");
            if (!in_array('discount_total',$onames)) $ensureO("ALTER TABLE orders ADD COLUMN discount_total INTEGER DEFAULT 0");
            // Shipping zones table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS shipping_zones (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, fee INTEGER NOT NULL DEFAULT 30000, created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
            // Ensure product new columns & brands table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS brands (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT UNIQUE NOT NULL, slug TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
            $colsP = $this->conn->query('PRAGMA table_info(products)')->fetchAll(PDO::FETCH_ASSOC);
            $pnames = array_column($colsP, 'name');
            $ensure = function(string $sql){ try{ $this->conn->exec($sql);} catch(\Throwable $e){} };
            if (!in_array('sku',$pnames)) $ensure("ALTER TABLE products ADD COLUMN sku TEXT");
            if (!in_array('stock',$pnames)) $ensure("ALTER TABLE products ADD COLUMN stock INTEGER DEFAULT 0");
            if (!in_array('status',$pnames)) $ensure("ALTER TABLE products ADD COLUMN status TEXT DEFAULT 'active'");
            if (!in_array('brand_id',$pnames)) $ensure("ALTER TABLE products ADD COLUMN brand_id INTEGER REFERENCES brands(id)");
            if (!in_array('tax_category_id',$pnames)) $ensure("ALTER TABLE products ADD COLUMN tax_category_id INTEGER");
            // Ensure new columns exist (idempotent)
            $cols = $this->conn->query('PRAGMA table_info(users)')->fetchAll(PDO::FETCH_ASSOC);
            $names = array_column($cols, 'name');
            $ensure = function(string $sql) { try { $this->conn->exec($sql); } catch (\Throwable $e) { /* ignore */ } };
            if (!in_array('email', $names)) $ensure("ALTER TABLE users ADD COLUMN email TEXT");
            if (!in_array('phone', $names)) $ensure("ALTER TABLE users ADD COLUMN phone TEXT");
            if (!in_array('avatar', $names)) $ensure("ALTER TABLE users ADD COLUMN avatar TEXT");
            if (!in_array('is_active', $names)) $ensure("ALTER TABLE users ADD COLUMN is_active INTEGER DEFAULT 1");
            if (!in_array('email_verified_at', $names)) $ensure("ALTER TABLE users ADD COLUMN email_verified_at TEXT");
            if (!in_array('reset_token', $names)) $ensure("ALTER TABLE users ADD COLUMN reset_token TEXT");
            if (!in_array('reset_expires', $names)) $ensure("ALTER TABLE users ADD COLUMN reset_expires TEXT");
            if (!in_array('provider', $names)) $ensure("ALTER TABLE users ADD COLUMN provider TEXT");
            if (!in_array('provider_uid', $names)) $ensure("ALTER TABLE users ADD COLUMN provider_uid TEXT");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS addresses (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE, name TEXT, phone TEXT, address_line TEXT NOT NULL, ward TEXT, district TEXT, province TEXT, is_default INTEGER DEFAULT 0, created_at TEXT DEFAULT CURRENT_TIMESTAMP)");

            // Orders and order_items extra tax columns
            $colsO2 = $this->conn->query('PRAGMA table_info(orders)')->fetchAll(PDO::FETCH_ASSOC);
            $on2 = array_column($colsO2, 'name');
            if (!in_array('tax_total', $on2)) $ensureO("ALTER TABLE orders ADD COLUMN tax_total INTEGER DEFAULT 0");
            if (!in_array('tax_inclusive', $on2)) $ensureO("ALTER TABLE orders ADD COLUMN tax_inclusive INTEGER DEFAULT 0");
            $colsOI = $this->conn->query('PRAGMA table_info(order_items)')->fetchAll(PDO::FETCH_ASSOC);
            $oin = array_column($colsOI, 'name');
            $ensureOI = function(string $sql){ try{ $this->conn->exec($sql);} catch(\Throwable $e){} };
            if (!in_array('tax_amount',$oin)) $ensureOI("ALTER TABLE order_items ADD COLUMN tax_amount INTEGER DEFAULT 0");
            if (!in_array('tax_rate',$oin)) $ensureOI("ALTER TABLE order_items ADD COLUMN tax_rate REAL DEFAULT 0");
            if (!in_array('tax_code',$oin)) $ensureOI("ALTER TABLE order_items ADD COLUMN tax_code TEXT");
            if (!in_array('tax_inclusive',$oin)) $ensureOI("ALTER TABLE order_items ADD COLUMN tax_inclusive INTEGER DEFAULT 0");

            // Tax master data tables (minimal to start)
            $this->conn->exec("CREATE TABLE IF NOT EXISTS tax_rates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT UNIQUE,
                name TEXT,
                rate REAL NOT NULL,
                type TEXT DEFAULT 'exclusive', -- 'exclusive' | 'inclusive'
                compound INTEGER DEFAULT 0,
                active INTEGER DEFAULT 1,
                valid_from TEXT,
                valid_to TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS tax_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT UNIQUE,
                name TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS tax_rate_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tax_rate_id INTEGER NOT NULL REFERENCES tax_rates(id) ON DELETE CASCADE,
                tax_category_id INTEGER NOT NULL REFERENCES tax_categories(id) ON DELETE CASCADE
            )");
            $this->conn->exec("CREATE TABLE IF NOT EXISTS tax_journal (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                order_item_id INTEGER,
                tax_code TEXT,
                base_amount INTEGER NOT NULL,
                tax_amount INTEGER NOT NULL,
                rate REAL NOT NULL,
                inclusive INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");

            // Seed a default VAT 10% if empty
            $trCount = (int)$this->conn->query('SELECT COUNT(*) FROM tax_rates')->fetchColumn();
            if ($trCount === 0) {
                $this->conn->exec("INSERT INTO tax_rates(code,name,rate,type,compound,active) VALUES ('VAT10','VAT 10%',0.10,'exclusive',0,1)");
            }
            $uCount = (int)$this->conn->query('SELECT COUNT(*) FROM users')->fetchColumn();
            if ($uCount === 0) {
                $hash = password_hash('admin123', PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare('INSERT INTO users (username, password_hash, role) VALUES (:u, :h, :r)');
                $stmt->execute([':u' => 'admin', ':h' => $hash, ':r' => 'admin']);
            }
        } catch (\Throwable $e) { /* ignore */ }
    }

    public static function getInstance(): Database
    {
        if (!self::$instance) self::$instance = new Database();
        return self::$instance;
    }

    public function pdo(): PDO
    {
        return $this->conn;
    }
}
