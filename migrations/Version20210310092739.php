<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310092739 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, carrier VARCHAR(255) NOT NULL, delivered_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery_item (id INT AUTO_INCREMENT NOT NULL, delivery_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_CE87ED8412136921 (delivery_id), INDEX IDX_CE87ED844584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery_item ADD CONSTRAINT FK_CE87ED8412136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id)');
        $this->addSql('ALTER TABLE delivery_item ADD CONSTRAINT FK_CE87ED844584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_item DROP FOREIGN KEY FK_CE87ED8412136921');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE delivery_item');
    }
}
