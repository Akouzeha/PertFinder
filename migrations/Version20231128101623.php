<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128101623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edge (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, successeur_id INT DEFAULT NULL, INDEX IDX_7506D3668DB60186 (task_id), INDEX IDX_7506D3669FD3F22B (successeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3668DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3669FD3F22B FOREIGN KEY (successeur_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3668DB60186');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3669FD3F22B');
        $this->addSql('DROP TABLE edge');
    }
}
