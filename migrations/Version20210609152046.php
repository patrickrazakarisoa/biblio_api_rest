<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210609152046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE97925F06C53');
        $this->addSql('DROP INDEX IDX_52ECE97925F06C53 ON pret');
        $this->addSql('ALTER TABLE pret CHANGE adherent_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE979A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_52ECE979A76ED395 ON pret (user_id)');
        $this->addSql('ALTER TABLE user ADD roles TINYTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE979A76ED395');
        $this->addSql('DROP INDEX IDX_52ECE979A76ED395 ON pret');
        $this->addSql('ALTER TABLE pret CHANGE user_id adherent_id INT NOT NULL');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE97925F06C53 FOREIGN KEY (adherent_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_52ECE97925F06C53 ON pret (adherent_id)');
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
