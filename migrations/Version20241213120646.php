<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213120646 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE filter (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, client_id INT DEFAULT NULL, measurement VARCHAR(255) DEFAULT NULL, level INT DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, quick_range VARCHAR(255) DEFAULT NULL, creation_date DATETIME NOT NULL, update_date DATETIME NOT NULL, INDEX IDX_7FC45F1DA76ED395 (user_id), INDEX IDX_7FC45F1D19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE filter ADD CONSTRAINT FK_7FC45F1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE filter ADD CONSTRAINT FK_7FC45F1D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE filter DROP FOREIGN KEY FK_7FC45F1DA76ED395');
        $this->addSql('ALTER TABLE filter DROP FOREIGN KEY FK_7FC45F1D19EB6921');
        $this->addSql('DROP TABLE filter');
    }
}
