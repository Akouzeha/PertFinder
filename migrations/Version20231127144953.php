<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127144953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D36646DEB8ED');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D366C469B9EE');
        $this->addSql('DROP TABLE edge');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edge (id INT AUTO_INCREMENT NOT NULL, source_task_id INT DEFAULT NULL, target_task_id INT DEFAULT NULL, type VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_7506D366C469B9EE (source_task_id), INDEX IDX_7506D36646DEB8ED (target_task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D36646DEB8ED FOREIGN KEY (target_task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D366C469B9EE FOREIGN KEY (source_task_id) REFERENCES task (id)');
    }
}
