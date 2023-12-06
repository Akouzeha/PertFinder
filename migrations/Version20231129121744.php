<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129121744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D36668C90015');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3668DB60186');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D36668C90015 FOREIGN KEY (predecessor_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3668DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3668DB60186');
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D36668C90015');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3668DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D36668C90015 FOREIGN KEY (predecessor_id) REFERENCES task (id) ON DELETE CASCADE');
    }
}
