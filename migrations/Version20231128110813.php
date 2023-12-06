<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128110813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D3669FD3F22B');
        $this->addSql('DROP INDEX IDX_7506D3669FD3F22B ON edge');
        $this->addSql('ALTER TABLE edge CHANGE successeur_id predecessor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D36668C90015 FOREIGN KEY (predecessor_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_7506D36668C90015 ON edge (predecessor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edge DROP FOREIGN KEY FK_7506D36668C90015');
        $this->addSql('DROP INDEX IDX_7506D36668C90015 ON edge');
        $this->addSql('ALTER TABLE edge CHANGE predecessor_id successeur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE edge ADD CONSTRAINT FK_7506D3669FD3F22B FOREIGN KEY (successeur_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_7506D3669FD3F22B ON edge (successeur_id)');
    }
}
