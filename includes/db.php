<?php
require_once __DIR__ . '/config.php';

class DB {
    private static $instance = null;

    public static function conn() {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function query($sql, $params = []) {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch($sql, $params = []) {
        return self::query($sql, $params)->fetch();
    }

    public static function fetchAll($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchColumn($sql, $params = []) {
        return self::query($sql, $params)->fetchColumn();
    }

    public static function insert($table, $data) {
        $cols   = array_keys($data);
        $place  = array_map(fn($c) => ':' . $c, $cols);
        $sql    = 'INSERT INTO `' . $table . '` (' . implode(',', $cols) . ') VALUES (' . implode(',', $place) . ')';
        $stmt   = self::conn()->prepare($sql);
        $stmt->execute($data);
        return self::conn()->lastInsertId();
    }

    public static function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $c) $set[] = "`$c` = :$c";
        $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $set) . ' WHERE ' . $where;
        $stmt = self::conn()->prepare($sql);
        $stmt->execute(array_merge($data, $whereParams));
        return $stmt->rowCount();
    }

    public static function delete($table, $where, $params = []) {
        $sql = 'DELETE FROM `' . $table . '` WHERE ' . $where;
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}

/* ---------- Settings cache ---------- */
function settings() {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (DB::fetchAll('SELECT setting_key, setting_value FROM settings') as $r) {
            $cache[$r['setting_key']] = $r['setting_value'];
        }
    }
    return $cache;
}

function setting($key, $default = '') {
    $s = settings();
    return $s[$key] ?? $default;
}

function setting_save($key, $value) {
    $exists = DB::fetchColumn('SELECT 1 FROM settings WHERE setting_key = ?', [$key]);
    if ($exists) {
        DB::update('settings', ['setting_value' => $value], 'setting_key = :k', [':k' => $key]);
    } else {
        DB::insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
    }
    // invalidate static cache (process-local best-effort)
}

/* ---------- Slug helper ---------- */
function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'item';
}
