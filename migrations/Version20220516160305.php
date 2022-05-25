<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220516160305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rider (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E2067FF881F6');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E2067FF881F6 FOREIGN KEY (rider_id) REFERENCES rider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E2067FF881F6');
        $this->addSql('DROP TABLE rider');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E2067FF881F6');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E2067FF881F6 FOREIGN KEY (rider_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
