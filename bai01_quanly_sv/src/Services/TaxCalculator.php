<?php
namespace App\Services;

use App\Database;
use PDO;

class TaxCalculator
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    // Resolve a tax rate for a product tax_category (very first match), fallback VAT10
    public function resolveRate(?int $taxCategoryId): array
    {
        if ($taxCategoryId) {
            $st = $this->pdo->prepare('SELECT tr.* FROM tax_rates tr JOIN tax_rate_categories tc ON tr.id=tc.tax_rate_id WHERE tc.tax_category_id=:c AND tr.active=1 LIMIT 1');
            $st->execute([':c'=>$taxCategoryId]);
            $r = $st->fetch(PDO::FETCH_ASSOC);
            if ($r) return $r;
        }
        $r = $this->pdo->query("SELECT * FROM tax_rates WHERE code='VAT10' OR active=1 ORDER BY code='VAT10' DESC, id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        return $r ?: ['code'=>'NONE','rate'=>0,'type'=>'exclusive','compound'=>0];
    }

    // Compute tax for one line
    public function computeLine(int $price, int $qty, float $rate, string $type): array
    {
        $base = $price * $qty;
        if ($rate <= 0) return ['base'=>$base,'tax'=>0];
        if (strtolower($type)==='inclusive') {
            $tax = (int) round($base - ($base / (1+$rate)));
            return ['base'=>$base-$tax,'tax'=>$tax];
        }
        $tax = (int) round($base * $rate);
        return ['base'=>$base,'tax'=>$tax];
    }
}

