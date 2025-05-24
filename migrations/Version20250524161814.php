<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250524161814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, payer_id INT NOT NULL, payee_id INT NOT NULL, amount INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_4034A3C0C17AD9A9 (payer_id), INDEX IDX_4034A3C0CB4B68F (payee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0C17AD9A9 FOREIGN KEY (payer_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0CB4B68F FOREIGN KEY (payee_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD balance INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0C17AD9A9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0CB4B68F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transfer
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP balance
        SQL);
    }
}
