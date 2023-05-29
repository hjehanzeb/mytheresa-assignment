<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Discount\DiscountTypeConfiguration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230528194923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pre-defined categories';
    }

    public function up(Schema $schema): void
    {
        $catDisountType = DiscountTypeConfiguration::CATEGORY_DISCOUNT_TYPE;
        $skuDiscountType = DiscountTypeConfiguration::SKU_DISCOUNT_TYPE;

        $this->addSql("INSERT INTO product_category (id, name) VALUES (1, 'boots')");
        $this->addSql("INSERT INTO product_category (id, name) VALUES (2, 'sandals')");
        $this->addSql("INSERT INTO product_category (id, name) VALUES (3, 'sneakers')");
        $this->addSql("INSERT INTO discount (id, product_category_id, discount_type, discount_percent, product_sku) VALUES (1, 1, '$catDisountType', '30', null)");
        $this->addSql("INSERT INTO discount (id, product_category_id, discount_type, discount_percent, product_sku) VALUES (2, null, '$skuDiscountType', '15', '000003')");

        // Adding some sample products
        $this->addSql("INSERT INTO product (id, discount_id, product_category_id, name, sku, price) VALUES (1, null, 1, 'BV Lean leather ankle boots', '000001', 89000)");
        $this->addSql("INSERT INTO product (id, discount_id, product_category_id, name, sku, price) VALUES (2, null, 1, 'BV Lean leather ankle boots', '000002', 99000)");
        $this->addSql("INSERT INTO product (id, discount_id, product_category_id, name, sku, price) VALUES (3, null, 1, 'Ashlington leather ankle boots', '000003', 71000)");
        $this->addSql("INSERT INTO product (id, discount_id, product_category_id, name, sku, price) VALUES (4, null, 2, 'Naima embellished suede sandals', '000004', 79500)");
        $this->addSql("INSERT INTO product (id, discount_id, product_category_id, name, sku, price) VALUES (5, null, 3, 'Nathane leather sneakers', '000005', 59000)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM product_category WHERE id = 1");
        $this->addSql("DELETE FROM discount WHERE id IN (1, 2)");
        $this->addSql("DELETE FROM product WHERE id IN (1, 2, 3, 4, 5)");
    }
}
