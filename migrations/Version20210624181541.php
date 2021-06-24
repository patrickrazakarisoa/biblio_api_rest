<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210624181541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livre ADD dispo INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pret RENAME INDEX idx_52ece979a76ed395 TO IDX_52ECE97925F06C53');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livre DROP dispo');
        $this->addSql('ALTER TABLE pret RENAME INDEX idx_52ece97925f06c53 TO IDX_52ECE979A76ED395');
    }
}
